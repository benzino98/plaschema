<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MessageCategory;
use Illuminate\Support\Str;

class MessageCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'General Inquiry',
                'description' => 'General questions about PLASCHEMA and its services',
                'priority' => 5,
            ],
            [
                'name' => 'Enrollment Question',
                'description' => 'Questions about enrolling in healthcare plans',
                'priority' => 10,
            ],
            [
                'name' => 'Provider Question',
                'description' => 'Questions about healthcare providers and services',
                'priority' => 8,
            ],
            [
                'name' => 'Feedback',
                'description' => 'Comments and suggestions about PLASCHEMA services',
                'priority' => 3,
            ],
            [
                'name' => 'Technical Issue',
                'description' => 'Problems with the website or technical functionality',
                'priority' => 7,
            ],
        ];

        foreach ($categories as $category) {
            MessageCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'priority' => $category['priority'],
                ]
            );
        }
    }
}
