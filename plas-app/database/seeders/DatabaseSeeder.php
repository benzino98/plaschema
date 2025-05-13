<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\HealthcareProvider;
use App\Models\News;
use App\Models\Faq;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create an admin user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create additional test users
        User::factory(5)->create();

        // Create 15 healthcare providers
        HealthcareProvider::factory(15)->create();

        // Create 15 news articles
        News::factory(15)->create();

        // Create 15 FAQs
        Faq::factory(15)->create();
        
        // Seed roles and permissions
        $this->call(RoleAndPermissionSeeder::class);
        
        // Seed message categories
        $this->call(MessageCategoriesSeeder::class);
        
        // Seed resources and resource categories
        $this->call(ResourceSeeder::class);
    }
}
