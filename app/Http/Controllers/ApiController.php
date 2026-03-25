<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function getStats()
    {
        $user = Auth::user();

        $stats = [];

        if ($user->isSuperAdmin()) {
            $stats = [
                'total_users' => User::count(),
                'total_files' => File::count(),
                'total_transfers' => Transfer::count(),
                'storage_used' => File::sum('file_size'),
            ];
        } elseif ($user->isDepartmentAdmin()) {
            $stats = [
                'total_users' => User::where('department_id', $user->department_id)->count(),
                'total_files' => File::where('department_id', $user->department_id)->count(),
                'total_transfers' => Transfer::whereHas('sender', function($q) use ($user) {
                    $q->where('department_id', $user->department_id);
                })->count(),
                'storage_used' => File::where('department_id', $user->department_id)->sum('file_size'),
            ];
        } else {
            $stats = [
                'my_files' => File::where('owner_id', $user->id)->count(),
                'shared_files' => $user->sharedFiles()->count(),
                'my_transfers' => Transfer::where('sender_id', $user->id)->count(),
                'pending_transfers' => Transfer::where('receiver_id', $user->id)->where('status', 'pending')->count(),
            ];
        }

        return response()->json($stats);
    }

    public function getRecentActivities()
    {
        $user = Auth::user();

        $activities = \App\Models\ActivityLog::with('user')
            ->where('user_id', $user->id)
            ->orWhereHas('user', function($q) use ($user) {
                if ($user->isDepartmentAdmin()) {
                    $q->where('department_id', $user->department_id);
                }
            })
            ->latest()
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
                ];
            });

        return response()->json($activities);
    }

    public function getChartData(Request $request)
    {
        $type = $request->get('type', 'files');
        $period = $request->get('period', 'week');

        $data = [];

        switch ($type) {
            case 'files':
                $data = $this->getFileChartData($period);
                break;
            case 'transfers':
                $data = $this->getTransferChartData($period);
                break;
            case 'users':
                $data = $this->getUserChartData($period);
                break;
            case 'activities':
                $data = $this->getActivityChartData($period);
                break;
        }

        return response()->json($data);
    }

    private function getFileChartData($period)
    {
        $query = File::query();

        if ($period === 'week') {
            $dates = collect(range(6, 0))->map(function($days) {
                return now()->subDays($days)->format('Y-m-d');
            });

            $data = $dates->map(function($date) use ($query) {
                return $query->whereDate('created_at', $date)->count();
            });

            return [
                'labels' => $dates->map(function($date) {
                    return \Carbon\Carbon::parse($date)->format('D');
                }),
                'data' => $data
            ];
        } elseif ($period === 'month') {
            $dates = collect(range(29, 0))->map(function($days) {
                return now()->subDays($days)->format('Y-m-d');
            });

            $data = $dates->map(function($date) use ($query) {
                return $query->whereDate('created_at', $date)->count();
            });

            return [
                'labels' => $dates->map(function($date) {
                    return \Carbon\Carbon::parse($date)->format('M d');
                }),
                'data' => $data
            ];
        }

        return ['labels' => [], 'data' => []];
    }

    private function getTransferChartData($period)
    {
        $query = Transfer::query();

        if ($period === 'week') {
            $dates = collect(range(6, 0))->map(function($days) {
                return now()->subDays($days)->format('Y-m-d');
            });

            $data = $dates->map(function($date) use ($query) {
                return $query->whereDate('created_at', $date)->count();
            });

            return [
                'labels' => $dates->map(function($date) {
                    return \Carbon\Carbon::parse($date)->format('D');
                }),
                'data' => $data
            ];
        }

        return ['labels' => [], 'data' => []];
    }

    private function getUserChartData($period)
    {
        $query = User::query();

        if ($period === 'week') {
            $dates = collect(range(6, 0))->map(function($days) {
                return now()->subDays($days)->format('Y-m-d');
            });

            $data = $dates->map(function($date) use ($query) {
                return $query->whereDate('created_at', $date)->count();
            });

            return [
                'labels' => $dates->map(function($date) {
                    return \Carbon\Carbon::parse($date)->format('D');
                }),
                'data' => $data
            ];
        }

        return ['labels' => [], 'data' => []];
    }

    private function getActivityChartData($period)
    {
        $query = \App\Models\ActivityLog::query();

        if ($period === 'week') {
            $dates = collect(range(6, 0))->map(function($days) {
                return now()->subDays($days)->format('Y-m-d');
            });

            $data = $dates->map(function($date) use ($query) {
                return $query->whereDate('created_at', $date)->count();
            });

            return [
                'labels' => $dates->map(function($date) {
                    return \Carbon\Carbon::parse($date)->format('D');
                }),
                'data' => $data
            ];
        }

        return ['labels' => [], 'data' => []];
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
        ];

        return $icons[$action] ?? 'bi-circle';
    }

    public function validateField(Request $request)
    {
        $field = $request->get('field');
        $value = $request->get('value');
        $rules = $request->get('rules', []);

        $validator = \Validator::make([$field => $value], [
            $field => $rules
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => $validator->errors()->first($field)
            ]);
        }

        return response()->json(['valid' => true]);
    }
}