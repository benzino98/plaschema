<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define resource permissions
        $resourcePermissions = [
            'resources' => [
                'view-resources' => 'View resources',
                'create-resources' => 'Create new resources',
                'edit-resources' => 'Edit resources',
                'delete-resources' => 'Delete resources',
            ],
            'resource-categories' => [
                'view-resource-categories' => 'View resource categories',
                'create-resource-categories' => 'Create new resource categories',
                'edit-resource-categories' => 'Edit resource categories',
                'delete-resource-categories' => 'Delete resource categories',
            ],
        ];

        // Create resource permissions
        foreach ($resourcePermissions as $module => $permissions) {
            foreach ($permissions as $slug => $name) {
                // Check if permission already exists
                $existingPermission = Permission::where('slug', $slug)->first();
                if (!$existingPermission) {
                    Permission::create([
                        'name' => $name,
                        'slug' => $slug,
                        'module' => $module,
                    ]);
                }
            }
        }

        // Assign permissions to roles
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $admin = Role::where('slug', 'admin')->first();
        $editor = Role::where('slug', 'editor')->first();
        
        if ($superAdmin) {
            // Get all resource permissions
            $allResourcePermissions = Permission::whereIn('module', ['resources', 'resource-categories'])->get();
            // Add them to super admin's existing permissions
            $currentPermissions = $superAdmin->permissions->pluck('id')->toArray();
            $superAdmin->permissions()->sync(array_merge($currentPermissions, $allResourcePermissions->pluck('id')->toArray()));
        }
        
        if ($admin) {
            // Get all resource permissions
            $allResourcePermissions = Permission::whereIn('module', ['resources', 'resource-categories'])->get();
            // Add them to admin's existing permissions
            $currentPermissions = $admin->permissions->pluck('id')->toArray();
            $admin->permissions()->sync(array_merge($currentPermissions, $allResourcePermissions->pluck('id')->toArray()));
        }
        
        if ($editor) {
            // Editors can view and edit resources but not delete them
            $editorResourcePermissions = Permission::whereIn('slug', [
                'view-resources', 
                'create-resources', 
                'edit-resources',
                'view-resource-categories'
            ])->get();
            // Add them to editor's existing permissions
            $currentPermissions = $editor->permissions->pluck('id')->toArray();
            $editor->permissions()->sync(array_merge($currentPermissions, $editorResourcePermissions->pluck('id')->toArray()));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the resource permissions
        $permissionSlugs = [
            'view-resources',
            'create-resources',
            'edit-resources',
            'delete-resources',
            'view-resource-categories',
            'create-resource-categories',
            'edit-resource-categories',
            'delete-resource-categories',
        ];

        // First remove from role_permission pivot table
        $permissions = Permission::whereIn('slug', $permissionSlugs)->get();
        foreach ($permissions as $permission) {
            DB::table('role_permission')->where('permission_id', $permission->id)->delete();
        }

        // Then delete the permissions
        Permission::whereIn('slug', $permissionSlugs)->delete();
    }
};
