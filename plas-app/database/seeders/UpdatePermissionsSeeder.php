<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdatePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder adds missing permissions without affecting existing ones.
     */
    public function run(): void
    {
        // Set up logging
        Log::info('Starting UpdatePermissionsSeeder');
        
        // Define all expected permissions
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
            
            // Translation permissions
            'translations' => [
                'manage_translations' => 'Manage translations',
            ],
            
            // Resource permissions
            'resources' => [
                'view-resources' => 'View resources',
                'create-resources' => 'Create new resources',
                'edit-resources' => 'Edit resources',
                'delete-resources' => 'Delete resources',
            ],
            
            // Resource category permissions
            'resource-categories' => [
                'view-resource-categories' => 'View resource categories',
                'create-resource-categories' => 'Create new resource categories',
                'edit-resource-categories' => 'Edit resource categories',
                'delete-resource-categories' => 'Delete resource categories',
            ],
        ];

        // Get existing permissions
        $existingPermissions = Permission::pluck('slug')->toArray();
        $addedPermissions = [];
        $existingCount = count($existingPermissions);
        
        Log::info("Found {$existingCount} existing permissions");

        // Add only missing permissions
        foreach ($permissionModules as $module => $permissions) {
            foreach ($permissions as $slug => $name) {
                if (!in_array($slug, $existingPermissions)) {
                    Permission::create([
                        'name' => $name,
                        'slug' => $slug,
                        'module' => $module,
                    ]);
                    $addedPermissions[] = $slug;
                    Log::info("Added missing permission: {$slug}");
                }
            }
        }

        Log::info("Added " . count($addedPermissions) . " missing permissions");

        // Get roles
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $admin = Role::where('slug', 'admin')->first();
        $editor = Role::where('slug', 'editor')->first();
        $viewer = Role::where('slug', 'viewer')->first();

        if (!$superAdmin || !$admin || !$editor || !$viewer) {
            Log::error("One or more required roles are missing. Please run the RoleAndPermissionSeeder first.");
            return;
        }

        // Get all permissions
        $allPermissions = Permission::all();
        
        // Get existing role-permission assignments
        $existingRolePermissions = DB::table('role_permission')->get();
        $existingRolePermMap = [];
        
        foreach ($existingRolePermissions as $rp) {
            $key = $rp->role_id . '-' . $rp->permission_id;
            $existingRolePermMap[$key] = true;
        }
        
        Log::info("Found " . count($existingRolePermMap) . " existing role-permission assignments");

        // Function to sync missing permissions for a role
        $syncMissingPermissions = function($role, $permissions) use ($existingRolePermMap) {
            $added = 0;
            foreach ($permissions as $permission) {
                $key = $role->id . '-' . $permission->id;
                if (!isset($existingRolePermMap[$key])) {
                    DB::table('role_permission')->insert([
                        'role_id' => $role->id,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $added++;
                }
            }
            return $added;
        };

        // Super admin gets all permissions
        $addedSuperAdmin = $syncMissingPermissions($superAdmin, $allPermissions);
        Log::info("Added {$addedSuperAdmin} missing permissions to Super Admin role");

        // Admin gets all permissions except role management
        $adminPermissions = Permission::whereNot('module', 'roles')->get();
        $addedAdmin = $syncMissingPermissions($admin, $adminPermissions);
        Log::info("Added {$addedAdmin} missing permissions to Admin role");

        // Editor gets content permissions (providers, news, faqs, resources) but not user/role management
        $editorPermissions = Permission::whereIn('module', ['providers', 'news', 'faqs', 'resources', 'resource-categories'])->get();
        $addedEditor = $syncMissingPermissions($editor, $editorPermissions);
        Log::info("Added {$addedEditor} missing permissions to Editor role");

        // Viewer gets only view permissions
        $viewerPermissions = Permission::where('slug', 'like', 'view-%')->get();
        $addedViewer = $syncMissingPermissions($viewer, $viewerPermissions);
        Log::info("Added {$addedViewer} missing permissions to Viewer role");

        Log::info("Permission update completed successfully");
    }
} 