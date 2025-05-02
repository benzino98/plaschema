<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = News::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(rand(6, 10));
        $slug = Str::slug($title);
        
        return [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(rand(3, 7), true),
            'image_path' => null, // Could set to a placeholder image if needed
            'published_at' => $this->faker->randomElement([
                null, 
                $this->faker->dateTimeBetween('-1 year', 'now')
            ]),
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
    
    /**
     * Configure the model factory to create a featured news article.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
    
    /**
     * Configure the model factory to create a published news article.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }
    
    /**
     * Configure the model factory to create a draft news article.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
