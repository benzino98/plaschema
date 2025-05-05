<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing roles and permissions to avoid duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('role_permission')->truncate();
        DB::table('user_role')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Create default roles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Has access to all features',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Has access to most administrative features',
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Can create and edit content but not manage users or roles',
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'description' => 'Can only view content in the admin panel',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        // Create permissions
        $permissionModules = [
            // User management permissions
            'users' => [
                'view-users' => 'View users',
                'create-users' => 'Create new users',
                'edit-users' => 'Edit existing users',
                'delete-users' => 'Delete users',
                'manage-user-roles' => 'Manage user roles',
            ],
            
            // Role management permissions
            'roles' => [
                'view-roles' => 'View roles',
                'create-roles' => 'Create new roles',
                'edit-roles' => 'Edit existing roles',
                'delete-roles' => 'Delete roles',
                'manage-role-permissions' => 'Manage role permissions',
            ],
            
            // Healthcare provider permissions
            'providers' => [
                'view-providers' => 'View healthcare providers',
                'create-providers' => 'Create new healthcare providers',
                'edit-providers' => 'Edit healthcare providers',
                'delete-providers' => 'Delete healthcare providers',
            ],
            
            // News permissions
            'news' => [
                'view-news' => 'View news articles',
                'create-news' => 'Create new news articles',
                'edit-news' => 'Edit news articles',
                'delete-news' => 'Delete news articles',
            ],
            
            // FAQ permissions
            'faqs' => [
                'view-faqs' => 'View FAQs',
                'create-faqs' => 'Create new FAQs',
                'edit-faqs' => 'Edit FAQs',
                'delete-faqs' => 'Delete FAQs',
            ],
            
            // Activity log permissions
            'activity-logs' => [
                'view-activity-logs' => 'View activity logs',
            ],
            
            // Analytics permissions
            'analytics' => [
                'view-analytics' => 'View analytics dashboard',
                'generate-reports' => 'Generate analytics reports',
            ],
        ];

        // Create all permissions
        foreach ($permissionModules as $module => $permissions) {
            foreach ($permissions as $slug => $name) {
                Permission::create([
                    'name' => $name,
                    'slug' => $slug,
                    'module' => $module,
                ]);
            }
        }

        // Assign permissions to roles
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $admin = Role::where('slug', 'admin')->first();
        $editor = Role::where('slug', 'editor')->first();
        $viewer = Role::where('slug', 'viewer')->first();

        // Super admin gets all permissions
        $allPermissions = Permission::all();
        $superAdmin->permissions()->sync($allPermissions->pluck('id')->toArray());

        // Admin gets all permissions except role management
        $adminPermissions = Permission::whereNot('module', 'roles')->get();
        $admin->permissions()->sync($adminPermissions->pluck('id')->toArray());

        // Editor gets content permissions (providers, news, faqs) but not user/role management
        $editorPermissions = Permission::whereIn('module', ['providers', 'news', 'faqs'])->get();
        $editor->permissions()->sync($editorPermissions->pluck('id')->toArray());

        // Viewer gets only view permissions
        $viewerPermissions = Permission::where('slug', 'like', 'view-%')->get();
        $viewer->permissions()->sync($viewerPermissions->pluck('id')->toArray());

        // Assign super-admin role to the first user
        $adminUser = User::where('email', 'test@example.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($superAdmin);
        }
    }
} 