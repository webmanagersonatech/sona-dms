<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Jenssegers\Agent\Agent;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:viewAny,App\Models\Permission');
    }

    /**
     * Display a listing of permissions.
     */
    public function index(Request $request)
    {
        $query = Permission::withCount('roles');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%')
                  ->orWhere('module', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        $permissions = $query->orderBy('module')->orderBy('name')->paginate(25);

        $modules = Permission::select('module')->distinct()->pluck('module');
        $stats = [
            'total' => Permission::count(),
            'modules' => Permission::select('module')->distinct()->count(),
        ];

        return view('permissions.index', compact('permissions', 'modules', 'stats'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        $this->authorize('create', Permission::class);
        
        $modules = Permission::select('module')->distinct()->pluck('module');
        
        return view('permissions.create', compact('modules'));
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Permission::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions|regex:/^[a-z0-9-]+$/',
            'module' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $permission = Permission::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'module' => $request->module,
                'description' => $request->description,
            ]);

            // Auto-assign to Super Admin role
            $superAdmin = Role::where('slug', 'super-admin')->first();
            if ($superAdmin) {
                $superAdmin->permissions()->attach($permission->id);
            }

            $this->logActivity(
                Auth::user(),
                $request,
                'create',
                'permission',
                'Created permission: ' . $permission->name,
                null,
                null,
                null,
                $permission->toArray()
            );

            DB::commit();

            return redirect()->route('permissions.index')
                ->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create permission: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        $this->authorize('view', $permission);

        $permission->load('roles');
        $rolesCount = $permission->roles()->count();

        return view('permissions.show', compact('permission', 'rolesCount'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        $this->authorize('update', $permission);
        
        $modules = Permission::select('module')->distinct()->pluck('module');
        
        return view('permissions.edit', compact('permission', 'modules'));
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission)
    {
        $this->authorize('update', $permission);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id), 'regex:/^[a-z0-9-]+$/'],
            'module' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $oldData = $permission->toArray();
            
            $permission->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'module' => $request->module,
                'description' => $request->description,
            ]);

            $this->logActivity(
                Auth::user(),
                $request,
                'update',
                'permission',
                'Updated permission: ' . $permission->name,
                $oldData,
                null,
                null,
                $permission->toArray()
            );

            DB::commit();

            return redirect()->route('permissions.show', $permission)
                ->with('success', 'Permission updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update permission: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Request $request, Permission $permission)
    {
        $this->authorize('delete', $permission);

        if ($permission->roles()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete permission that is assigned to roles.']);
        }

        DB::beginTransaction();
        try {
            $oldData = $permission->toArray();
            $permission->delete();

            $this->logActivity(
                Auth::user(),
                $request,
                'delete',
                'permission',
                'Deleted permission: ' . $permission->name,
                $oldData
            );

            DB::commit();

            return redirect()->route('permissions.index')
                ->with('success', 'Permission deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete permission: ' . $e->getMessage()]);
        }
    }

    /**
     * Log activity for permission actions.
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