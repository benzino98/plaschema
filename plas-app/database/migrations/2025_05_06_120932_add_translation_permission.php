<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create translation management permission
        $permission = Permission::create([
            'name' => 'Manage translations',
            'slug' => 'manage_translations',
            'module' => 'translations',
            'description' => 'Can manage and edit language translations',
        ]);

        // Assign permission to super-admin role
        $superAdmin = Role::where('slug', 'super-admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->attach($permission->id);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Find and delete the translation permission
        $permission = Permission::where('slug', 'manage_translations')->first();
        
        if ($permission) {
            // Detach the permission from all roles
            $permission->roles()->detach();
            
            // Delete the permission
            $permission->delete();
        }
    }
};
