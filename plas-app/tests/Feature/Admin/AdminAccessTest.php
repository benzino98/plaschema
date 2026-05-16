<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function createAdminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('slug', 'super-admin')->first());

        return $user;
    }

    /**
     * Test that unauthenticated users cannot access the admin dashboard.
     */
    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test that /admin sends guests to the login page.
     */
    public function test_admin_entry_redirects_guests_to_login(): void
    {
        $response = $this->get(route('admin.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test that authenticated admin users can access the admin dashboard.
     */
    public function test_authenticated_admin_user_can_access_admin_dashboard(): void
    {
        $user = $this->createAdminUser();

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /**
     * Test that users without admin roles cannot access the admin dashboard.
     */
    public function test_non_admin_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test that authenticated admin users can access the admin sidebar links.
     */
    public function test_admin_dashboard_contains_sidebar_links(): void
    {
        $user = $this->createAdminUser();

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('News Management');
        $response->assertSee('Healthcare Providers');
        $response->assertSee('FAQ Management');
    }

    /**
     * Test that admin users are redirected to the admin dashboard after login.
     */
    public function test_admin_user_redirected_to_admin_dashboard_after_login(): void
    {
        $user = $this->createAdminUser();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test that the user can logout from admin area.
     */
    public function test_user_can_logout_from_admin_area(): void
    {
        $user = $this->createAdminUser();

        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $response = $this->post(route('logout'));

        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }
} 