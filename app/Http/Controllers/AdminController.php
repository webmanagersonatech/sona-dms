<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\File;
use App\Models\Transfer;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SystemSetting;
use App\Exports\StatsExport;
use App\Exports\AuditLogsExport;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin,admin']);
    }

    public function dashboard()
    {
        $stats = [
            'total_files' => File::count(),
            'total_users' => User::count(),
            'active_transfers' => Transfer::whereIn('status', ['pending', 'in_transit'])->count(),
            'otp_approvals_today' => \App\Models\Otp::whereDate('verified_at', today())->count(),
            'department_activity' => ActivityLog::whereDate('performed_at', today())->count(),
        ];

        $recentFiles = File::with(['owner', 'department'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentTransfers = Transfer::with(['sender', 'receiver', 'file'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentActivity = ActivityLog::with(['user', 'file'])
            ->orderBy('performed_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentFiles', 'recentTransfers', 'recentActivity'));
    }

    public function users()
    {
        $users = User::with(['role', 'department'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $roles = Role::where('is_active', true)
            ->where('id', '!=', 1)
            ->get();
        $departments = Department::where('is_active', true)->get();

        return view('admin.users.index', compact('users', 'roles', 'departments'));
    }

    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'required|exists:departments,id',
            'employee_id' => 'nullable|string|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
            'employee_id' => $request->employee_id,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'required|exists:departments,id',
            'employee_id' => 'nullable|string|unique:users,employee_id,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_locked' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
            'employee_id' => $request->employee_id,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active'),
            'is_locked' => $request->boolean('is_locked'),
        ]);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password reset successfully.');
    }

    public function departments()
    {
        $departments = Department::orderBy('name')->paginate(20);
        return view('admin.departments.index', compact('departments'));
    }

    public function storeDepartment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:departments',
            'code' => 'required|string|max:50|unique:departments',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Department::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('admin.departments')->with('success', 'Department created successfully.');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $department->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->back()->with('success', 'Department updated successfully.');
    }

    public function auditLogs(Request $request)
    {
        $query = ActivityLog::with(['user', 'file', 'transfer'])
            ->orderBy('performed_at', 'desc');

        // Apply filters
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('date_from')) {
            $query->whereDate('performed_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('performed_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);
        $users = User::where('is_active', true)->get();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('admin.audit-logs', compact('logs', 'users', 'actions'));
    }

    public function systemStats()
    {
        // File statistics by type
        $fileStats = File::selectRaw('extension, count(*) as count')
            ->groupBy('extension')
            ->orderBy('count', 'desc')
            ->get();

        // User statistics by role
        $userStats = User::selectRaw('roles.name as role_name, count(*) as count')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->get();

        // Transfer statistics by status
        $transferStats = Transfer::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        // Daily activity
        $dailyActivity = ActivityLog::selectRaw('DATE(performed_at) as date, count(*) as count')
            ->whereDate('performed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.stats', compact('fileStats', 'userStats', 'transferStats', 'dailyActivity'));
    }

    public function fileManagement(Request $request)
    {
        $query = File::with(['owner', 'department'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        if ($request->has('status')) {
            if ($request->status === 'archived') {
                $query->where('is_archived', true);
            } elseif ($request->status === 'expired') {
                $query->where('expires_at', '<', now());
            } elseif ($request->status === 'shared') {
                $query->where('is_shared', true);
            }
        }

        $files = $query->paginate(30);
        $departments = Department::where('is_active', true)->get();
        $owners = User::where('is_active', true)->get();

        return view('admin.files.index', compact('files', 'departments', 'owners'));
    }

    public function transferMonitoring()
    {
        $transfers = Transfer::with(['sender', 'receiver', 'file'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $stats = [
            'pending' => Transfer::where('status', 'pending')->count(),
            'in_transit' => Transfer::where('status', 'in_transit')->count(),
            'delivered' => Transfer::where('status', 'delivered')->count(),
            'received' => Transfer::where('status', 'received')->count(),
        ];

        return view('admin.transfers.monitor', compact('transfers', 'stats'));
    }

    public function bulkActions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:archive,restore,delete',
            'files' => 'required|array',
            'files.*' => 'exists:files,id',
        ]);

        $files = File::whereIn('id', $request->files)->get();

        foreach ($files as $file) {
            switch ($request->action) {
                case 'archive':
                    if (!$file->is_archived) {
                        $file->update(['is_archived' => true, 'archived_at' => now()]);
                    }
                    break;
                case 'restore':
                    if ($file->is_archived) {
                        $file->update(['is_archived' => false, 'archived_at' => null]);
                    }
                    break;
                case 'delete':
                    // Soft delete
                    $file->delete();
                    break;
            }
        }

        return redirect()->back()->with('success', 'Bulk action completed successfully.');
    }

     public function exportStats(Request $request)
    {
        $period = $request->input('period', 'monthly'); // daily, weekly, monthly, yearly
        $format = $request->input('format', 'excel'); // excel, csv, pdf
        
        switch ($format) {
            case 'csv':
                return Excel::download(new StatsExport($period), "system_stats_{$period}.csv");
            case 'pdf':
                return Excel::download(new StatsExport($period), "system_stats_{$period}.pdf", \Maatwebsite\Excel\Excel::MPDF);
            default:
                return Excel::download(new StatsExport($period), "system_stats_{$period}.xlsx");
        }
    }
    
    /**
     * Export audit logs
     */
    public function exportAuditLogs(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $format = $request->input('format', 'excel');
        
        return Excel::download(
            new AuditLogsExport($startDate, $endDate),
            "audit_logs_{$startDate}_to_{$endDate}.xlsx"
        );
    }
    
    /**
     * Show system settings page
     */
    public function settings()
    {
        $settings = SystemSetting::all()->pluck('value', 'key');
        
        return view('admin.settings', compact('settings'));
    }
    
    /**
     * Update system settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'file_upload_max_size' => 'required|integer|min:1',
            'file_expiry_days' => 'required|integer|min:1',
            'transfer_expiry_hours' => 'required|integer|min:1',
            'otp_expiry_minutes' => 'required|integer|min:1',
            'maintenance_mode' => 'boolean',
            'enable_registration' => 'boolean',
            'enable_two_factor' => 'boolean',
        ]);
        
        foreach ($validated as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
        
        // Clear cache if needed
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        
        return redirect()->route('admin.settings')
            ->with('success', 'System settings updated successfully.');
    }
    
    /**
     * Show backup management page
     */
    // public function backups()
    // {
    //     $backupPath = storage_path('app/backups');
    //     $backups = [];
        
    //     if (file_exists($backupPath)) {
    //         $files = scandir($backupPath);
    //         foreach ($files as $file) {
    //             if ($file != '.' && $file != '..') {
    //                 $filePath = $backupPath . '/' . $file;
    //                 $backups[] = [
    //                     'filename' => $file,
    //                     'size' => filesize($filePath),
    //                     'created_at' => Carbon::createFromTimestamp(filemtime($filePath)),
    //                     'path' => $filePath,
    //                 ];
    //             }
    //         }
            
    //         // Sort by creation date, newest first
    //         usort($backups, function ($a, $b) {
    //             return $b['created_at'] <=> $a['created_at'];
    //         });
    //     }
        
    //     return view('admin.backups', compact('backups'));
    // }
    
    /**
     * Create a new backup
     */
    // public function createBackup(Request $request)
    // {
    //     $type = $request->input('type', 'full'); // full, database, files
        
    //     try {
    //         $filename = 'backup_' . date('Y-m-d_H-i-s') . '_' . $type . '.zip';
    //         $backupPath = storage_path('app/backups');
            
    //         // Create backups directory if it doesn't exist
    //         if (!file_exists($backupPath)) {
    //             mkdir($backupPath, 0755, true);
    //         }
            
    //         // For demonstration - you should use a proper backup package
    //         // like spatie/laravel-backup in production
    //         $command = '';
            
    //         switch ($type) {
    //             case 'database':
    //                 $command = "mysqldump -u " . env('DB_USERNAME') . 
    //                           " -p'" . env('DB_PASSWORD') . "' " . 
    //                           env('DB_DATABASE') . " > " . $backupPath . "/" . str_replace('.zip', '.sql', $filename);
    //                 break;
    //             case 'files':
    //                 // Backup storage files
    //                 $storagePath = storage_path('app');
    //                 $command = "cd " . $storagePath . " && zip -r " . $backupPath . "/" . $filename . " .";
    //                 break;
    //             default:
    //                 // Full backup (database + files)
    //                 $command = $this->createFullBackup($backupPath, $filename);
    //         }
            
    //         if ($command) {
    //             exec($command, $output, $returnCode);
                
    //             if ($returnCode === 0) {
    //                 // Log backup creation
    //                 AuditLog::create([
    //                     'user_id' => auth()->id(),
    //                     'action' => 'backup_created',
    //                     'description' => "Created {$type} backup: {$filename}",
    //                     'ip_address' => $request->ip(),
    //                     'user_agent' => $request->userAgent(),
    //                 ]);
                    
    //                 return redirect()->route('admin.backups')
    //                     ->with('success', "{$type} backup created successfully.");
    //             }
    //         }
            
    //         return redirect()->route('admin.backups')
    //             ->with('error', 'Failed to create backup.');
                
    //     } catch (\Exception $e) {
    //         return redirect()->route('admin.backups')
    //             ->with('error', 'Backup failed: ' . $e->getMessage());
    //     }
    // }
    
    /**
     * Restore from a backup
     */
    // public function restoreBackup(Request $request, $filename)
    // {
    //     $request->validate([
    //         'confirmation' => 'required|in:yes,confirm',
    //     ]);
        
    //     if ($request->input('confirmation') !== 'yes') {
    //         return redirect()->route('admin.backups')
    //             ->with('error', 'Backup restoration cancelled.');
    //     }
        
    //     try {
    //         $backupPath = storage_path("app/backups/{$filename}");
            
    //         if (!file_exists($backupPath)) {
    //             return redirect()->route('admin.backups')
    //                 ->with('error', 'Backup file not found.');
    //         }
            
    //         // Determine backup type and restore accordingly
    //         if (str_contains($filename, 'database')) {
    //             // Restore database
    //             $this->restoreDatabase($backupPath);
    //         } elseif (str_contains($filename, 'files')) {
    //             // Restore files
    //             $this->restoreFiles($backupPath);
    //         } else {
    //             // Restore full backup
    //             $this->restoreFullBackup($backupPath);
    //         }
            
    //         // Log restoration
    //         AuditLog::create([
    //             'user_id' => auth()->id(),
    //             'action' => 'backup_restored',
    //             'description' => "Restored from backup: {$filename}",
    //             'ip_address' => $request->ip(),
    //             'user_agent' => $request->userAgent(),
    //         ]);
            
    //         return redirect()->route('admin.backups')
    //             ->with('success', 'Backup restored successfully. Please clear cache if needed.');
                
    //     } catch (\Exception $e) {
    //         return redirect()->route('admin.backups')
    //             ->with('error', 'Restoration failed: ' . $e->getMessage());
    //     }
    // }
    
    /**
     * Helper method to create full backup
     */
    // private function createFullBackup($backupPath, $filename)
    // {
    //     // Create temporary directory
    //     $tempDir = sys_get_temp_dir() . '/backup_' . time();
    //     mkdir($tempDir, 0755, true);
        
    //     // Backup database
    //     $dbFile = $tempDir . '/database.sql';
    //     $dbCommand = "mysqldump -u " . env('DB_USERNAME') . 
    //                 " -p'" . env('DB_PASSWORD') . "' " . 
    //                 env('DB_DATABASE') . " > " . $dbFile;
    //     exec($dbCommand);
        
    //     // Copy important directories
    //     $directories = [
    //         storage_path('app') => 'storage',
    //         database_path('seeds') => 'database/seeds',
    //         resource_path('views') => 'resources/views',
    //         app_path() => 'app',
    //     ];
        
    //     foreach ($directories as $source => $dest) {
    //         if (is_dir($source)) {
    //             $destPath = $tempDir . '/' . $dest;
    //             if (!file_exists(dirname($destPath))) {
    //                 mkdir(dirname($destPath), 0755, true);
    //             }
    //             $this->copyDirectory($source, $destPath);
    //         }
    //     }
        
    //     // Create zip file
    //     $zipCommand = "cd " . $tempDir . " && zip -r " . $backupPath . "/" . $filename . " .";
    //     exec($zipCommand);
        
    //     // Clean up temporary directory
    //     $this->deleteDirectory($tempDir);
        
    //     return $zipCommand;
    // }
    
    /**
     * Helper to copy directory recursively
     */
    // private function copyDirectory($src, $dst)
    // {
    //     $dir = opendir($src);
    //     @mkdir($dst, 0755, true);
        
    //     while (($file = readdir($dir)) !== false) {
    //         if ($file != '.' && $file != '..') {
    //             if (is_dir($src . '/' . $file)) {
    //                 $this->copyDirectory($src . '/' . $file, $dst . '/' . $file);
    //             } else {
    //                 copy($src . '/' . $file, $dst . '/' . $file);
    //             }
    //         }
    //     }
    //     closedir($dir);
    // }
    
    /**
     * Helper to delete directory recursively
     */
    // private function deleteDirectory($dir)
    // {
    //     if (!file_exists($dir)) return true;
        
    //     $files = array_diff(scandir($dir), array('.', '..'));
    //     foreach ($files as $file) {
    //         $path = $dir . '/' . $file;
    //         is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
    //     }
    //     return rmdir($dir);
    // }
    
    /**
     * Helper to restore database
     */
    // private function restoreDatabase($backupPath)
    // {
    //     $command = "mysql -u " . env('DB_USERNAME') . 
    //               " -p'" . env('DB_PASSWORD') . "' " . 
    //               env('DB_DATABASE') . " < " . $backupPath;
    //     exec($command);
    // }
    
    /**
     * Helper to restore files
     */
    // private function restoreFiles($backupPath)
    // {
    //     $extractPath = storage_path('app');
    //     $command = "unzip -o " . $backupPath . " -d " . $extractPath;
    //     exec($command);
    // }
    
    /**
     * Helper to restore full backup
     */
    // private function restoreFullBackup($backupPath)
    // {
    //     $tempDir = sys_get_temp_dir() . '/restore_' . time();
    //     mkdir($tempDir, 0755, true);
        
    //     // Extract backup
    //     $command = "unzip " . $backupPath . " -d " . $tempDir;
    //     exec($command);
        
    //     // Restore database if exists
    //     $dbFile = $tempDir . '/database.sql';
    //     if (file_exists($dbFile)) {
    //         $this->restoreDatabase($dbFile);
    //     }
        
    //     // Restore files
    //     $this->restoreFilesFromTemp($tempDir);
        
    //     // Clean up
    //     $this->deleteDirectory($tempDir);
    // }
    
    /**
     * Helper to restore files from temp directory
     */
    // private function restoreFilesFromTemp($tempDir)
    // {
    //     // This would restore files to their original locations
    //     // Implementation depends on your backup structure
    // }
}