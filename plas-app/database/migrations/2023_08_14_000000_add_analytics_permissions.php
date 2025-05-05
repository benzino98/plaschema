<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;
use App\Models\Role;

class AddAnalyticsPermissions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create analytics permissions
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
            Permission::create($permissionData);
        }

        // Assign permissions to roles
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $admin = Role::where('slug', 'admin')->first();

        if ($superAdmin) {
            $analyticsPermissions = Permission::where('module', 'analytics')->get();
            $superAdmin->permissions()->attach($analyticsPermissions->pluck('id')->toArray());
        }

        if ($admin) {
            $analyticsPermissions = Permission::where('module', 'analytics')->get();
            $admin->permissions()->attach($analyticsPermissions->pluck('id')->toArray());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Find and delete analytics permissions
        $analyticsPermissions = Permission::where('module', 'analytics')->get();
        
        // Detach permissions from roles
        foreach ($analyticsPermissions as $permission) {
            $permission->roles()->detach();
            $permission->delete();
        }
    }
} 