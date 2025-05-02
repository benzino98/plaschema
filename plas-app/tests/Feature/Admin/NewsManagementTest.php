<?php

namespace Tests\Feature\Admin;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewsManagementTest extends TestCase
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
     * Test that unauthenticated users cannot access admin news pages.
     */
    public function test_unauthenticated_users_cannot_access_admin_news_area(): void
    {
        // Test index
        $response = $this->get(route('admin.news.index'));
        $response->assertRedirect(route('login'));

        // Test create
        $response = $this->get(route('admin.news.create'));
        $response->assertRedirect(route('login'));

        // Test store
        $response = $this->post(route('admin.news.store'));
        $response->assertRedirect(route('login'));

        // Test edit
        $news = News::factory()->create();
        $response = $this->get(route('admin.news.edit', $news->id));
        $response->assertRedirect(route('login'));

        // Test update
        $response = $this->put(route('admin.news.update', $news->id));
        $response->assertRedirect(route('login'));

        // Test destroy
        $response = $this->delete(route('admin.news.destroy', $news->id));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that authenticated users can access the news index page.
     */
    public function test_authenticated_user_can_access_news_index(): void
    {
        $response = $this->actingAs($this->user)
                         ->get(route('admin.news.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.news.index');
    }

    /**
     * Test that authenticated users can view the create news form.
     */
    public function test_authenticated_user_can_view_create_news_form(): void
    {
        $response = $this->actingAs($this->user)
                         ->get(route('admin.news.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.news.create');
    }

    /**
     * Test that authenticated users can create a news article.
     */
    public function test_authenticated_user_can_create_news(): void
    {
        $image = UploadedFile::fake()->image('news.jpg');
        
        $formData = [
            'title' => $this->faker->sentence,
            'excerpt' => $this->faker->paragraph,
            'content' => $this->faker->paragraphs(3, true),
            'image' => $image,
            'is_featured' => true,
        ];
        
        $response = $this->actingAs($this->user)
                         ->post(route('admin.news.store'), $formData);
        
        $response->assertRedirect(route('admin.news.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('news', [
            'title' => $formData['title'],
            'excerpt' => $formData['excerpt'],
            'content' => $formData['content'],
            'is_featured' => true,
        ]);
        
        // Assert the image was stored
        $news = News::where('title', $formData['title'])->first();
        $this->assertNotNull($news->image_path);
    }

    /**
     * Test that authenticated users can view the edit news form.
     */
    public function test_authenticated_user_can_view_edit_news_form(): void
    {
        $news = News::factory()->create();
        
        $response = $this->actingAs($this->user)
                         ->get(route('admin.news.edit', $news->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.news.edit');
        $response->assertViewHas('news', $news);
    }

    /**
     * Test that authenticated users can update a news article.
     */
    public function test_authenticated_user_can_update_news(): void
    {
        $news = News::factory()->create();
        
        $formData = [
            'title' => $this->faker->sentence,
            'excerpt' => $this->faker->paragraph,
            'content' => $this->faker->paragraphs(3, true),
            'is_featured' => true,
        ];
        
        $response = $this->actingAs($this->user)
                         ->put(route('admin.news.update', $news->id), $formData);
        
        $response->assertRedirect(route('admin.news.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'title' => $formData['title'],
            'excerpt' => $formData['excerpt'],
            'content' => $formData['content'],
            'is_featured' => true,
        ]);
    }

    /**
     * Test that authenticated users can update a news article with a new image.
     */
    public function test_authenticated_user_can_update_news_with_new_image(): void
    {
        $news = News::factory()->create([
            'image_path' => 'old-image.jpg'
        ]);
        
        $image = UploadedFile::fake()->image('new-news.jpg');
        
        $formData = [
            'title' => $this->faker->sentence,
            'excerpt' => $this->faker->paragraph,
            'content' => $this->faker->paragraphs(3, true),
            'image' => $image,
            'is_featured' => false,
        ];
        
        $response = $this->actingAs($this->user)
                         ->put(route('admin.news.update', $news->id), $formData);
        
        $response->assertRedirect(route('admin.news.index'));
        $response->assertSessionHas('success');
        
        $updatedNews = News::find($news->id);
        $this->assertNotNull($updatedNews->image_path);
        $this->assertNotEquals('old-image.jpg', $updatedNews->image_path);
    }

    /**
     * Test that authenticated users can delete a news article.
     */
    public function test_authenticated_user_can_delete_news(): void
    {
        $news = News::factory()->create();
        
        $response = $this->actingAs($this->user)
                         ->delete(route('admin.news.destroy', $news->id));
        
        $response->assertRedirect(route('admin.news.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('news', [
            'id' => $news->id
        ]);
    }

    /**
     * Test that validation rules are enforced when creating a news article.
     */
    public function test_news_creation_validates_input(): void
    {
        $response = $this->actingAs($this->user)
                         ->post(route('admin.news.store'), [
                             'title' => '', // Missing required field
                             'content' => $this->faker->paragraph,
                         ]);
        
        $response->assertSessionHasErrors('title');
        $response->assertSessionHasErrors('excerpt');
    }

    /**
     * Test that validation rules are enforced when updating a news article.
     */
    public function test_news_update_validates_input(): void
    {
        $news = News::factory()->create();
        
        $response = $this->actingAs($this->user)
                         ->put(route('admin.news.update', $news->id), [
                             'title' => '', // Missing required field
                             'content' => $this->faker->paragraph,
                         ]);
        
        $response->assertSessionHasErrors('title');
        $response->assertSessionHasErrors('excerpt');
    }
} 