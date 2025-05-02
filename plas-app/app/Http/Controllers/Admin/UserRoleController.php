<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserRoleController extends Controller
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
        
        // All user role management requires proper permissions
        $this->middleware('permission:view-users')->only(['index']);
        $this->middleware('permission:manage-user-roles')->only(['edit', 'update']);
    }
    
    /**
     * Display a listing of users with their roles.
     */
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified user's roles.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();
        
        return view('admin.users.roles', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified user's roles.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        // Store original roles for logging
        $originalRoles = $user->roles->pluck('id')->toArray();
        
        $user->roles()->sync($validated['roles'] ?? []);
        
        // Log the user role update
        $newRoles = $validated['roles'] ?? [];
        $roleChanges = [
            'original_roles' => $originalRoles,
            'new_roles' => $newRoles,
        ];
        
        $this->activityLogService->log(
            'updated_roles',
            $user,
            'Updated user roles',
            ['roles' => $originalRoles],
            ['roles' => $newRoles]
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User roles updated successfully.');
    }

    /**
     * Display activity logs for users.
     */
    public function activity(Request $request)
    {
        // Check permission
        if (!auth()->user()->can('view-activity-logs')) {
            abort(403, 'Unauthorized action.');
        }

        $query = \App\Models\ActivityLog::with('user')
            ->where('entity_type', \App\Models\User::class)
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
        $actions = \App\Models\ActivityLog::where('entity_type', \App\Models\User::class)
            ->distinct()
            ->pluck('action');
        
        return view('admin.users.activity', compact('logs', 'actions'));
    }
} 