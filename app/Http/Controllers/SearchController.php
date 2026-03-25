<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Department;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $user = Auth::user();
        $results = [];

        // Search files
        $fileQuery = File::with(['owner', 'department'])
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('original_name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });

        if (!$user->isSuperAdmin()) {
            if ($user->isDepartmentAdmin()) {
                $fileQuery->where('department_id', $user->department_id);
            } else {
                $fileQuery->where('owner_id', $user->id)
                    ->orWhereHas('shares', function($q) use ($user) {
                        $q->where('shared_with', $user->id);
                    });
            }
        }

        $files = $fileQuery->limit(5)->get();
        foreach ($files as $file) {
            $results[] = [
                'type' => 'file',
                'icon' => $file->icon,
                'title' => $file->name,
                'subtitle' => 'File • ' . $file->size_for_humans,
                'url' => route('files.show', $file),
                'badge' => strtoupper($file->extension)
            ];
        }

        // Search transfers
        if ($user->can('viewAny', Transfer::class)) {
            $transferQuery = Transfer::with(['sender', 'receiver'])
                ->where(function($q) use ($query) {
                    $q->where('transfer_id', 'like', "%{$query}%")
                      ->orWhere('purpose', 'like', "%{$query}%")
                      ->orWhere('receiver_name', 'like', "%{$query}%");
                });

            if (!$user->isSuperAdmin()) {
                $transferQuery->where(function($q) use ($user) {
                    $q->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
                });
            }

            $transfers = $transferQuery->limit(5)->get();
            foreach ($transfers as $transfer) {
                $results[] = [
                    'type' => 'transfer',
                    'icon' => 'bi-truck',
                    'title' => $transfer->transfer_id,
                    'subtitle' => 'Transfer • ' . $transfer->purpose,
                    'url' => route('transfers.show', $transfer),
                    'badge' => ucfirst($transfer->status)
                ];
            }
        }

        // Search users
        if ($user->can('viewAny', User::class)) {
            $userQuery = User::with(['role', 'department'])
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%");
                });

            if (!$user->isSuperAdmin()) {
                $userQuery->where('department_id', $user->department_id);
            }

            $users = $userQuery->limit(5)->get();
            foreach ($users as $searchUser) {
                $results[] = [
                    'type' => 'user',
                    'icon' => 'bi-person',
                    'title' => $searchUser->name,
                    'subtitle' => $searchUser->email . ' • ' . ($searchUser->role->name ?? 'User'),
                    'url' => route('users.show', $searchUser),
                    'badge' => ucfirst($searchUser->status)
                ];
            }
        }

        // Search departments
        if ($user->can('viewAny', Department::class)) {
            $departments = Department::where('name', 'like', "%{$query}%")
                ->orWhere('code', 'like', "%{$query}%")
                ->limit(5)
                ->get();

            foreach ($departments as $dept) {
                $results[] = [
                    'type' => 'department',
                    'icon' => 'bi-building',
                    'title' => $dept->name,
                    'subtitle' => 'Department • ' . $dept->code,
                    'url' => route('departments.show', $dept),
                    'badge' => ucfirst($dept->status)
                ];
            }
        }

        return response()->json([
            'results' => $results,
            'total' => count($results)
        ]);
    }

    public function advanced(Request $request)
    {
        $type = $request->get('type', 'all');
        $query = $request->get('q');
        $filters = $request->except(['type', 'q']);

        $user = Auth::user();
        $results = [];

        switch ($type) {
            case 'files':
                $results = $this->searchFiles($query, $filters, $user);
                break;
            case 'transfers':
                $results = $this->searchTransfers($query, $filters, $user);
                break;
            case 'users':
                $results = $this->searchUsers($query, $filters, $user);
                break;
            case 'departments':
                $results = $this->searchDepartments($query, $filters, $user);
                break;
            default:
                $results = [
                    'files' => $this->searchFiles($query, $filters, $user, 5),
                    'transfers' => $this->searchTransfers($query, $filters, $user, 5),
                    'users' => $this->searchUsers($query, $filters, $user, 5),
                    'departments' => $this->searchDepartments($query, $filters, $user, 5),
                ];
        }

        return view('search.results', compact('results', 'type', 'query'));
    }

    private function searchFiles($query, $filters, $user, $limit = null)
    {
        $fileQuery = File::with(['owner', 'department']);

        if ($query) {
            $fileQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('original_name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        }

        // Apply filters
        if (!empty($filters['department_id'])) {
            $fileQuery->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['file_type'])) {
            if ($filters['file_type'] === 'image') {
                $fileQuery->whereIn('extension', ['jpg', 'jpeg', 'png', 'gif']);
            } elseif ($filters['file_type'] === 'document') {
                $fileQuery->whereIn('extension', ['pdf', 'doc', 'docx', 'txt']);
            }
        }

        if (!empty($filters['date_from'])) {
            $fileQuery->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $fileQuery->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Apply permissions
        if (!$user->isSuperAdmin()) {
            if ($user->isDepartmentAdmin()) {
                $fileQuery->where('department_id', $user->department_id);
            } else {
                $fileQuery->where('owner_id', $user->id)
                    ->orWhereHas('shares', function($q) use ($user) {
                        $q->where('shared_with', $user->id);
                    });
            }
        }

        if ($limit) {
            $fileQuery->limit($limit);
        }

        return $fileQuery->orderBy('created_at', 'desc')->get();
    }

    private function searchTransfers($query, $filters, $user, $limit = null)
    {
        if (!$user->can('viewAny', Transfer::class)) {
            return collect();
        }

        $transferQuery = Transfer::with(['sender', 'receiver']);

        if ($query) {
            $transferQuery->where(function($q) use ($query) {
                $q->where('transfer_id', 'like', "%{$query}%")
                  ->orWhere('purpose', 'like', "%{$query}%")
                  ->orWhere('receiver_name', 'like', "%{$query}%");
            });
        }

        // Apply filters
        if (!empty($filters['status'])) {
            $transferQuery->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $transferQuery->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $transferQuery->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Apply permissions
        if (!$user->isSuperAdmin()) {
            $transferQuery->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            });
        }

        if ($limit) {
            $transferQuery->limit($limit);
        }

        return $transferQuery->orderBy('created_at', 'desc')->get();
    }

    private function searchUsers($query, $filters, $user, $limit = null)
    {
        if (!$user->can('viewAny', User::class)) {
            return collect();
        }

        $userQuery = User::with(['role', 'department']);

        if ($query) {
            $userQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            });
        }

        // Apply filters
        if (!empty($filters['role_id'])) {
            $userQuery->where('role_id', $filters['role_id']);
        }

        if (!empty($filters['department_id'])) {
            $userQuery->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['status'])) {
            $userQuery->where('status', $filters['status']);
        }

        // Apply permissions
        if (!$user->isSuperAdmin()) {
            $userQuery->where('department_id', $user->department_id);
        }

        if ($limit) {
            $userQuery->limit($limit);
        }

        return $userQuery->orderBy('name')->get();
    }

    private function searchDepartments($query, $filters, $user, $limit = null)
    {
        if (!$user->can('viewAny', Department::class)) {
            return collect();
        }

        $deptQuery = Department::query();

        if ($query) {
            $deptQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('code', 'like', "%{$query}%");
            });
        }

        // Apply filters
        if (!empty($filters['status'])) {
            $deptQuery->where('status', $filters['status']);
        }

        if ($limit) {
            $deptQuery->limit($limit);
        }

        return $deptQuery->orderBy('name')->get();
    }
}