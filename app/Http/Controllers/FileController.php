<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileShare;
<<<<<<< HEAD
use App\Models\ActivityLog;
use App\Models\OtpLog;
use App\Models\User;
use App\Services\BrevoService;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use App\Models\Department;

class FileController extends Controller
{
    protected $brevoService;
    protected $encryptionService;

    public function __construct(BrevoService $brevoService, EncryptionService $encryptionService)
    {
        $this->brevoService = $brevoService;
        $this->encryptionService = $encryptionService;
    }

   public function index(Request $request)
{
    $user = Auth::user();
    
    $query = File::with(['owner', 'department', 'shares' => function($q) use ($user) {
        $q->where('shared_with', $user->id);
    }]);

    // Handle filter from tabs
    if ($request->filled('filter')) {
        if ($request->filter === 'my-files') {
            // Show only files owned by the user
            $query->where('owner_id', $user->id);
        } elseif ($request->filter === 'shared-with-me') {
            // Show only files shared with the user
            $query->whereHas('shares', function($q) use ($user) {
                $q->where('shared_with', $user->id)
                  ->where('status', 'active');
            });
        }
    } else {
        // Default: show files based on user role
        if ($user->isSuperAdmin()) {
            // Super Admin sees everything
        } elseif ($user->isDepartmentAdmin()) {
            $query->where('department_id', $user->department_id);
        } else {
            // Regular user sees their own files + files shared with them
            $query->where('owner_id', $user->id)
                ->orWhereHas('shares', function($q) use ($user) {
                    $q->where('shared_with', $user->id)
                      ->where('status', 'active');
                });
        }
    }

    // Apply search filter
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('original_name', 'like', '%' . $request->search . '%');
        });
    }

    // Apply type filter
    if ($request->filled('type')) {
        if ($request->type === 'image') {
            $query->whereIn('extension', ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']);
        } elseif ($request->type === 'document') {
            $query->whereIn('extension', ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt']);
        } elseif ($request->type === 'spreadsheet') {
            $query->whereIn('extension', ['xls', 'xlsx', 'csv', 'ods']);
        } elseif ($request->type === 'presentation') {
            $query->whereIn('extension', ['ppt', 'pptx', 'odp']);
        } elseif ($request->type === 'archive') {
            $query->whereIn('extension', ['zip', 'rar', '7z', 'tar', 'gz']);
        }
    }

    $files = $query->orderBy('created_at', 'desc')->paginate(15);

    // Get stats for the stats cards
    $stats = [
        'shared_with_me' => FileShare::where('shared_with', $user->id)
            ->where('status', 'active')
            ->count()
    ];

    // Get filter data for dropdowns
    $departments = Department::where('status', 'active')->get();
    $users = User::where('status', 'active')->get();

    return view('files.index', compact('files', 'departments', 'users', 'stats'));
}

    public function create()
    {
=======
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

>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
        return view('files.create');
    }

    public function store(Request $request)
    {
<<<<<<< HEAD
        $request->validate([
            'file' => 'required|file|max:102400', // 100MB max
            'description' => 'nullable|string|max:500',
            'encrypt' => 'boolean',
        ]);

        $user = Auth::user();
        $uploadedFile = $request->file('file');

        DB::beginTransaction();
        try {
            // Generate unique filename
            $uuid = Str::uuid();
            $extension = $uploadedFile->getClientOriginalExtension();
            $filename = $uuid . '.' . $extension;
            $path = $uploadedFile->storeAs('uploads/' . date('Y/m/d'), $filename, 'private');

            $fileData = [
                'uuid' => $uuid,
                'name' => $uploadedFile->getClientOriginalName(),
                'original_name' => $uploadedFile->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $uploadedFile->getMimeType(),
                'mime_type' => $uploadedFile->getMimeType(),
                'file_size' => $uploadedFile->getSize(),
                'extension' => $extension,
                'description' => $request->description,
                'owner_id' => $user->id,
                'department_id' => $user->department_id,
                'is_encrypted' => $request->boolean('encrypt', false),
                'status' => 'active',
            ];

            // Encrypt if requested
            if ($request->boolean('encrypt')) {
                $fileContent = file_get_contents($uploadedFile->getRealPath());
                $encryptedContent = $this->encryptionService->encrypt($fileContent);
                Storage::disk('private')->put($path, $encryptedContent);
                $fileData['encryption_key'] = $this->encryptionService->getKey();
            }

            $file = File::create($fileData);

            $this->logActivity(
                $user,
                $request,
                'upload',
                'file',
                'Uploaded file: ' . $file->name,
                null,
                $file->id
            );

            DB::commit();

            return redirect()->route('files.show', $file)
                ->with('success', 'File uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to upload file: ' . $e->getMessage()]);
        }
    }

    /**
     * Preview file with support for all types - NO DOWNLOAD BUTTON
     */
    public function preview(File $file)
    {
        $user = Auth::user();

        if (!$user->canAccessFile($file)) {
            abort(403, 'You do not have access to this file.');
        }

        // Get user's permission for this file
        $permission = $this->getUserPermission($file, $user);

        // Check if user has view permission at minimum
        if (!in_array($permission, ['view', 'download', 'edit', 'print', 'full_control'])) {
            abort(403, 'You do not have permission to view this file.');
        }

        if (!Storage::disk('private')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        // Handle encrypted files
        if ($file->is_encrypted) {
            return $this->previewEncrypted($file, $permission);
        }

        $fileContent = Storage::disk('private')->get($file->file_path);
        $extension = strtolower($file->extension);

        // Set headers to prevent download
        $headers = [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'inline; filename="' . $file->original_name . '"',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Content-Security-Policy' => "default-src 'none'; img-src 'self'; script-src 'none'; style-src 'self'; frame-ancestors 'self';",
            'Cache-Control' => 'public, max-age=86400',
            'X-Permission-Level' => $permission,
        ];

        // Handle different file types
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'])) {
            return response($fileContent, 200, $headers);
        } 
        elseif ($extension === 'pdf') {
            return response($fileContent, 200, $headers);
        }
        elseif (in_array($extension, ['mp4', 'webm', 'ogg', 'mov', 'avi'])) {
            return response()->stream(function() use ($fileContent) {
                echo $fileContent;
            }, 200, $headers);
        }
        elseif (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a'])) {
            return response()->stream(function() use ($fileContent) {
                echo $fileContent;
            }, 200, $headers);
        }
        elseif (in_array($extension, ['txt', 'php', 'js', 'html', 'css', 'json', 'xml', 'sql', 'md'])) {
            $headers['Content-Type'] = 'text/plain; charset=utf-8';
            return response($fileContent, 200, $headers);
        }
        elseif (in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])) {
            // For Office documents, use Google Docs Viewer (doesn't allow download)
            $fileUrl = route('files.download', $file);
            $googleViewerUrl = 'https://docs.google.com/gview?url=' . urlencode($fileUrl) . '&embedded=true';
            return redirect()->away($googleViewerUrl);
        }
        else {
            abort(415, 'File type not supported for preview.');
        }
    }

    /**
     * Preview encrypted file
     */
    private function previewEncrypted(File $file, $permission)
    {
        if (!Storage::disk('private')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        $content = Storage::disk('private')->get($file->file_path);
        
        try {
            $decryptedContent = $this->encryptionService->decrypt($content, $file->encryption_key);
            
            $headers = [
                'Content-Type' => $file->mime_type,
                'Content-Disposition' => 'inline; filename="' . $file->original_name . '"',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'public, max-age=86400',
                'X-Permission-Level' => $permission,
            ];
            
            return response($decryptedContent, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt file for preview', [
                'file_id' => $file->id,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Failed to decrypt file for preview.');
        }
=======
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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    }

    public function show(File $file)
    {
        $user = Auth::user();

<<<<<<< HEAD
        if (!$user->canAccessFile($file)) {
            abort(403, 'You do not have access to this file.');
        }

        // Get user's permission level
        $permission = $this->getUserPermission($file, $user);

        $file->increment('view_count');
        $file->update(['last_accessed_at' => now()]);

        $this->logActivity(
            $user,
            request(),
            'view',
            'file',
            'Viewed file: ' . $file->name,
            null,
            $file->id
        );

        $file->load(['owner', 'department', 'shares' => function($q) {
            $q->where('status', 'active');
        }, 'shares.sharedBy', 'shares.sharedWith']);
        
        $activityLogs = ActivityLog::where('file_id', $file->id)
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        return view('files.show', compact('file', 'activityLogs', 'permission'));
    }

    public function download(Request $request, File $file)
    {
        $user = Auth::user();

        if (!$user->canAccessFile($file)) {
            // Check if OTP verification is needed for external access
            if ($file->owner_id !== $user->id) {
                return $this->requestOtpForDownload($file);
            }
            abort(403, 'You do not have access to this file.');
        }

        // Check if user has download permission
        $permission = $this->getUserPermission($file, $user);
        if (!in_array($permission, ['download', 'edit', 'full_control']) && $user->id !== $file->owner_id && !$user->isSuperAdmin()) {
            abort(403, 'You do not have permission to download this file.');
        }

        $file->increment('download_count');
        $file->update(['last_accessed_at' => now()]);

        $this->logActivity(
            $user,
            $request,
            'download',
            'file',
            'Downloaded file: ' . $file->name,
            null,
            $file->id
        );

        // Check if file is encrypted
        if ($file->is_encrypted) {
            return $this->downloadEncrypted($file);
        }

        if (!Storage::disk('private')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('private')->download($file->file_path, $file->original_name);
    }

    private function downloadEncrypted(File $file)
    {
        if (!Storage::disk('private')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        $content = Storage::disk('private')->get($file->file_path);
        
        try {
            $decryptedContent = $this->encryptionService->decrypt($content, $file->encryption_key);
            
            return response($decryptedContent)
                ->header('Content-Type', $file->mime_type)
                ->header('Content-Disposition', 'attachment; filename="' . $file->original_name . '"');
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt file for download', [
                'file_id' => $file->id,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Failed to decrypt file for download.');
        }
    }

    /**
     * Get user's permission level for a file
     */
    private function getUserPermission(File $file, User $user)
    {
        // Owner and Super Admin have full control
        if ($user->isSuperAdmin() || $user->id === $file->owner_id) {
            return 'full_control';
        }

        // Department admin can view files in their department
        if ($user->isDepartmentAdmin() && $user->department_id === $file->department_id) {
            return 'view';
        }

        // Check share permissions
        $share = FileShare::where('file_id', $file->id)
            ->where('shared_with', $user->id)
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->first();

        return $share ? $share->permission_level : 'none';
    }

    /**
     * Display files based on permission
     */
    public function byPermission(Request $request, $permission)
    {
        $user = Auth::user();
        
        // Validate permission
        $validPermissions = ['view', 'download', 'edit', 'print', 'full_control'];
        if (!in_array($permission, $validPermissions)) {
            abort(404);
        }
        
        $query = File::with(['owner', 'department', 'shares' => function($q) use ($user) {
            $q->where('shared_with', $user->id);
        }]);

        // For owner's files (they have full control)
        if ($permission === 'full_control') {
            $query->where('owner_id', $user->id);
        } 
        // For shared files with specific permission
        else {
            $query->whereHas('shares', function($q) use ($user, $permission) {
                $q->where('shared_with', $user->id)
                  ->where('status', 'active')
                  ->where('permission_level', $permission)
                  ->where(function($subQ) {
                      $subQ->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                  });
            })->orWhere('owner_id', $user->id); // Also include user's own files
        }

        // Apply search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('original_name', 'like', '%' . $request->search . '%');
            });
        }

        // Apply type filter
        if ($request->filled('type')) {
            if ($request->type === 'image') {
                $query->whereIn('extension', ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']);
            } elseif ($request->type === 'document') {
                $query->whereIn('extension', ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt']);
            } elseif ($request->type === 'spreadsheet') {
                $query->whereIn('extension', ['xls', 'xlsx', 'csv', 'ods']);
            } elseif ($request->type === 'presentation') {
                $query->whereIn('extension', ['ppt', 'pptx', 'odp']);
            } elseif ($request->type === 'archive') {
                $query->whereIn('extension', ['zip', 'rar', '7z', 'tar', 'gz']);
            } elseif ($request->type === 'audio') {
                $query->whereIn('extension', ['mp3', 'wav', 'ogg', 'm4a', 'flac']);
            } elseif ($request->type === 'video') {
                $query->whereIn('extension', ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv']);
            } elseif ($request->type === 'code') {
                $query->whereIn('extension', ['php', 'js', 'html', 'css', 'json', 'xml', 'sql']);
            }
        }

        $files = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get count for each permission type
        $counts = [
            'view' => $this->getFileCountByPermission($user, 'view'),
            'download' => $this->getFileCountByPermission($user, 'download'),
            'edit' => $this->getFileCountByPermission($user, 'edit'),
            'print' => $this->getFileCountByPermission($user, 'print'),
            'full_control' => $this->getFileCountByPermission($user, 'full_control'),
        ];

        return view('files.by-permission', compact('files', 'permission', 'counts'));
    }

    /**
     * Get file count by permission
     */
    private function getFileCountByPermission($user, $permission)
    {
        if ($permission === 'full_control') {
            return File::where('owner_id', $user->id)->count();
        }
        
        return FileShare::where('shared_with', $user->id)
            ->where('status', 'active')
            ->where('permission_level', $permission)
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->count();
    }

    /**
     * Display files shared with me (grouped view)
     */
   public function sharedWithMe(Request $request)
{
    $user = Auth::user();
    
    $shares = FileShare::with(['file', 'sharedBy', 'file.owner', 'file.department'])
        ->where('shared_with', $user->id)
        ->where('status', 'active')
        ->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);
    
    // Get counts by permission
    $counts = [
        'view' => FileShare::where('shared_with', $user->id)->where('permission_level', 'view')->where('status', 'active')->count(),
        'download' => FileShare::where('shared_with', $user->id)->where('permission_level', 'download')->where('status', 'active')->count(),
        'edit' => FileShare::where('shared_with', $user->id)->where('permission_level', 'edit')->where('status', 'active')->count(),
        'print' => FileShare::where('shared_with', $user->id)->where('permission_level', 'print')->where('status', 'active')->count(),
        'full_control' => FileShare::where('shared_with', $user->id)->where('permission_level', 'full_control')->where('status', 'active')->count(),
    ];
    
    return view('files.shared-with-me', compact('shares', 'counts'));
}

    /**
     * Display files where user has view permission only
     */
    public function viewOnlyFiles(Request $request)
    {
        return $this->byPermission($request, 'view');
    }

    /**
     * Display files where user has download permission
     */
    public function downloadableFiles(Request $request)
    {
        return $this->byPermission($request, 'download');
    }

    /**
     * Display files where user has edit permission
     */
    public function editableFiles(Request $request)
    {
        return $this->byPermission($request, 'edit');
    }

    /**
     * Display files where user has print permission
     */
    public function printableFiles(Request $request)
    {
        return $this->byPermission($request, 'print');
    }

    /**
     * Display files where user has full control (own files)
     */
    public function myFiles(Request $request)
    {
        $user = Auth::user();
        
        $query = File::with(['owner', 'department'])
            ->where('owner_id', $user->id);

        // Apply search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('original_name', 'like', '%' . $request->search . '%');
            });
        }

        // Apply type filter
        if ($request->filled('type')) {
            if ($request->type === 'image') {
                $query->whereIn('extension', ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']);
            } elseif ($request->type === 'document') {
                $query->whereIn('extension', ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt']);
            } elseif ($request->type === 'spreadsheet') {
                $query->whereIn('extension', ['xls', 'xlsx', 'csv', 'ods']);
            } elseif ($request->type === 'presentation') {
                $query->whereIn('extension', ['ppt', 'pptx', 'odp']);
            } elseif ($request->type === 'archive') {
                $query->whereIn('extension', ['zip', 'rar', '7z', 'tar', 'gz']);
            } elseif ($request->type === 'audio') {
                $query->whereIn('extension', ['mp3', 'wav', 'ogg', 'm4a', 'flac']);
            } elseif ($request->type === 'video') {
                $query->whereIn('extension', ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv']);
            } elseif ($request->type === 'code') {
                $query->whereIn('extension', ['php', 'js', 'html', 'css', 'json', 'xml', 'sql']);
            }
        }

        $files = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('files.my-files', compact('files'));
    }

    public function share(Request $request, File $file)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission_level' => 'required|in:view,download,edit,print,full_control',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $user = Auth::user();

        if ($file->owner_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403, 'You can only share your own files.');
        }

        // Check if share already exists
        $existingShare = FileShare::where('file_id', $file->id)
            ->where('shared_with', $request->user_id)
            ->where('status', 'active')
            ->first();

        if ($existingShare) {
            return back()->withErrors(['error' => 'File is already shared with this user.']);
        }

        DB::beginTransaction();
        try {
            $share = FileShare::create([
                'file_id' => $file->id,
                'shared_by' => $user->id,
                'shared_with' => $request->user_id,
                'permission_level' => $request->permission_level,
                'expires_at' => $request->expires_at,
                'status' => 'active',
                'access_token' => Str::random(64),
            ]);

            // Send notification email
            $this->brevoService->sendFileSharedEmail(
                $share->sharedWith->email,
                $user->name,
                $file->name,
                $share->permission_level,
                $share->expires_at
            );

            $this->logActivity(
                $user,
                $request,
                'share',
                'file',
                'Shared file with ' . $share->sharedWith->name,
                null,
                $file->id,
                null,
                $share->toArray()
            );

            DB::commit();

            return redirect()->back()->with('success', 'File shared successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to share file: ' . $e->getMessage()]);
        }
    }

    public function revokeAccess(Request $request, FileShare $share)
    {
        $user = Auth::user();

        if ($share->file->owner_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403);
        }

        $oldData = $share->toArray();
        $share->update(['status' => 'revoked']);

        $this->logActivity(
            $user,
            $request,
            'revoke_access',
            'file',
            'Revoked access for ' . $share->sharedWith->name,
            $oldData,
            $share->file_id
        );

        return redirect()->back()->with('success', 'Access revoked successfully.');
    }

    public function archive(Request $request, File $file)
    {
        $user = Auth::user();

        if ($file->owner_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403);
        }

        $oldData = $file->toArray();
        $file->update(['status' => 'archived']);

        $this->logActivity(
            $user,
            $request,
            'archive',
            'file',
            'Archived file: ' . $file->name,
            $oldData,
            $file->id
        );
=======
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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f

        return redirect()->route('files.index')->with('success', 'File archived successfully.');
    }

<<<<<<< HEAD
    public function restore(Request $request, File $file)
    {
        $user = Auth::user();

        if ($file->owner_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403);
        }

        if ($file->status !== 'archived') {
            return back()->withErrors(['error' => 'File is not archived.']);
        }

        $oldData = $file->toArray();
        $file->update(['status' => 'active']);

        $this->logActivity(
            $user,
            $request,
            'restore',
            'file',
            'Restored file: ' . $file->name,
            $oldData,
            $file->id
        );

        return redirect()->route('files.show', $file)->with('success', 'File restored successfully.');
    }

    public function destroy(Request $request, File $file)
    {
        $user = Auth::user();

        if ($file->owner_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Delete physical file
            if (Storage::disk('private')->exists($file->file_path)) {
                Storage::disk('private')->delete($file->file_path);
            }

            $oldData = $file->toArray();
            $file->delete();

            $this->logActivity(
                $user,
                $request,
                'delete',
                'file',
                'Deleted file: ' . $file->name,
                $oldData
            );

            DB::commit();

            return redirect()->route('files.index')->with('success', 'File deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete file: ' . $e->getMessage()]);
        }
    }

    public function verifyAccess(Request $request, $uuid)
    {
        $file = File::where('uuid', $uuid)->firstOrFail();
        
        if (!session('pending_file_download')) {
            return redirect()->route('files.show', $file);
        }

        return view('files.verify-access', compact('file'));
    }

    public function confirmAccess(Request $request, $uuid)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $file = File::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        $otpLog = OtpLog::where('file_id', $file->id)
            ->where('target_user_id', $user->id)
            ->where('purpose', 'file_access')
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpLog) {
            return back()->withErrors(['otp' => 'No valid OTP found.']);
        }

        if ($otpLog->otp_code !== $request->otp) {
            $otpLog->increment('attempts');
            return back()->withErrors(['otp' => 'Invalid OTP code.']);
        }

        $otpLog->update([
            'verified_at' => now(),
            'status' => 'verified',
        ]);

        session()->forget('pending_file_download');

        $this->logActivity(
            $user,
            $request,
            'otp_verified',
            'file',
            'OTP verified for file access',
            null,
            $file->id
        );

        return redirect()->route('files.download', $file->uuid);
    }

    private function requestOtpForDownload(File $file)
    {
        session(['pending_file_download' => $file->id]);
        
        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        OtpLog::create([
            'user_id' => $file->owner_id,
            'otp_code' => $otp,
            'purpose' => 'file_access',
            'file_id' => $file->id,
            'target_user_id' => Auth::id(),
            'expires_at' => now()->addMinutes(5),
            'status' => 'pending',
        ]);

        // Send OTP to file owner
        $this->brevoService->sendFileAccessOtp(
            $file->owner->email,
            $otp,
            $file->name,
            Auth::user()->name
        );

        return redirect()->route('files.access.verify', $file->uuid)
            ->with('info', 'OTP has been sent to the file owner for approval.');
    }

    private function logActivity($user, $request, $action, $module, $description, $oldData = null, $fileId = null, $transferId = null, $newData = null)
    {
        try {
            $agent = new Agent();
            
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'module' => $module,
                'file_id' => $fileId,
                'transfer_id' => $transferId,
                'description' => $description,
                'old_data' => $oldData ? json_encode($oldData) : null,
                'new_data' => $newData ? json_encode($newData) : null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log activity', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? null,
                'action' => $action
            ]);
        }
=======
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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    }
}