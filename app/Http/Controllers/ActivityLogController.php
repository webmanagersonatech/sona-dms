<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ActivityLog::class);

        $query = ActivityLog::with(['user', 'file', 'transfer']);

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
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('ip_address', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('user_id') && Auth::user()->isSuperAdmin()) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(50);

        // Get unique actions and modules for filters
        $actions = ActivityLog::distinct('action')->pluck('action');
        $modules = ActivityLog::distinct('module')->pluck('module');
        $users = Auth::user()->isSuperAdmin() ? User::all() : collect();

        // Get statistics
        $stats = [
            'total' => $logs->total(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'week' => ActivityLog::whereDate('created_at', '>=', now()->subDays(7))->count(),
            'month' => ActivityLog::whereDate('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('logs.index', compact('logs', 'actions', 'modules', 'users', 'stats'));
    }

    public function show(ActivityLog $log)
    {
        $this->authorize('view', $log);
        
        $log->load(['user', 'file', 'transfer']);
        
        return view('logs.show', compact('log'));
    }

    public function export(Request $request)
    {
        $this->authorize('export', ActivityLog::class);

        $format = $request->get('format', 'excel');
        
        $query = $this->buildExportQuery($request);
        $logs = $query->get();

        if ($format === 'excel') {
            return $this->exportExcel($logs);
        } elseif ($format === 'pdf') {
            return $this->exportPdf($logs);
        }

        return back()->withErrors(['format' => 'Invalid export format.']);
    }

    private function buildExportQuery(Request $request)
    {
        $query = ActivityLog::with(['user', 'file', 'transfer']);

        if (!Auth::user()->isSuperAdmin()) {
            if (Auth::user()->isDepartmentAdmin()) {
                $query->whereHas('user', function($q) {
                    $q->where('department_id', Auth::user()->department_id);
                });
            } else {
                $query->where('user_id', Auth::id());
            }
        }

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

        return $query->latest();
    }

    private function exportExcel($logs)
    {
        return Excel::download(new class($logs) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $logs;
            
            public function __construct($logs) 
            { 
                $this->logs = $logs; 
            }
            
            public function headings(): array
            {
                return [
                    'Date/Time',
                    'User',
                    'Action',
                    'Module',
                    'Description',
                    'IP Address',
                    'Device',
                    'Browser',
                    'Location'
                ];
            }
            
            public function array(): array 
            {
                $data = [];
                foreach ($this->logs as $log) {
                    $data[] = [
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->user->name ?? 'System',
                        ucfirst(str_replace('_', ' ', $log->action)),
                        ucfirst($log->module),
                        $log->description,
                        $log->ip_address ?? 'N/A',
                        $log->device_type ?? 'N/A',
                        $log->browser ?? 'N/A',
                        $log->location ?? 'N/A'
                    ];
                }
                return $data;
            }
        }, 'activity-logs.xlsx');
    }

    private function exportPdf($logs)
    {
        $pdf = Pdf::loadView('logs.export-pdf', compact('logs'));
        return $pdf->download('activity-logs.pdf');
    }

    public function stats()
    {
        $this->authorize('viewStats', ActivityLog::class);

        $stats = [
            'total_today' => ActivityLog::whereDate('created_at', today())->count(),
            'total_week' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'total_month' => ActivityLog::whereMonth('created_at', now()->month)->count(),
            'by_action' => ActivityLog::select('action', DB::raw('count(*) as count'))
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->get(),
            'by_module' => ActivityLog::select('module', DB::raw('count(*) as count'))
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->groupBy('module')
                ->orderBy('count', 'desc')
                ->get(),
            'by_hour' => ActivityLog::select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as count'))
                ->whereDate('created_at', today())
                ->groupBy('hour')
                ->orderBy('hour')
                ->get(),
            'top_users' => ActivityLog::select('user_id', DB::raw('count(*) as count'))
                ->with('user')
                ->whereDate('created_at', '>=', now()->subDays(30))
                ->groupBy('user_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('logs.stats', compact('stats'));
    }

    public function cleanup(Request $request)
    {
        $this->authorize('cleanup', ActivityLog::class);

        $days = $request->get('days', 90);
        
        $count = ActivityLog::whereDate('created_at', '<=', now()->subDays($days))->delete();

        return redirect()->route('logs.index')
            ->with('success', "Deleted {$count} logs older than {$days} days.");
    }
}