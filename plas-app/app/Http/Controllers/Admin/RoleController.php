<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * The activity log service instance.
     */
    protected $activityLogService;

    /**
     * Create a new controller instance.
     */
    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
        
        // All role management requires proper permissions
        $this->middleware('permission:view-roles')->only(['index', 'show']);
        $this->middleware('permission:create-roles')->only(['create', 'store']);
        $this->middleware('permission:edit-roles')->only(['edit', 'update']);
        $this->middleware('permission:delete-roles')->only(['destroy']);
        $this->middleware('permission:manage-role-permissions')->only(['edit', 'update']);
    }

    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::withCount('users')->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::orderBy('module')->get()->groupBy('module');
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }
        
        // Log the role creation
        $this->activityLogService->logCreated($role, 'Created role with ' . count($validated['permissions'] ?? []) . ' permissions');

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('module')->get()->groupBy('module');
        $role->load('permissions');
        $selectedPermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'selectedPermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);
        
        // Store original values for logging
        $originalValues = $role->getAttributes();
        $originalPermissions = $role->permissions->pluck('id')->toArray();

        $role->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);
        
        // Log the role update with permission changes
        $newPermissions = $validated['permissions'] ?? [];
        $permissionDescription = 'Updated role details';
        
        if (count(array_diff($newPermissions, $originalPermissions)) > 0 || 
            count(array_diff($originalPermissions, $newPermissions)) > 0) {
            $permissionDescription .= ' and changed permissions';
        }
        
        $this->activityLogService->logUpdated($role, $originalValues, $permissionDescription);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Don't allow deletion if users are assigned to this role
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete role with assigned users.');
        }
        
        // Log the role deletion before it's deleted
        $this->activityLogService->logDeleted($role, 'Deleted role with ' . $role->permissions->count() . ' permissions');

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Display activity logs for roles.
     */
    public function activity(Request $request)
    {
        // Check permission
        if (!auth()->user()->can('view-activity-logs')) {
            abort(403, 'Unauthorized action.');
        }

        $query = \App\Models\ActivityLog::with('user')
            ->where('entity_type', \App\Models\Role::class)
            ->orderBy('created_at', 'desc');
        
        // Filter by action if specified
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        // Filter by date range if specified
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        $logs = $query->paginate(20)->withQueryString();
        
        // Get unique values for filters
        $actions = \App\Models\ActivityLog::where('entity_type', \App\Models\Role::class)
            ->distinct()
            ->pluck('action');
        
        return view('admin.roles.activity', compact('logs', 'actions'));
    }
} 