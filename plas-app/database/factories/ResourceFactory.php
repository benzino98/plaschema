<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\ResourceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resource>
 */
class ResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Resource::class;

    /**
     * File types for sample resources.
     *
     * @var array
     */
    protected $fileTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
    ];

    /**
     * File extensions for sample resources.
     *
     * @var array
     */
    protected $fileExtensions = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'txt',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence();
        $fileTypeIndex = $this->faker->numberBetween(0, count($this->fileTypes) - 1);
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(),
            'category_id' => ResourceCategory::factory(),
            'file_path' => 'resources/' . $this->faker->uuid() . '.' . $this->fileExtensions[$fileTypeIndex],
            'file_name' => $this->faker->words(3, true) . '.' . $this->fileExtensions[$fileTypeIndex],
            'file_size' => $this->faker->numberBetween(100000, 5000000), // 100KB to 5MB
            'file_type' => $this->fileTypes[$fileTypeIndex],
            'searchable_content' => $this->faker->paragraphs(3, true),
            'download_count' => $this->faker->numberBetween(0, 500),
            'publish_date' => $this->faker->dateTimeBetween('-1 year', '+1 month'),
            'is_featured' => $this->faker->boolean(20), // 20% are featured
            'is_active' => true,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Configure the model factory to create an inactive resource.
     *
     * @return $this
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Configure the model factory to create a featured resource.
     *
     * @return $this
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Configure the model factory to create a resource with a specific category.
     *
     * @param int $categoryId
     * @return $this
     */
    public function withCategory(int $categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }

    /**
     * Configure the model factory to create a published resource.
     *
     * @return $this
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'publish_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'is_active' => true,
        ]);
    }

    /**
     * Configure the model factory to create an unpublished resource.
     *
     * @return $this
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'publish_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'is_active' => true,
        ]);
    }

    /**
     * Configure the model factory to create a popular resource.
     *
     * @return $this
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'download_count' => $this->faker->numberBetween(1000, 5000),
        ]);
    }
} 