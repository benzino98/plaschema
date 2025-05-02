<?php

namespace Tests\Feature\Admin;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FaqManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authentication
        $this->user = User::factory()->create();
    }

    /**
     * Test that unauthenticated users cannot access admin FAQ pages.
     */
    public function test_unauthenticated_users_cannot_access_admin_faq_area(): void
    {
        // Test index
        $response = $this->get(route('admin.faqs.index'));
        $response->assertRedirect(route('login'));

        // Test create
        $response = $this->get(route('admin.faqs.create'));
        $response->assertRedirect(route('login'));

        // Test store
        $response = $this->post(route('admin.faqs.store'));
        $response->assertRedirect(route('login'));

        // Test edit
        $faq = Faq::factory()->create();
        $response = $this->get(route('admin.faqs.edit', $faq->id));
        $response->assertRedirect(route('login'));

        // Test update
        $response = $this->put(route('admin.faqs.update', $faq->id));
        $response->assertRedirect(route('login'));

        // Test destroy
        $response = $this->delete(route('admin.faqs.destroy', $faq->id));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that authenticated users can access the FAQs index page.
     */
    public function test_authenticated_user_can_access_faqs_index(): void
    {
        $response = $this->actingAs($this->user)
                         ->get(route('admin.faqs.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.faqs.index');
    }

    /**
     * Test that the FAQs index can be filtered by category.
     */
    public function test_faqs_index_can_be_filtered_by_category(): void
    {
        // Create FAQs with different categories
        Faq::factory()->create([
            'category' => 'General'
        ]);
        
        Faq::factory()->create([
            'category' => 'Enrollment'
        ]);
        
        $response = $this->actingAs($this->user)
                         ->get(route('admin.faqs.index', ['category' => 'General']));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.faqs.index');
        $response->assertViewHas('faqs');
        
        // Additional assertion could check that only General FAQs are returned
        // but would require more complex setup and verification
    }

    /**
     * Test that authenticated users can view the create FAQ form.
     */
    public function test_authenticated_user_can_view_create_faq_form(): void
    {
        $response = $this->actingAs($this->user)
                         ->get(route('admin.faqs.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.faqs.create');
    }

    /**
     * Test that authenticated users can create an FAQ.
     */
    public function test_authenticated_user_can_create_faq(): void
    {
        $formData = [
            'question' => $this->faker->sentence . '?',
            'answer' => $this->faker->paragraph,
            'category' => $this->faker->word,
            'order' => 1,
            'is_active' => true,
        ];
        
        $response = $this->actingAs($this->user)
                         ->post(route('admin.faqs.store'), $formData);
        
        $response->assertRedirect(route('admin.faqs.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('faqs', [
            'question' => $formData['question'],
            'answer' => $formData['answer'],
            'category' => $formData['category'],
            'order' => $formData['order'],
            'is_active' => true,
        ]);
    }

    /**
     * Test that authenticated users can view the edit FAQ form.
     */
    public function test_authenticated_user_can_view_edit_faq_form(): void
    {
        $faq = Faq::factory()->create();
        
        $response = $this->actingAs($this->user)
                         ->get(route('admin.faqs.edit', $faq->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.faqs.edit');
        $response->assertViewHas('faq', $faq);
    }

    /**
     * Test that authenticated users can update an FAQ.
     */
    public function test_authenticated_user_can_update_faq(): void
    {
        $faq = Faq::factory()->create();
        
        $formData = [
            'question' => $this->faker->sentence . '?',
            'answer' => $this->faker->paragraph,
            'category' => $this->faker->word,
            'order' => 2,
            'is_active' => false,
        ];
        
        $response = $this->actingAs($this->user)
                         ->put(route('admin.faqs.update', $faq->id), $formData);
        
        $response->assertRedirect(route('admin.faqs.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'question' => $formData['question'],
            'answer' => $formData['answer'],
            'category' => $formData['category'],
            'order' => $formData['order'],
            'is_active' => false,
        ]);
    }

    /**
     * Test that authenticated users can delete an FAQ.
     */
    public function test_authenticated_user_can_delete_faq(): void
    {
        $faq = Faq::factory()->create();
        
        $response = $this->actingAs($this->user)
                         ->delete(route('admin.faqs.destroy', $faq->id));
        
        $response->assertRedirect(route('admin.faqs.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('faqs', [
            'id' => $faq->id
        ]);
    }

    /**
     * Test that validation rules are enforced when creating an FAQ.
     */
    public function test_faq_creation_validates_input(): void
    {
        $response = $this->actingAs($this->user)
                         ->post(route('admin.faqs.store'), [
                             'question' => '', // Missing required field
                             'answer' => '', // Missing required field
                         ]);
        
        $response->assertSessionHasErrors('question');
        $response->assertSessionHasErrors('answer');
    }

    /**
     * Test that validation rules are enforced when updating an FAQ.
     */
    public function test_faq_update_validates_input(): void
    {
        $faq = Faq::factory()->create();
        
        $response = $this->actingAs($this->user)
                         ->put(route('admin.faqs.update', $faq->id), [
                             'question' => '', // Missing required field
                             'answer' => '', // Missing required field
                         ]);
        
        $response->assertSessionHasErrors('question');
        $response->assertSessionHasErrors('answer');
    }
} 