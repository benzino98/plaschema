<?php

namespace Tests\Feature\Admin;

use App\Models\HealthcareProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HealthcareProviderManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authentication
        $this->user = User::factory()->create();
        
        // Setup fake storage for image uploads
        Storage::fake('public');
    }

    /**
     * Test that unauthenticated users cannot access admin provider pages.
     */
    public function test_unauthenticated_users_cannot_access_admin_provider_area(): void
    {
        // Test index
        $response = $this->get(route('admin.providers.index'));
        $response->assertRedirect(route('login'));

        // Test create
        $response = $this->get(route('admin.providers.create'));
        $response->assertRedirect(route('login'));

        // Test store
        $response = $this->post(route('admin.providers.store'));
        $response->assertRedirect(route('login'));

        // Test edit
        $provider = HealthcareProvider::factory()->create();
        $response = $this->get(route('admin.providers.edit', $provider->id));
        $response->assertRedirect(route('login'));

        // Test update
        $response = $this->put(route('admin.providers.update', $provider->id));
        $response->assertRedirect(route('login'));

        // Test destroy
        $response = $this->delete(route('admin.providers.destroy', $provider->id));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that authenticated users can access the providers index page.
     */
    public function test_authenticated_user_can_access_providers_index(): void
    {
        $response = $this->actingAs($this->user)
                         ->get(route('admin.providers.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.providers.index');
    }

    /**
     * Test that authenticated users can view the create provider form.
     */
    public function test_authenticated_user_can_view_create_provider_form(): void
    {
        $response = $this->actingAs($this->user)
                         ->get(route('admin.providers.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.providers.create');
    }

    /**
     * Test that authenticated users can create a healthcare provider.
     */
    public function test_authenticated_user_can_create_provider(): void
    {
        $image = UploadedFile::fake()->image('provider.jpg');
        
        $formData = [
            'name' => $this->faker->company,
            'type' => $this->faker->randomElement(['Hospital', 'Clinic', 'Primary Care']),
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'description' => $this->faker->paragraph,
            'services' => $this->faker->paragraph,
            'image' => $image,
            'is_featured' => true,
        ];
        
        $response = $this->actingAs($this->user)
                         ->post(route('admin.providers.store'), $formData);
        
        $response->assertRedirect(route('admin.providers.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('healthcare_providers', [
            'name' => $formData['name'],
            'type' => $formData['type'],
            'address' => $formData['address'],
            'city' => $formData['city'],
            'email' => $formData['email'],
            'is_featured' => true,
        ]);
        
        // Assert the image was stored
        $provider = HealthcareProvider::where('name', $formData['name'])->first();
        $this->assertNotNull($provider->image_path);
    }

    /**
     * Test that authenticated users can view the edit provider form.
     */
    public function test_authenticated_user_can_view_edit_provider_form(): void
    {
        $provider = HealthcareProvider::factory()->create();
        
        $response = $this->actingAs($this->user)
                         ->get(route('admin.providers.edit', $provider->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.providers.edit');
        $response->assertViewHas('provider', $provider);
    }

    /**
     * Test that authenticated users can update a healthcare provider.
     */
    public function test_authenticated_user_can_update_provider(): void
    {
        $provider = HealthcareProvider::factory()->create();
        
        $formData = [
            'name' => $this->faker->company,
            'type' => $this->faker->randomElement(['Hospital', 'Clinic', 'Primary Care']),
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'description' => $this->faker->paragraph,
            'services' => $this->faker->paragraph,
            'is_featured' => true,
        ];
        
        $response = $this->actingAs($this->user)
                         ->put(route('admin.providers.update', $provider->id), $formData);
        
        $response->assertRedirect(route('admin.providers.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('healthcare_providers', [
            'id' => $provider->id,
            'name' => $formData['name'],
            'type' => $formData['type'],
            'address' => $formData['address'],
            'city' => $formData['city'],
            'email' => $formData['email'],
            'is_featured' => true,
        ]);
    }

    /**
     * Test that authenticated users can update a provider with a new image.
     */
    public function test_authenticated_user_can_update_provider_with_new_image(): void
    {
        $provider = HealthcareProvider::factory()->create([
            'image_path' => 'old-image.jpg'
        ]);
        
        $image = UploadedFile::fake()->image('new-provider.jpg');
        
        $formData = [
            'name' => $this->faker->company,
            'type' => $this->faker->randomElement(['Hospital', 'Clinic', 'Primary Care']),
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'description' => $this->faker->paragraph,
            'services' => $this->faker->paragraph,
            'image' => $image,
            'is_featured' => false,
        ];
        
        $response = $this->actingAs($this->user)
                         ->put(route('admin.providers.update', $provider->id), $formData);
        
        $response->assertRedirect(route('admin.providers.index'));
        $response->assertSessionHas('success');
        
        $updatedProvider = HealthcareProvider::find($provider->id);
        $this->assertNotNull($updatedProvider->image_path);
        $this->assertNotEquals('old-image.jpg', $updatedProvider->image_path);
    }

    /**
     * Test that authenticated users can delete a healthcare provider.
     */
    public function test_authenticated_user_can_delete_provider(): void
    {
        $provider = HealthcareProvider::factory()->create();
        
        $response = $this->actingAs($this->user)
                         ->delete(route('admin.providers.destroy', $provider->id));
        
        $response->assertRedirect(route('admin.providers.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('healthcare_providers', [
            'id' => $provider->id
        ]);
    }

    /**
     * Test that validation rules are enforced when creating a healthcare provider.
     */
    public function test_provider_creation_validates_input(): void
    {
        $response = $this->actingAs($this->user)
                         ->post(route('admin.providers.store'), [
                             'name' => '', // Missing required field
                             'email' => 'not-an-email', // Invalid email format
                         ]);
        
        $response->assertSessionHasErrors('name');
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test that validation rules are enforced when updating a healthcare provider.
     */
    public function test_provider_update_validates_input(): void
    {
        $provider = HealthcareProvider::factory()->create();
        
        $response = $this->actingAs($this->user)
                         ->put(route('admin.providers.update', $provider->id), [
                             'name' => '', // Missing required field
                             'email' => 'not-an-email', // Invalid email format
                         ]);
        
        $response->assertSessionHasErrors('name');
        $response->assertSessionHasErrors('email');
    }
} 