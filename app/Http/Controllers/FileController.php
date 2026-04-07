<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileShare;
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
        
        $query = File::with(['owner', 'department', 'shares' => function($q) {
            $q->where('status', 'active')->with('sharedBy', 'sharedWith');
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
                      ->where('status', 'active')
                      ->where(function($subQ) {
                          $subQ->whereNull('expires_at')
                               ->orWhere('expires_at', '>', now());
                      });
                });
            }
        } else {
            // Default: show files based on user role
            if ($user->isSuperAdmin()) {
                // Super Admin sees everything - no filter needed
            } elseif ($user->isDepartmentAdmin()) {
                $query->where('department_id', $user->department_id);
            } else {
                // Regular user sees their own files + files shared with them
                $query->where(function($q) use ($user) {
                    $q->where('owner_id', $user->id)
                        ->orWhereHas('shares', function($subQ) use ($user) {
                            $subQ->where('shared_with', $user->id)
                                  ->where('status', 'active')
                                  ->where(function($expQ) {
                                      $expQ->whereNull('expires_at')
                                           ->orWhere('expires_at', '>', now());
                                  });
                        });
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
            } elseif ($request->type === 'pdf') {
                $query->where('extension', 'pdf');
            } elseif ($request->type === 'audio') {
                $query->whereIn('extension', ['mp3', 'wav', 'ogg', 'm4a', 'flac']);
            } elseif ($request->type === 'video') {
                $query->whereIn('extension', ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv']);
            } elseif ($request->type === 'code') {
                $query->whereIn('extension', ['php', 'js', 'html', 'css', 'json', 'xml', 'sql', 'py', 'java', 'cpp']);
            } elseif ($request->type === 'other') {
                $query->whereNotIn('extension', ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'pdf', 'doc', 'docx', 'txt', 'rtf', 'odt', 'xls', 'xlsx', 'csv', 'ods', 'ppt', 'pptx', 'odp', 'zip', 'rar', '7z', 'tar', 'gz', 'mp3', 'wav', 'ogg', 'm4a', 'flac', 'mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv']);
            }
        }

        // Apply sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'size_desc':
                    $query->orderBy('file_size', 'desc');
                    break;
                case 'size_asc':
                    $query->orderBy('file_size', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Apply permission filter
        if ($request->filled('permission') && $request->filter !== 'shared-with-me') {
            $permission = $request->permission;
            if (in_array($permission, ['view', 'download', 'edit', 'print'])) {
                $query->whereHas('shares', function($q) use ($user, $permission) {
                    $q->where('shared_with', $user->id)
                      ->where('status', 'active')
                      ->where('permission_level', $permission)
                      ->where(function($subQ) {
                          $subQ->whereNull('expires_at')
                               ->orWhere('expires_at', '>', now());
                      });
                })->orWhere('owner_id', $user->id);
            }
        }

        $files = $query->paginate(15);

        // Get stats for the stats cards
        $stats = [
            'total_files' => File::count(),
            'shared_with_me' => FileShare::where('shared_with', $user->id)
                ->where('status', 'active')
                ->where(function($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->count(),
            'total_downloads' => File::sum('download_count'),
            'total_views' => File::sum('view_count'),
        ];

        // Get filter data for dropdowns
        $departments = Department::where('status', 'active')->get();
        $users = User::where('status', 'active')->get();

        return view('files.index', compact('files', 'departments', 'users', 'stats'));
    }

    public function create()
    {
        $user = Auth::user();
        $departments = $user->isSuperAdmin() ? Department::where('status', 'active')->get() : collect();
        return view('files.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $rules = [
            'file' => 'required|file|max:102400', // 100MB max
            'description' => 'nullable|string|max:500',
            'encrypt' => 'boolean',
        ];

        if (Auth::user()->isSuperAdmin()) {
            $rules['department_id'] = 'required|exists:departments,id';
        }

        $request->validate($rules);

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
                'department_id' => $user->isSuperAdmin() ? $request->department_id : $user->department_id,
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
    }

    public function show(File $file)
    {
        $user = Auth::user();
        $permission = $this->getUserPermission($file, $user);

        if ($permission === 'none') {
            abort(403, 'You do not have access to this file.');
        }

        // Require OTP for shared access (not for owner or super admin)
        if ($user->id !== $file->owner_id && !$user->isSuperAdmin()) {
            if (!session('file_otp_verified_' . $file->id)) {
                return redirect()->route('files.access.verify', $file->uuid);
            }
        }

        $file->load(['owner', 'department', 'shares' => function($q) {
            $q->where('status', 'active')->with('sharedBy');
        }, 'shares.sharedWith']);
        
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

        $activityLogs = ActivityLog::where('file_id', $file->id)
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        $pendingOtps = OtpLog::where('file_id', $file->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->get()
            ->groupBy('target_user_id');

        return view('files.show', compact('file', 'activityLogs', 'permission', 'pendingOtps'));
    }

    public function download(Request $request, File $file)
    {
        $user = Auth::user();
        $permission = $this->getUserPermission($file, $user);

        if (!in_array($permission, ['download', 'edit', 'full_control']) && $user->id !== $file->owner_id && !$user->isSuperAdmin()) {
            abort(403, 'You do not have permission to download this file.');
        }

        // Require OTP for shared access (not for owner or super admin)
        if ($user->id !== $file->owner_id && !$user->isSuperAdmin()) {
            if (!session('file_otp_verified_' . $file->id)) {
                return redirect()->route('files.access.verify', $file->uuid);
            }
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

    private function getUserPermission(File $file, User $user)
    {
        // Owner and Super Admin have full control
        if ($user->isSuperAdmin() || $user->id === $file->owner_id) {
            return 'full_control';
        }

        // Check specific share permissions first (most granular)
        $share = FileShare::where('file_id', $file->id)
            ->where('shared_with', $user->id)
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($share) {
            return $share->permission_level;
        }

        // Department admin can view all files in their department by default
        if ($user->isDepartmentAdmin() && $user->department_id === $file->department_id) {
            return 'view';
        }

        return 'none';
    }

    /**
     * Display files shared with me (grouped view)
     */
    public function sharedWithMe(Request $request)
    {
        $user = Auth::user();
        
        $query = FileShare::with(['file', 'sharedBy', 'file.owner', 'file.department'])
            ->where('shared_with', $user->id)
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });

        // Apply permission filter
        if ($request->filled('permission')) {
            $query->where('permission_level', $request->permission);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->whereHas('file', function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('original_name', 'like', $searchTerm);
            });
        }
        
        $shares = $query->orderBy('created_at', 'desc')->paginate(15);
        
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
            ->where('owner_id', $user->id)
            ->where('status', 'active');

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
            } elseif ($request->type === 'pdf') {
                $query->where('extension', 'pdf');
            }
        }

        // Apply sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'size_desc':
                    $query->orderBy('file_size', 'desc');
                    break;
                case 'size_asc':
                    $query->orderBy('file_size', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $files = $query->paginate(15);
        
        // Get stats
        $stats = [
            'total_files' => File::where('owner_id', $user->id)->count(),
            'shared_with_me' => FileShare::where('shared_with', $user->id)->where('status', 'active')->count(),
            'total_downloads' => File::where('owner_id', $user->id)->sum('download_count'),
            'total_views' => File::where('owner_id', $user->id)->sum('view_count'),
        ];
        
        return view('files.my-files', compact('files', 'stats'));
    }

    /**
     * Display files based on permission
     */
    public function byPermission(Request $request, $permission)
    {
        $user = Auth::user();
        
        // Validate permission
        $validPermissions = ['view', 'download', 'edit', 'print'];
        if (!in_array($permission, $validPermissions)) {
            abort(404);
        }
        
        $query = File::with(['owner', 'department'])
            ->whereHas('shares', function($q) use ($user, $permission) {
                $q->where('shared_with', $user->id)
                  ->where('status', 'active')
                  ->where('permission_level', $permission)
                  ->where(function($subQ) {
                      $subQ->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                  });
            });

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
            } elseif ($request->type === 'pdf') {
                $query->where('extension', 'pdf');
            }
        }

        // Apply sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'size_desc':
                    $query->orderBy('file_size', 'desc');
                    break;
                case 'size_asc':
                    $query->orderBy('file_size', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $files = $query->paginate(15);
        
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

    public function share(Request $request, File $file)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission_level' => 'required|in:view,download,edit,print,full_control',
            'expires_at' => 'nullable|date|after:now',
            'share_reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        // Super Admin can share anything, Dept Admin can share files from their department, owner can share their files
        $canShare = $user->isSuperAdmin() || 
                    ($user->isDepartmentAdmin() && $user->department_id === $file->department_id) || 
                    ($file->owner_id === $user->id);

        if (!$canShare) {
            abort(403, 'You do not have permission to share this file.');
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
                'expires_at' => $request->expires_at ? \Carbon\Carbon::parse($request->expires_at) : null,
                'share_reason' => $request->share_reason,
                'status' => 'active',
                'access_token' => \Illuminate\Support\Str::random(64),
            ]);

            $recipient = User::find($request->user_id);
            if ($recipient) {
                // Send notification email
                $this->brevoService->sendFileSharedEmail(
                    $recipient->email,
                    $user->name,
                    $file->name,
                    $share->permission_level,
                    $share->expires_at
                );
                // Create local notification
                \App\Models\Notification::create([
                    'user_id' => $recipient->id,
                    'type' => 'info',
                    'icon' => 'bi-share',
                    'message' => 'User ' . $user->name . ' shared a file with you: ' . $file->name,
                    'link' => route('files.show', $file),
                    'notifiable_id' => $recipient->id,
                    'notifiable_type' => 'App\Models\User',
                    'data' => [
                        'file_id' => $file->id,
                        'shared_by' => $user->id,
                        'permission' => $share->permission_level
                    ]
                ]);
            }

            $this->logActivity(
                $user,
                $request,
                'share',
                'file',
                'Shared file with ' . ($recipient->name ?? 'User'),
                null,
                $file->id,
                null,
                $share->toArray()
            );

            DB::commit();

            return redirect()->back()->with('success', 'File shared successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Share error: ' . $e->getMessage());
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

        return redirect()->route('files.index')->with('success', 'File archived successfully.');
    }

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
        $user = Auth::user();
        
        // If already verified or user is exempt, redirect to show
        if (session('file_otp_verified_' . $file->id) || $user->id === $file->owner_id || $user->isSuperAdmin()) {
            return redirect()->route('files.show', $file);
        }

        // Check if an OTP is already pending, if not, generate and send one
        $pendingOtp = OtpLog::where('file_id', $file->id)
            ->where('target_user_id', $user->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$pendingOtp) {
            // This implicitly calls sendEmail in most implementations, but we must ensure it's called
            $this->requestOtpForDownload($file);
        }

        return view('files.verify-access', compact('file'));
    }

    public function confirmAccess(Request $request, $uuid)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
            'access_reason' => 'required|string|max:500',
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

        session(["file_otp_verified_{$file->id}" => true]);
        session()->forget('pending_file_download');

        $this->logActivity(
            $user,
            $request,
            'otp_verified',
            'file',
            'OTP verified for file access. Reason: ' . $request->access_reason,
            null,
            $file->id
        );

        $redirectTo = session()->pull('otp_redirect_to', 'download');
        
        if ($redirectTo === 'show') {
            return redirect()->route('files.show', $file);
        }

        return redirect()->route('files.download', $file);
    }

    public function edit(File $file)
    {
        $user = Auth::user();
        $permission = $this->getUserPermission($file, $user);

        if (!in_array($permission, ['edit', 'full_control'])) {
            abort(403, 'You do not have permission to edit this file.');
        }

        $departments = $user->isSuperAdmin() ? Department::where('status', 'active')->get() : collect();
        
        return view('files.edit', compact('file', 'departments'));
    }

    public function update(Request $request, File $file)
    {
        $user = Auth::user();
        $permission = $this->getUserPermission($file, $user);

        if (!in_array($permission, ['edit', 'full_control'])) {
            abort(403, 'You do not have permission to edit this file.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ];

        if ($user->isSuperAdmin()) {
            $rules['department_id'] = 'required|exists:departments,id';
        }

        $request->validate($rules);

        $oldData = $file->toArray();
        
        $updateData = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        if ($user->isSuperAdmin()) {
            $updateData['department_id'] = $request->department_id;
        }

        $file->update($updateData);

        $this->logActivity(
            $user,
            $request,
            'update',
            'file',
            'Updated file: ' . $file->name,
            $oldData,
            $file->id,
            null,
            $file->toArray()
        );

        return redirect()->route('files.show', $file)
            ->with('success', 'File updated successfully.');
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

        // Create local notification for owner
        \App\Models\Notification::create([
            'user_id' => $file->owner_id,
            'type' => 'warning',
            'icon' => 'bi-shield-lock',
            'message' => 'User ' . Auth::user()->name . ' requested access to ' . $file->name,
            'link' => route('files.show', $file),
            'notifiable_id' => $file->owner_id,
            'notifiable_type' => 'App\Models\User',
            'data' => [
                'file_id' => $file->id,
                'requester_id' => Auth::id()
            ]
        ]);

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
    }
}