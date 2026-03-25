<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Jenssegers\Agent\Agent;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:viewAny,App\Models\Role');
    }

    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $query = Role::withCount('users', 'permissions');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $roles = $query->orderBy('name')->paginate(15);

        $stats = [
            'total' => Role::count(),
            'with_users' => Role::has('users')->count(),
            'total_permissions' => Permission::count(),
        ];

        return view('roles.index', compact('roles', 'stats'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $this->authorize('create', Role::class);
        
        $permissions = Permission::all()->groupBy('module');
        
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'slug' => 'required|string|max:255|unique:roles|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
            ]);

            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }

            $this->logActivity(
                Auth::user(),
                $request,
                'create',
                'role',
                'Created role: ' . $role->name,
                null,
                null,
                null,
                $role->toArray()
            );

            DB::commit();

            return redirect()->route('roles.index')
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create role: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $this->authorize('view', $role);

        $role->load(['users' => function($q) {
            $q->limit(10);
        }, 'permissions']);

        $permissionsByModule = $role->permissions->groupBy('module');
        $usersCount = $role->users()->count();

        return view('roles.show', compact('role', 'permissionsByModule', 'usersCount'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        $permissions = Permission::all()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id), 'regex:/^[a-z0-9-]+$/'],
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $oldData = $role->toArray();
            
            $role->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
            ]);

            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            } else {
                $role->permissions()->detach();
            }

            $this->logActivity(
                Auth::user(),
                $request,
                'update',
                'role',
                'Updated role: ' . $role->name,
                $oldData,
                null,
                null,
                $role->toArray()
            );

            DB::commit();

            return redirect()->route('roles.show', $role)
                ->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update role: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Request $request, Role $role)
    {
        $this->authorize('delete', $role);

        if ($role->slug === 'super-admin') {
            return back()->withErrors(['error' => 'Super Admin role cannot be deleted.']);
        }

        if ($role->users()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete role with assigned users.']);
        }

        DB::beginTransaction();
        try {
            $oldData = $role->toArray();
            $role->permissions()->detach();
            $role->delete();

            $this->logActivity(
                Auth::user(),
                $request,
                'delete',
                'role',
                'Deleted role: ' . $role->name,
                $oldData
            );

            DB::commit();

            return redirect()->route('roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete role: ' . $e->getMessage()]);
        }
    }

    /**
     * Display permissions for a role.
     */
    public function permissions(Role $role)
    {
        $this->authorize('view', $role);

        $permissions = Permission::all()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update permissions for a role.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $oldPermissions = $role->permissions->pluck('id')->toArray();
            
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            } else {
                $role->permissions()->detach();
            }

            $this->logActivity(
                Auth::user(),
                $request,
                'update_permissions',
                'role',
                'Updated permissions for role: ' . $role->name,
                ['permissions' => $oldPermissions],
                null,
                null,
                ['permissions' => $request->permissions ?? []]
            );

            DB::commit();

            return redirect()->route('roles.permissions', $role)
                ->with('success', 'Role permissions updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update permissions: ' . $e->getMessage()]);
        }
    }

    /**
     * Log activity for role actions.
     */
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