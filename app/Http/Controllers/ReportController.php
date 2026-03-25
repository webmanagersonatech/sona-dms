<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Transfer;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:viewReports,App\Models\User');
    }

    public function dashboard()
    {
        $user = Auth::user();

        $data = [
            'file_stats' => $this->getFileStats(),
            'transfer_stats' => $this->getTransferStats(),
            'user_stats' => $this->getUserStats(),
            'activity_stats' => $this->getActivityStats(),
        ];

        return view('reports.dashboard', $data);
    }

    public function files(Request $request)
    {
        $query = File::with(['owner', 'department']);

        if (!Auth::user()->isSuperAdmin()) {
            if (Auth::user()->isDepartmentAdmin()) {
                $query->where('department_id', Auth::user()->department_id);
            } else {
                $query->where('owner_id', Auth::id());
            }
        }

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }
        if ($request->filled('file_type')) {
            if ($request->file_type === 'image') {
                $query->whereIn('extension', ['jpg', 'jpeg', 'png', 'gif', 'bmp']);
            } elseif ($request->file_type === 'document') {
                $query->whereIn('extension', ['pdf', 'doc', 'docx', 'txt']);
            } elseif ($request->file_type === 'spreadsheet') {
                $query->whereIn('extension', ['xls', 'xlsx', 'csv']);
            }
        }

        // Get statistics
        $stats = [
            'total_files' => (clone $query)->count(),
            'total_size' => (clone $query)->sum('file_size'),
            'avg_size' => (clone $query)->avg('file_size'),
            'total_downloads' => (clone $query)->sum('download_count'),
            'total_views' => (clone $query)->sum('view_count'),
            'by_extension' => (clone $query)->select('extension', DB::raw('count(*) as count'))
                ->groupBy('extension')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'by_department' => (clone $query)->select('department_id', DB::raw('count(*) as count'))
                ->with('department')
                ->groupBy('department_id')
                ->get(),
            'by_month' => (clone $query)->select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('count(*) as count')
                )
                ->whereDate('created_at', '>=', now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get(),
            'top_files' => (clone $query)->orderBy('download_count', 'desc')->limit(10)->get(),
        ];

        $files = $query->latest()->paginate(20);
        
        $departments = Department::where('status', 'active')->get();
        $users = User::where('status', 'active')->get();

        return view('reports.files', compact('files', 'stats', 'departments', 'users'));
    }

    public function transfers(Request $request)
    {
        $query = Transfer::with(['sender', 'receiver', 'file']);

        if (!Auth::user()->isSuperAdmin()) {
            if (Auth::user()->isDepartmentAdmin()) {
                $query->whereHas('sender', function($q) {
                    $q->where('department_id', Auth::user()->department_id);
                });
            } else {
                $query->where(function($q) {
                    $q->where('sender_id', Auth::id())
                      ->orWhere('receiver_id', Auth::id());
                });
            }
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }

        // Get statistics
        $stats = [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'in_transit' => (clone $query)->where('status', 'in_transit')->count(),
            'delivered' => (clone $query)->where('status', 'delivered')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'overdue' => (clone $query)->where('status', '!=', 'delivered')
                ->where('expected_delivery_time', '<', now())
                ->count(),
            'avg_delivery_time' => (clone $query)->where('status', 'delivered')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, actual_delivery_time)) as avg'))
                ->first()->avg,
            'by_month' => (clone $query)->select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('count(*) as count')
                )
                ->whereDate('created_at', '>=', now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get(),
        ];

        $transfers = $query->latest()->paginate(20);
        $users = User::where('status', 'active')->get();

        return view('reports.transfers', compact('transfers', 'stats', 'users'));
    }

    public function users(Request $request)
    {
        $query = User::with(['role', 'department']);

        if (!Auth::user()->isSuperAdmin()) {
            if (Auth::user()->isDepartmentAdmin()) {
                $query->where('department_id', Auth::user()->department_id);
            }
        }

        // Apply filters
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Get statistics
        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'inactive' => (clone $query)->where('status', 'inactive')->count(),
            'suspended' => (clone $query)->where('status', 'suspended')->count(),
            'by_role' => (clone $query)->select('role_id', DB::raw('count(*) as count'))
                ->with('role')
                ->groupBy('role_id')
                ->get(),
            'by_department' => (clone $query)->select('department_id', DB::raw('count(*) as count'))
                ->with('department')
                ->groupBy('department_id')
                ->get(),
            'new_users' => (clone $query)->whereDate('created_at', '>=', now()->subDays(30))->count(),
            'by_month' => (clone $query)->select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('count(*) as count')
                )
                ->whereDate('created_at', '>=', now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get(),
        ];

        $users = $query->latest()->paginate(20);
        
        $roles = \App\Models\Role::all();
        $departments = Department::where('status', 'active')->get();

        return view('reports.users', compact('users', 'stats', 'roles', 'departments'));
    }

    public function activities(Request $request)
    {
        $query = ActivityLog::with('user');

        if (!Auth::user()->isSuperAdmin()) {
            if (Auth::user()->isDepartmentAdmin()) {
                $query->whereHas('user', function($q) {
                    $q->where('department_id', Auth::user()->department_id);
                });
            } else {
                $query->where('user_id', Auth::id());
            }
        }

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Get statistics
        $stats = [
            'total' => (clone $query)->count(),
            'by_action' => (clone $query)->select('action', DB::raw('count(*) as count'))
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->get(),
            'by_module' => (clone $query)->select('module', DB::raw('count(*) as count'))
                ->groupBy('module')
                ->orderBy('count', 'desc')
                ->get(),
            'by_hour' => (clone $query)->select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as count'))
                ->whereDate('created_at', today())
                ->groupBy('hour')
                ->orderBy('hour')
                ->get(),
            'by_day' => (clone $query)->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        $activities = $query->latest()->paginate(50);

        return view('reports.activities', compact('activities', 'stats'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'excel');
        $reportType = $request->get('report_type', 'files');

        switch ($reportType) {
            case 'files':
                return $this->exportFiles($request, $type);
            case 'transfers':
                return $this->exportTransfers($request, $type);
            case 'users':
                return $this->exportUsers($request, $type);
            case 'activities':
                return $this->exportActivities($request, $type);
            default:
                return back()->withErrors(['type' => 'Invalid report type']);
        }
    }

    private function exportFiles($request, $type)
    {
        $query = File::with(['owner', 'department']);
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $files = $query->get();

        if ($type === 'excel') {
            return Excel::download(new class($files) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                private $files;
                public function __construct($files) { $this->files = $files; }
                public function headings(): array {
                    return ['File Name', 'Type', 'Size', 'Owner', 'Department', 'Uploaded Date', 'Downloads', 'Views'];
                }
                public function array(): array {
                    $data = [];
                    foreach ($this->files as $file) {
                        $data[] = [
                            $file->name,
                            strtoupper($file->extension),
                            $this->formatBytes($file->file_size),
                            $file->owner->name,
                            $file->department->name ?? 'N/A',
                            $file->created_at->format('Y-m-d H:i:s'),
                            $file->download_count,
                            $file->view_count
                        ];
                    }
                    return $data;
                }
                private function formatBytes($bytes) {
                    if ($bytes === 0) return '0 Bytes';
                    $k = 1024;
                    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    $i = floor(log($bytes) / log($k));
                    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
                }
            }, 'files-report.xlsx');
        } else {
            $pdf = Pdf::loadView('reports.exports.files-pdf', compact('files'));
            return $pdf->download('files-report.pdf');
        }
    }

    private function exportTransfers($request, $type)
    {
        $query = Transfer::with(['sender', 'receiver']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transfers = $query->get();

        if ($type === 'excel') {
            return Excel::download(new class($transfers) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                private $transfers;
                public function __construct($transfers) { $this->transfers = $transfers; }
                public function headings(): array {
                    return ['Transfer ID', 'Sender', 'Receiver', 'Purpose', 'Expected Delivery', 'Actual Delivery', 'Status'];
                }
                public function array(): array {
                    $data = [];
                    foreach ($this->transfers as $transfer) {
                        $data[] = [
                            $transfer->transfer_id,
                            $transfer->sender->name,
                            $transfer->receiver->name ?? $transfer->receiver_name,
                            $transfer->purpose,
                            $transfer->expected_delivery_time->format('Y-m-d H:i:s'),
                            $transfer->actual_delivery_time?->format('Y-m-d H:i:s') ?? 'Not delivered',
                            ucfirst($transfer->status)
                        ];
                    }
                    return $data;
                }
            }, 'transfers-report.xlsx');
        } else {
            $pdf = Pdf::loadView('reports.exports.transfers-pdf', compact('transfers'));
            return $pdf->download('transfers-report.pdf');
        }
    }

    private function exportUsers($request, $type)
    {
        $query = User::with(['role', 'department']);
        
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $users = $query->get();

        if ($type === 'excel') {
            return Excel::download(new class($users) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                private $users;
                public function __construct($users) { $this->users = $users; }
                public function headings(): array {
                    return ['Name', 'Email', 'Role', 'Department', 'Status', 'Last Login', 'Files Count'];
                }
                public function array(): array {
                    $data = [];
                    foreach ($this->users as $user) {
                        $data[] = [
                            $user->name,
                            $user->email,
                            $user->role->name,
                            $user->department->name ?? 'N/A',
                            ucfirst($user->status),
                            $user->last_login_at?->format('Y-m-d H:i:s') ?? 'Never',
                            $user->files()->count()
                        ];
                    }
                    return $data;
                }
            }, 'users-report.xlsx');
        } else {
            $pdf = Pdf::loadView('reports.exports.users-pdf', compact('users'));
            return $pdf->download('users-report.pdf');
        }
    }

    private function exportActivities($request, $type)
    {
        $query = ActivityLog::with('user');
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->get();

        if ($type === 'excel') {
            return Excel::download(new class($activities) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                private $activities;
                public function __construct($activities) { $this->activities = $activities; }
                public function headings(): array {
                    return ['Date/Time', 'User', 'Action', 'Module', 'Description', 'IP Address', 'Device'];
                }
                public function array(): array {
                    $data = [];
                    foreach ($this->activities as $activity) {
                        $data[] = [
                            $activity->created_at->format('Y-m-d H:i:s'),
                            $activity->user->name ?? 'System',
                            ucfirst(str_replace('_', ' ', $activity->action)),
                            ucfirst($activity->module),
                            $activity->description,
                            $activity->ip_address ?? 'N/A',
                            $activity->device_type ?? 'N/A'
                        ];
                    }
                    return $data;
                }
            }, 'activities-report.xlsx');
        } else {
            $pdf = Pdf::loadView('reports.exports.activities-pdf', compact('activities'));
            return $pdf->download('activities-report.pdf');
        }
    }

    private function getFileStats()
    {
        $query = File::query();

        if (!Auth::user()->isSuperAdmin()) {
            if (Auth::user()->isDepartmentAdmin()) {
                $query->where('department_id', Auth::user()->department_id);
            } else {
                $query->where('owner_id', Auth::id());
            }
        }

        return [
            'total' => $query->count(),
            'total_size' => $query->sum('file_size'),
            'by_type' => $query->select('extension', DB::raw('count(*) as count'))
                ->groupBy('extension')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
            'trend' => $query->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }

    private function getTransferStats()
    {
        $query = Transfer::query();

        if (!Auth::user()->isSuperAdmin()) {
            if (Auth::user()->isDepartmentAdmin()) {
                $query->whereHas('sender', function($q) {
                    $q->where('department_id', Auth::user()->department_id);
                });
            } else {
                $query->where('sender_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            }
        }

        return [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'delivered' => (clone $query)->where('status', 'delivered')->count(),
            'trend' => $query->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }

    private function getUserStats()
    {
        if (!Auth::user()->can('viewAny', User::class)) {
            return null;
        }

        $query = User::query();

        if (!Auth::user()->isSuperAdmin() && Auth::user()->isDepartmentAdmin()) {
            $query->where('department_id', Auth::user()->department_id);
        }

        return [
            'total' => $query->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'new' => (clone $query)->whereDate('created_at', '>=', now()->subDays(30))->count(),
            'by_role' => $query->select('role_id', DB::raw('count(*) as count'))
                ->with('role')
                ->groupBy('role_id')
                ->get(),
        ];
    }

    private function getActivityStats()
    {
        $query = ActivityLog::query();

        if (!Auth::user()->isSuperAdmin()) {
            if (Auth::user()->isDepartmentAdmin()) {
                $query->whereHas('user', function($q) {
                    $q->where('department_id', Auth::user()->department_id);
                });
            } else {
                $query->where('user_id', Auth::id());
            }
        }

        return [
            'today' => (clone $query)->whereDate('created_at', today())->count(),
            'week' => (clone $query)->whereDate('created_at', '>=', now()->subDays(7))->count(),
            'month' => (clone $query)->whereDate('created_at', '>=', now()->subDays(30))->count(),
            'by_action' => $query->select('action', DB::raw('count(*) as count'))
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
        ];
    }
}