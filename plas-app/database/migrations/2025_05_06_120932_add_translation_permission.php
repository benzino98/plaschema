<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permission = Permission::firstOrCreate(
            ['slug' => 'manage_translations'],
            [
                'name' => 'Manage translations',
                'module' => 'translations',
                'description' => 'Can manage and edit language translations',
            ]
        );

        $superAdmin = Role::where('slug', 'super-admin')->first();

        if ($superAdmin) {
            $current = $superAdmin->permissions()->pluck('permissions.id')->toArray();
            $superAdmin->permissions()->sync(array_values(array_unique(array_merge($current, [$permission->id]))));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = Permission::where('slug', 'manage_translations')->first();

        if ($permission) {
            $permission->roles()->detach();
            $permission->delete();
        }
    }
};
