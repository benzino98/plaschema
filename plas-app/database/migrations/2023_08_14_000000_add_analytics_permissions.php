<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

class AddAnalyticsPermissions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            [
                'name' => 'View analytics dashboard',
                'slug' => 'view-analytics',
                'module' => 'analytics',
            ],
            [
                'name' => 'Generate analytics reports',
                'slug' => 'generate-reports',
                'module' => 'analytics',
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData
            );
        }

        $superAdmin = Role::where('slug', 'super-admin')->first();
        $admin = Role::where('slug', 'admin')->first();
        $analyticsPermissionIds = Permission::where('module', 'analytics')->pluck('id')->toArray();

        if ($superAdmin && $analyticsPermissionIds !== []) {
            $current = $superAdmin->permissions()->pluck('permissions.id')->toArray();
            $superAdmin->permissions()->sync(array_values(array_unique(array_merge($current, $analyticsPermissionIds))));
        }

        if ($admin && $analyticsPermissionIds !== []) {
            $current = $admin->permissions()->pluck('permissions.id')->toArray();
            $admin->permissions()->sync(array_values(array_unique(array_merge($current, $analyticsPermissionIds))));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $analyticsPermissions = Permission::where('module', 'analytics')->get();

        foreach ($analyticsPermissions as $permission) {
            $permission->roles()->detach();
            $permission->delete();
        }
    }
}
