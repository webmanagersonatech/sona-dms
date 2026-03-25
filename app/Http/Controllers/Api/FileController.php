<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\FileShare;
use App\Services\FileEncryptionService;
use App\Services\OtpService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    protected $encryptionService;
    protected $otpService;

    public function __construct(
        FileEncryptionService $encryptionService,
        OtpService $otpService
    ) {
        $this->encryptionService = $encryptionService;
        $this->otpService = $otpService;
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = File::with(['owner', 'department'])
            ->where('owner_id', $user->id)
            ->orWhere('department_id', $user->department_id);

        if ($request->has('search')) {
            $query->where('original_name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('type')) {
            $query->where('extension', $request->type);
        }

        if ($request->has('archived')) {
            $query->where('is_archived', $request->boolean('archived'));
        }

        $files = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->hasPermission('files.upload') && !$user->isSender()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:20480',
            'description' => 'nullable|string|max:500',
            'tags' => 'nullable|array',
            'expires_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $mimeType = $uploadedFile->getMimeType();
        $size = $uploadedFile->getSize();
        $extension = $uploadedFile->getClientOriginalExtension();

        // Validate file type
        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/zip',
        ];

        if (!in_array($mimeType, $allowedTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'File type not allowed.',
            ], 400);
        }

        // Store and encrypt file
        $tempPath = $uploadedFile->store('temp', 'local');
        $encryptionResult = $this->encryptionService->encryptFile($tempPath, $originalName);

        if (!$encryptionResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to encrypt file.',
            ], 500);
        }

        // Create file record
        $file = File::create([
            'owner_id' => $user->id,
            'department_id' => $user->department_id,
            'original_name' => $originalName,
            'storage_name' => pathinfo($encryptionResult['encrypted_path'], PATHINFO_FILENAME),
            'file_path' => $encryptionResult['encrypted_path'],
            'mime_type' => $mimeType,
            'size' => $size,
            'extension' => $extension,
            'encryption_status' => 'encrypted',
            'encryption_key' => $encryptionResult['encryption_key'],
            'permissions' => ['view', 'download'],
            'description' => $request->description,
            'tags' => $request->tags,
            'expires_at' => $request->expires_at,
        ]);

        ActivityLogger::log('file_upload', "Uploaded file via API: {$originalName}", $user->id, $file->id);

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully.',
            'file' => $file->load(['owner', 'department']),
        ], 201);
    }

    public function show(File $file)
    {
        $user = Auth::user();

        if (!$file->canBeAccessedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        if ($file->isArchived()) {
            return response()->json([
                'success' => false,
                'message' => 'File is archived.',
            ], 400);
        }

        if ($file->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'File has expired.',
            ], 400);
        }

        ActivityLogger::log('file_view', "Viewed file via API: {$file->original_name}", $user->id, $file->id);

        return response()->json([
            'success' => true,
            'file' => $file->load(['owner', 'department']),
        ]);
    }

    public function download(File $file)
    {
        $user = Auth::user();

        if (!$file->canBeAccessedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        // Check if OTP is required for download
        if ($user->id !== $file->owner_id) {
            // For API, we require OTP in the request
            if (!$request->has('otp')) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP required for downloading this file.',
                    'requires_otp' => true,
                ], 403);
            }

            // Verify OTP
            $result = $this->otpService->verifyOTP($user->email, $request->otp, 'file_access');
            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 401);
            }
        }

        // Decrypt file
        $result = $this->encryptionService->decryptFile($file->file_path);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to decrypt file.',
            ], 500);
        }

        ActivityLogger::log('file_download', "Downloaded file via API: {$file->original_name}", $user->id, $file->id);

        // Return file as base64 for API
        return response()->json([
            'success' => true,
            'file' => [
                'name' => $file->original_name,
                'type' => $file->mime_type,
                'size' => $file->size,
                'content' => base64_encode($result['content']),
            ],
        ]);
    }

    public function share(Request $request, File $file)
    {
        $user = Auth::user();

        if ($file->owner_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Only the file owner can share files.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'shared_with' => 'required_without:shared_email',
            'shared_email' => 'required_without:shared_with|email',
            'permissions' => 'required|array',
            'permissions.*' => 'in:view,download,edit,print',
            'valid_until' => 'required|date|after:now',
            'valid_from' => 'nullable|date|before:valid_until',
            'max_access_count' => 'nullable|integer|min:1',
            'requires_otp_approval' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $share = FileShare::create([
            'file_id' => $file->id,
            'shared_by' => $user->id,
            'shared_with' => $request->shared_with,
            'shared_email' => $request->shared_email,
            'permissions' => $request->permissions,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'max_access_count' => $request->max_access_count,
            'requires_otp_approval' => $request->boolean('requires_otp_approval'),
            'is_active' => true,
        ]);

        $file->update(['is_shared' => true]);

        ActivityLogger::log('file_share', "Shared file via API: {$file->original_name}", $user->id, $file->id);

        return response()->json([
            'success' => true,
            'message' => 'File shared successfully.',
            'share' => $share,
            'share_url' => route('shared.show', ['token' => $share->share_token]),
        ]);
    }

    public function sharedShow($token)
    {
        $share = FileShare::where('share_token', $token)->firstOrFail();
        $file = $share->file;

        if (!$share->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This share link has expired or been revoked.',
            ], 410);
        }

        // Check OTP if required
        if ($share->requires_otp_approval && !$request->has('otp')) {
            return response()->json([
                'success' => false,
                'message' => 'OTP approval required to access this file.',
                'requires_otp' => true,
            ], 403);
        }

        if ($share->requires_otp_approval && $request->has('otp')) {
            $owner = $file->owner;
            $result = $this->otpService->verifyOTP($owner->email, $request->otp, 'file_access');
            
            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 401);
            }
        }

        $share->incrementAccessCount();
        ActivityLogger::log('share_access', "Accessed shared file via API: {$file->original_name}", null, $file->id);

        return response()->json([
            'success' => true,
            'file' => [
                'id' => $file->id,
                'name' => $file->original_name,
                'type' => $file->mime_type,
                'size' => $file->size,
                'owner' => $file->owner->name,
                'permissions' => $share->permissions,
                'valid_until' => $share->valid_until,
            ],
        ]);
    }

    public function sharedPreview($token)
    {
        $share = FileShare::where('share_token', $token)->firstOrFail();
        $file = $share->file;

        if (!$share->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This share link has expired or been revoked.',
            ], 410);
        }

        $previewPath = $this->encryptionService->generatePreview($file->file_path, $file->mime_type);

        if (!$previewPath) {
            return response()->json([
                'success' => false,
                'message' => 'Preview not available for this file type.',
            ], 400);
        }

        $share->incrementAccessCount();

        return response()->json([
            'success' => true,
            'preview' => base64_encode(file_get_contents($previewPath)),
            'mime_type' => $file->mime_type,
        ]);
    }

    public function requestSharedOtp($token)
    {
        $share = FileShare::where('share_token', $token)->firstOrFail();
        $owner = $share->file->owner;

        $result = $this->otpService->generateAndSendOTP(
            $owner->email,
            'file_access',
            $owner->id,
            $share->file_id
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent to file owner for approval.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP.',
        ], 500);
    }

    public function verifySharedOtp(Request $request, $token)
    {
        $share = FileShare::where('share_token', $token)->firstOrFail();
        $owner = $share->file->owner;

        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->otpService->verifyOTP($owner->email, $request->otp, 'file_access');

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified. Access granted.',
            'access_token' => $this->generateAccessToken($share),
        ]);
    }

    protected function generateAccessToken($share)
    {
        // Generate a temporary access token for the shared file
        return hash('sha256', $share->share_token . now()->timestamp);
    }
}