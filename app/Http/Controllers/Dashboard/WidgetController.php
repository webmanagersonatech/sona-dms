<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Transfer;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WidgetController extends Controller
{
    public function getStats()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return $this->getSuperAdminStats();
        } elseif ($user->isDepartmentAdmin()) {
            return $this->getDepartmentAdminStats();
        } else {
            return $this->getUserStats();
        }
    }

    private function getSuperAdminStats()
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('status', 'active')->count(),
                'new_today' => User::whereDate('created_at', today())->count(),
                'by_role' => User::select('role_id', DB::raw('count(*) as count'))
                    ->with('role')
                    ->groupBy('role_id')
                    ->get()
                    ->mapWithKeys(function($item) {
                        return [$item->role->name => $item->count];
                    }),
            ],
            'files' => [
                'total' => File::count(),
                'size' => File::sum('file_size'),
                'uploads_today' => File::whereDate('created_at', today())->count(),
                'downloads_today' => File::whereDate('last_accessed_at', today())->sum('download_count'),
                'by_type' => File::select('extension', DB::raw('count(*) as count'))
                    ->groupBy('extension')
                    ->orderBy('count', 'desc')
                    ->limit(5)
                    ->get(),
            ],
            'transfers' => [
                'total' => Transfer::count(),
                'pending' => Transfer::where('status', 'pending')->count(),
                'delivered' => Transfer::where('status', 'delivered')->count(),
                'overdue' => Transfer::where('status', '!=', 'delivered')
                    ->where('expected_delivery_time', '<', now())
                    ->count(),
            ],
            'activities' => [
                'today' => ActivityLog::whereDate('created_at', today())->count(),
                'week' => ActivityLog::whereDate('created_at', '>=', now()->subDays(7))->count(),
            ],
        ];

        return response()->json($stats);
    }

    private function getDepartmentAdminStats()
    {
        $deptId = Auth::user()->department_id;

        $stats = [
            'users' => [
                'total' => User::where('department_id', $deptId)->count(),
                'active' => User::where('department_id', $deptId)->where('status', 'active')->count(),
                'new_today' => User::where('department_id', $deptId)->whereDate('created_at', today())->count(),
            ],
            'files' => [
                'total' => File::where('department_id', $deptId)->count(),
                'size' => File::where('department_id', $deptId)->sum('file_size'),
                'uploads_today' => File::where('department_id', $deptId)->whereDate('created_at', today())->count(),
                'by_user' => File::where('department_id', $deptId)
                    ->select('owner_id', DB::raw('count(*) as count'))
                    ->with('owner')
                    ->groupBy('owner_id')
                    ->orderBy('count', 'desc')
                    ->limit(5)
                    ->get(),
            ],
            'transfers' => [
                'total' => Transfer::whereHas('sender', function($q) use ($deptId) {
                    $q->where('department_id', $deptId);
                })->count(),
                'pending' => Transfer::whereHas('sender', function($q) use ($deptId) {
                    $q->where('department_id', $deptId);
                })->where('status', 'pending')->count(),
            ],
        ];

        return response()->json($stats);
    }

    private function getUserStats()
    {
        $userId = Auth::id();

        $stats = [
            'files' => [
                'total' => File::where('owner_id', $userId)->count(),
                'size' => File::where('owner_id', $userId)->sum('file_size'),
                'shared_with_me' => Auth::user()->sharedFiles()->count(),
                'recent' => File::where('owner_id', $userId)
                    ->orWhereHas('shares', function($q) use ($userId) {
                        $q->where('shared_with', $userId);
                    })
                    ->latest()
                    ->limit(5)
                    ->get(),
            ],
            'transfers' => [
                'sent' => Transfer::where('sender_id', $userId)->count(),
                'received' => Transfer::where('receiver_id', $userId)->count(),
                'pending' => Transfer::where('receiver_id', $userId)
                    ->where('status', 'pending')
                    ->count(),
            ],
            'activities' => [
                'today' => ActivityLog::where('user_id', $userId)
                    ->whereDate('created_at', today())
                    ->count(),
            ],
        ];

        return response()->json($stats);
    }

    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'files');
        $period = $request->get('period', 'week');
        $user = Auth::user();

        switch ($type) {
            case 'files':
                return $this->getFileChartData($period, $user);
            case 'transfers':
                return $this->getTransferChartData($period, $user);
            case 'users':
                return $this->getUserChartData($period, $user);
            case 'activities':
                return $this->getActivityChartData($period, $user);
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
    }

    private function getFileChartData($period, $user)
    {
        $query = File::query();

        if (!$user->isSuperAdmin()) {
            if ($user->isDepartmentAdmin()) {
                $query->where('department_id', $user->department_id);
            } else {
                $query->where('owner_id', $user->id);
            }
        }

        return $this->formatChartData($query, $period, 'files');
    }

    private function getTransferChartData($period, $user)
    {
        $query = Transfer::query();

        if (!$user->isSuperAdmin()) {
            if ($user->isDepartmentAdmin()) {
                $query->whereHas('sender', function($q) use ($user) {
                    $q->where('department_id', $user->department_id);
                });
            } else {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            }
        }

        return $this->formatChartData($query, $period, 'transfers');
    }

    private function getUserChartData($period, $user)
    {
        if (!$user->can('viewAny', User::class)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = User::query();

        if (!$user->isSuperAdmin() && $user->isDepartmentAdmin()) {
            $query->where('department_id', $user->department_id);
        }

        return $this->formatChartData($query, $period, 'users');
    }

    private function getActivityChartData($period, $user)
    {
        $query = ActivityLog::query();

        if (!$user->isSuperAdmin()) {
            if ($user->isDepartmentAdmin()) {
                $query->whereHas('user', function($q) use ($user) {
                    $q->where('department_id', $user->department_id);
                });
            } else {
                $query->where('user_id', $user->id);
            }
        }

        return $this->formatChartData($query, $period, 'activities');
    }

    private function formatChartData($query, $period, $type)
    {
        $now = now();
        $labels = [];
        $data = [];

        switch ($period) {
            case 'day':
                for ($i = 23; $i >= 0; $i--) {
                    $hour = $now->copy()->subHours($i);
                    $labels[] = $hour->format('H:00');
                    $count = (clone $query)
                        ->whereBetween('created_at', [$hour->copy()->startOfHour(), $hour->copy()->endOfHour()])
                        ->count();
                    $data[] = $count;
                }
                break;

            case 'week':
                for ($i = 6; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i);
                    $labels[] = $date->format('D');
                    $count = (clone $query)
                        ->whereDate('created_at', $date)
                        ->count();
                    $data[] = $count;
                }
                break;

            case 'month':
                for ($i = 29; $i >= 0; $i--) {
                    $date = $now->copy()->subDays($i);
                    $labels[] = $date->format('M d');
                    $count = (clone $query)
                        ->whereDate('created_at', $date)
                        ->count();
                    $data[] = $count;
                }
                break;

            case 'year':
                for ($i = 11; $i >= 0; $i--) {
                    $date = $now->copy()->subMonths($i);
                    $labels[] = $date->format('M Y');
                    $count = (clone $query)
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
                    $data[] = $count;
                }
                break;
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ucfirst($type),
                    'data' => $data,
                    'backgroundColor' => $this->getChartColors($type),
                    'borderColor' => $this->getChartColors($type, true),
                ]
            ]
        ]);
    }

    private function getChartColors($type, $border = false)
    {
        $colors = [
            'files' => $border ? '#0d6efd' : 'rgba(13, 110, 253, 0.5)',
            'transfers' => $border ? '#198754' : 'rgba(25, 135, 84, 0.5)',
            'users' => $border ? '#ffc107' : 'rgba(255, 193, 7, 0.5)',
            'activities' => $border ? '#dc3545' : 'rgba(220, 53, 69, 0.5)',
        ];

        return $colors[$type] ?? ($border ? '#6c757d' : 'rgba(108, 117, 125, 0.5)');
    }

    public function getRecentActivities()
    {
        $user = Auth::user();

        $query = ActivityLog::with('user');

        if (!$user->isSuperAdmin()) {
            if ($user->isDepartmentAdmin()) {
                $query->whereHas('user', function($q) use ($user) {
                    $q->where('department_id', $user->department_id);
                });
            } else {
                $query->where('user_id', $user->id);
            }
        }

        $activities = $query->latest()
            ->limit(10)
            ->get()
            ->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'user' => $activity->user?->name ?? 'System',
                    'action' => $activity->action,
                    'module' => $activity->module,
                    'description' => $activity->description,
                    'time' => $activity->created_at->diffForHumans(),
                    'icon' => $this->getActivityIcon($activity->action),
                    'color' => $this->getActivityColor($activity->action),
                ];
            });

        return response()->json($activities);
    }

    private function getActivityIcon($action)
    {
        $icons = [
            'login' => 'bi-box-arrow-in-right',
            'logout' => 'bi-box-arrow-right',
            'upload' => 'bi-upload',
            'download' => 'bi-download',
            'share' => 'bi-share',
            'create' => 'bi-plus-circle',
            'update' => 'bi-pencil',
            'delete' => 'bi-trash',
            'view' => 'bi-eye',
            'archive' => 'bi-archive',
            'restore' => 'bi-arrow-counterclockwise',
        ];

        return $icons[$action] ?? 'bi-circle';
    }

    private function getActivityColor($action)
    {
        $colors = [
            'login' => 'success',
            'logout' => 'secondary',
            'upload' => 'primary',
            'download' => 'info',
            'share' => 'warning',
            'create' => 'success',
            'update' => 'info',
            'delete' => 'danger',
            'view' => 'primary',
            'archive' => 'warning',
            'restore' => 'success',
        ];

        return $colors[$action] ?? 'secondary';
    }
}