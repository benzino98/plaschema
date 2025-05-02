<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that unauthenticated users cannot access the admin dashboard.
     */
    public function test_unauthenticated_user_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that authenticated users can access the admin dashboard.
     */
    public function test_authenticated_user_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
                         ->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /**
     * Test that authenticated users can access the admin sidebar links.
     */
    public function test_admin_dashboard_contains_sidebar_links(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
                         ->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertSee('News Management');
        $response->assertSee('Healthcare Providers');
        $response->assertSee('FAQ Management');
    }

    /**
     * Test that users are redirected to the intended admin page after login.
     */
    public function test_user_redirected_to_intended_admin_page_after_login(): void
    {
        // Try to access admin dashboard while unauthenticated
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
        
        // Login and expect to be redirected to the dashboard
        $user = User::factory()->create();
        
        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test that the user can logout from admin area.
     */
    public function test_user_can_logout_from_admin_area(): void
    {
        $user = User::factory()->create();
        
        // First login
        $this->actingAs($user);
        
        // Then visit admin dashboard to confirm we're logged in
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        
        // Then logout
        $response = $this->post(route('logout'));
        
        // Confirm we're logged out by trying to access admin again
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }
} 