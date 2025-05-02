<?php

namespace Database\Factories;

use App\Models\HealthcareProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HealthcareProvider>
 */
class HealthcareProviderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HealthcareProvider::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Hospital', 'Clinic', 'Pharmacy', 'Primary Care', 'Specialist', 'Dental', 'Vision'];
        
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->paragraphs(rand(1, 3), true),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => 'Plateau',
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'services' => json_encode([
                $this->faker->randomElement(['Consultation', 'Surgery', 'Emergency Care', 'Laboratory Services']),
                $this->faker->randomElement(['Vaccination', 'Wellness Checks', 'Maternal Care', 'Pediatrics']),
                $this->faker->randomElement(['X-ray', 'MRI', 'Pharmacy', 'Physical Therapy']),
            ]),
            'logo_path' => null, // Could set to a placeholder image if needed
            'category' => $this->faker->randomElement($categories),
            'status' => $this->faker->randomElement(['active', 'inactive'], [90, 10]), // 90% chance of being active
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
    
    /**
     * Configure the model factory to create an active provider.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }
    
    /**
     * Configure the model factory to create an inactive provider.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
    
    /**
     * Configure the model factory to create a specific category of provider.
     */
    public function inCategory(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }
}
