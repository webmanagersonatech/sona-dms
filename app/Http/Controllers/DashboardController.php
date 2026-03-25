<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\File;
use App\Models\Transfer;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        } elseif ($user->isDepartmentAdmin()) {
            return $this->departmentAdminDashboard();
        } else {
            return $this->userDashboard();
        }
    }

    private function superAdminDashboard()
    {
        // Get statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_departments' => Department::count(),
            'total_files' => File::count(),
            'total_transfers' => Transfer::count(),
            'files_this_month' => File::whereMonth('created_at', now()->month)->count(),
            'transfers_this_month' => Transfer::whereMonth('created_at', now()->month)->count(),
            'storage_used' => File::sum('file_size'),
            'pending_transfers' => Transfer::where('status', 'pending')->count(),
        ];

        // Get recent activities
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Get recent files
        $recentFiles = File::with('owner', 'department')
            ->latest()
            ->take(5)
            ->get();

        // Get recent transfers
        $recentTransfers = Transfer::with('sender', 'receiver')
            ->latest()
            ->take(5)
            ->get();

        // Get users by department for chart
        $usersByDepartment = Department::withCount('users')
            ->get()
            ->mapWithKeys(function ($dept) {
                return [$dept->name => $dept->users_count];
            });

        // Get file types distribution
        $fileTypes = File::select('extension', DB::raw('count(*) as count'))
            ->groupBy('extension')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Get transfer status distribution
        $transferStatus = Transfer::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Get daily activity for last 7 days
        $dailyActivity = ActivityLog::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard.super-admin', compact(
            'stats',
            'recentActivities',
            'recentFiles',
            'recentTransfers',
            'usersByDepartment',
            'fileTypes',
            'transferStatus',
            'dailyActivity'
        ));
    }

    private function departmentAdminDashboard()
    {
        $user = Auth::user();
        $departmentId = $user->department_id;

        // Get statistics
        $stats = [
            'total_users' => User::where('department_id', $departmentId)->count(),
            'active_users' => User::where('department_id', $departmentId)->where('status', 'active')->count(),
            'total_files' => File::where('department_id', $departmentId)->count(),
            'total_transfers' => Transfer::whereHas('sender', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })->count(),
            'files_this_month' => File::where('department_id', $departmentId)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'storage_used' => File::where('department_id', $departmentId)->sum('file_size'),
            'pending_transfers' => Transfer::whereHas('sender', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })->where('status', 'pending')->count(),
        ];

        // Get recent activities
        $recentActivities = ActivityLog::whereHas('user', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        // Get department files
        $recentFiles = File::with('owner')
            ->where('department_id', $departmentId)
            ->latest()
            ->take(5)
            ->get();

        // Get pending transfers
        $pendingTransfers = Transfer::whereHas('sender', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            })
            ->where('status', 'pending')
            ->with('sender', 'receiver')
            ->latest()
            ->take(5)
            ->get();

        // Get file types distribution
        $fileTypes = File::where('department_id', $departmentId)
            ->select('extension', DB::raw('count(*) as count'))
            ->groupBy('extension')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.department-admin', compact(
            'stats',
            'recentActivities',
            'recentFiles',
            'pendingTransfers',
            'fileTypes'
        ));
    }

    private function userDashboard()
    {
        $user = Auth::user();

        // Get statistics
        $stats = [
            'my_files' => File::where('owner_id', $user->id)->count(),
            'shared_with_me' => $user->sharedFiles()->count(),
            'my_transfers' => Transfer::where('sender_id', $user->id)->count(),
            'pending_transfers' => Transfer::where('receiver_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'recent_uploads' => File::where('owner_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'storage_used' => File::where('owner_id', $user->id)->sum('file_size'),
            'total_downloads' => File::where('owner_id', $user->id)->sum('download_count'),
        ];

        // Get recent files
        $recentFiles = File::where('owner_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Get shared files
        $sharedFiles = $user->sharedFiles()
            ->with('owner')
            ->latest()
            ->take(5)
            ->get();

        // Get recent activities
        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        // Get pending transfers
        $pendingTransfers = Transfer::where('receiver_id', $user->id)
            ->where('status', 'pending')
            ->with('sender')
            ->latest()
            ->take(5)
            ->get();

        // Get outgoing transfers
        $outgoingTransfers = Transfer::where('sender_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.user', compact(
            'stats',
            'recentFiles',
            'sharedFiles',
            'recentActivities',
            'pendingTransfers',
            'outgoingTransfers'
        ));
    }
}