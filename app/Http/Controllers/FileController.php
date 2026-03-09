<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileShare;
use App\Services\FileEncryptionService;
use App\Services\OtpService;
use App\Services\ActivityLogger;
use App\Services\BrevoEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FileController extends Controller
{
    protected $encryptionService;
    protected $otpService;
    protected $emailService;

    public function __construct(
        FileEncryptionService $encryptionService,
        OtpService $otpService,
        BrevoEmailService $emailService
    ) {
        $this->encryptionService = $encryptionService;
        $this->otpService = $otpService;
        $this->emailService = $emailService;
    }

    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin() || $user->role->slug === 'admin') {
            $files = File::with(['owner', 'department'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            $files = File::where('owner_id', $user->id)
                ->orWhere('department_id', $user->department_id)
                ->with(['owner', 'department'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return view('files.index', compact('files'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if (!$user->hasPermission('files.upload') && !$user->isSender()) {
            abort(403, 'Unauthorized action.');
        }

        return view('files.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasPermission('files.upload') && !$user->isSender()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:51200', // 50 MB
            'description' => 'nullable|string|max:500',
            'tags' => 'nullable|string',
            'expires_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        $extension = $file->getClientOriginalExtension();

        // Validate file type
        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/zip',
            'application/x-zip-compressed',
        ];

        if (!in_array($mimeType, $allowedTypes)) {
            return redirect()->back()->with('error', 'File type not allowed.');
        }

        // Store file temporarily
        $tempPath = $file->store('temp', 'local');

        // Encrypt file
        $encryptionResult = $this->encryptionService->encryptFile($tempPath, $originalName);

        if (!$encryptionResult['success']) {
            return redirect()->back()->with('error', 'Failed to encrypt file: ' . $encryptionResult['error']);
        }

        // Create file record
        $fileRecord = File::create([
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
            'permissions' => json_encode(['view', 'download']),
            'description' => $request->description,
            'tags' => $request->tags ? json_encode(explode(',', $request->tags)) : null,
            'expires_at' => $request->expires_at,
        ]);

        // Log activity
        ActivityLogger::log('file_upload', "Uploaded file: {$originalName}", $user->id, $fileRecord->id);

        return redirect()->route('files.index')->with('success', 'File uploaded successfully.');
    }

    public function show(File $file)
    {
        $user = Auth::user();

        // Check access
        if (!$file->canBeAccessedBy($user)) {
            abort(403, 'Access denied.');
        }

        // Check if file is archived
        if ($file->isArchived()) {
            return redirect()->route('files.index')->with('error', 'File is archived.');
        }

        // Check if file is expired
        if ($file->isExpired()) {
            return redirect()->route('files.index')->with('error', 'File has expired.');
        }

        // Log view activity
        ActivityLogger::log('file_view', "Viewed file: {$file->original_name}", $user->id, $file->id);

        return view('files.show', compact('file'));
    }

    public function preview(File $file)
    {
        $user = Auth::user();

        // Check access
        if (!$file->canBeAccessedBy($user)) {
            abort(403, 'Access denied.');
        }

        // Generate preview
        $previewPath = $this->encryptionService->generatePreview($file->file_path, $file->mime_type);

        if (!$previewPath) {
            return redirect()->back()->with('error', 'Preview not available for this file type.');
        }

        // Log preview activity
        ActivityLogger::log('file_view', "Previewed file: {$file->original_name}", $user->id, $file->id);

        return response()->file($previewPath);
    }

    public function download(File $file)
    {
        $user = Auth::user();

        // Check access
        if (!$file->canBeAccessedBy($user)) {
            abort(403, 'Access denied.');
        }

        // Check if OTP is required for download
        if ($user->id !== $file->owner_id) {
            session(['otp_required_file_access' => true]);
            
            // Store intended action
            session(['download_file_id' => $file->id]);
            
            return redirect()->route('otp.verify', ['purpose' => 'file_access'])
                ->with('warning', 'OTP required for downloading this file.');
        }

        // Decrypt file
        $result = $this->encryptionService->decryptFile($file->file_path);

        if (!$result['success']) {
            return redirect()->back()->with('error', 'Failed to decrypt file.');
        }

        // Log download activity
        ActivityLogger::log('file_download', "Downloaded file: {$file->original_name}", $user->id, $file->id);

        // Send email alert to owner
        if ($user->id !== $file->owner_id) {
            $owner = $file->owner;
            $this->emailService->sendAlert(
                $owner->email,
                'File Downloaded',
                "File '{$file->original_name}' was downloaded by {$user->name} ({$user->email})",
                [
                    'file_name' => $file->original_name,
                    'downloaded_by' => $user->name,
                    'downloaded_at' => now()->format('Y-m-d H:i:s'),
                    'ip_address' => request()->ip(),
                ]
            );
        }

        // Return download response
        return response()->streamDownload(function () use ($result) {
            echo $result['content'];
        }, $file->original_name, [
            'Content-Type' => $file->mime_type,
        ]);
    }

    public function archive(File $file)
    {
        $user = Auth::user();

        // Only owner can archive
        if ($file->owner_id !== $user->id) {
            abort(403, 'Only the file owner can archive files.');
        }

        $file->update([
            'is_archived' => true,
            'archived_at' => now(),
        ]);

        // Log activity
        ActivityLogger::log('file_delete', "Archived file: {$file->original_name}", $user->id, $file->id);

        // Revoke all active shares
        $file->shares()->update(['is_active' => false]);

        return redirect()->route('files.index')->with('success', 'File archived successfully.');
    }

    public function restore(File $file)
    {
        $user = Auth::user();

        // Only owner can restore
        if ($file->owner_id !== $user->id) {
            abort(403, 'Only the file owner can restore files.');
        }

        $file->update([
            'is_archived' => false,
            'archived_at' => null,
        ]);

        // Log activity
        ActivityLogger::log('file_edit', "Restored file: {$file->original_name}", $user->id, $file->id);

        return redirect()->route('files.index')->with('success', 'File restored successfully.');
    }

   public function shares(File $file)
    {
        $user = Auth::user();

        // Only owner can view shares
        if ($file->owner_id !== $user->id) {
            abort(403, 'Only the file owner can view shares.');
        }

        $shares = $file->shares()->with('sharedWith')->orderBy('created_at', 'desc')->get();

        return view('files.shares', compact('file', 'shares'));
    }

    public function createShare(File $file)
    {
        $user = Auth::user();

        // Only owner can share
        if ($file->owner_id !== $user->id) {
            abort(403, 'Only the file owner can share files.');
        }

        // Get users in same department (excluding owner)
        $departmentUsers = \App\Models\User::where('department_id', $user->department_id)
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->get();

        return view('files.create-share', compact('file', 'departmentUsers'));
    }


    public function storeShare(Request $request, File $file)
    {
        $user = Auth::user();

        // Only owner can share
        if ($file->owner_id !== $user->id) {
            abort(403, 'Only the file owner can share files.');
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create share
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

        // Update file sharing status
        $file->update(['is_shared' => true]);

        // Log activity
        ActivityLogger::log('file_share', "Shared file: {$file->original_name}", $user->id, $file->id);

        // Send email notification
        if ($request->shared_email) {
            $shareUrl = route('shared.show', ['token' => $share->share_token]);
            
            $this->emailService->sendAlert(
                $request->shared_email,
                'File Shared With You',
                "A file has been shared with you. Click <a href='{$shareUrl}'>here</a> to access it.",
                [
                    'file_name' => $file->original_name,
                    'shared_by' => $user->name,
                    'valid_until' => $share->valid_until->format('Y-m-d H:i:s'),
                    'permissions' => implode(', ', $request->permissions),
                ]
            );
        }

        return redirect()->route('files.shares', $file)
            ->with('success', 'File shared successfully.');
    }

    public function revokeShare(FileShare $share)
    {
        $user = Auth::user();

        // Only share creator or file owner can revoke
        if ($share->shared_by !== $user->id && $share->file->owner_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $share->update(['is_active' => false]);

        // Log activity
        ActivityLogger::log('share_revoke', "Revoked share for file: {$share->file->original_name}", $user->id, $share->file_id);

        return redirect()->back()->with('success', 'Share revoked successfully.');
    }

    public function sharedShow($token)
    {
        $share = FileShare::where('share_token', $token)->firstOrFail();
        $file = $share->file;

        // Check if share is valid
        if (!$share->isValid()) {
            return view('shared.expired')->with('error', 'This share link has expired or been revoked.');
        }

        // Check if OTP approval is required
        if ($share->requires_otp_approval && !session("otp_verified_file_access")) {
            session(['shared_token' => $token]);
            return redirect()->route('shared.otp', $token)
                ->with('warning', 'OTP approval required to access this file.');
        }

        // Increment access count
        $share->incrementAccessCount();

        // Log activity
        ActivityLogger::log('share_access', "Accessed shared file: {$file->original_name}", null, $file->id);

        return view('shared.show', compact('file', 'share'));
    }

    public function sharedOtp($token)
    {
        $share = FileShare::where('share_token', $token)->firstOrFail();

        if (!$share->requires_otp_approval) {
            return redirect()->route('shared.show', $token);
        }

        return view('shared.otp', compact('share'));
    }

   public function verifySharedOtp(Request $request, $token)
{
    // Ensure otp is an array of 6 digits
    $request->validate([
        'otp'   => 'required|array|size:6',
        'otp.*' => 'required|digits:1',
    ]);

    // Convert array → string (e.g. 123456)
    $otp = implode('', $request->otp);

    $share = FileShare::where('share_token', $token)->firstOrFail();
    $owner = $share->file->owner;

    // Verify OTP
    $result = $this->otpService->verifyOTP(
        $owner->email,
        $otp,
        'file_access'
    );

    if (!$result['success']) {
        return back()->with('error', $result['message']);
    }

    // Mark OTP verified
    session(['otp_verified_file_access' => true]);

    // Email alert
    $this->emailService->sendAlert(
        $owner->email,
        'Shared File Accessed',
        "Your shared file '{$share->file->original_name}' was accessed.",
        [
            'file_name'   => $share->file->original_name,
            'accessed_at' => now()->format('Y-m-d H:i:s'),
            'share_token' => $token,
        ]
    );

    return redirect()
        ->route('shared.show', $token)
        ->with('success', 'OTP verified. Access granted.');
}


    public function requestOtpForShared($token)
    {
        $share = FileShare::where('share_token', $token)->firstOrFail();
        $owner = $share->file->owner;

        // Send OTP to owner
        $result = $this->otpService->generateAndSendOTP($owner->email, 'file_access', $owner->id, $share->file_id);

        if ($result['success']) {
            return redirect()->back()->with('success', 'OTP sent to file owner for approval.');
        }

        return redirect()->back()->with('error', 'Failed to send OTP.');
    }
}