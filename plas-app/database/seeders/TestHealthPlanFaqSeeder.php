<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestHealthPlanFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test FAQs specific to healthcare plans
        Faq::create([
            'question' => 'How do I enroll in a PLASCHEMA health plan?',
            'answer' => 'You can enroll by visiting any PLASCHEMA office, filling out an enrollment form, providing the required documentation, and making the appropriate premium payment based on your selected plan.',
            'category' => 'Healthcare Plans',
            'order' => 1,
            'is_active' => true,
            'show_on_plans_page' => true,
        ]);

        Faq::create([
            'question' => 'Can I enroll my family members?',
            'answer' => 'You can enroll your family by visiting any PLASCHEMA office or enrollment center with your family details and making the required family premium payment.',
            'category' => 'Healthcare Plans',
            'order' => 2,
            'is_active' => true,
            'show_on_plans_page' => true,
        ]);

        Faq::create([
            'question' => 'Am I eligible for the BHCPF or Equity Program?',
            'answer' => 'Eligibility for these programs is determined through community-based targeting. You can visit your local government healthcare office or contact PLASCHEMA directly to check your eligibility.',
            'category' => 'Healthcare Plans',
            'order' => 3,
            'is_active' => true,
            'show_on_plans_page' => true,
        ]);

        // Create a few more FAQs that shouldn't appear on the health plan page
        Faq::create([
            'question' => 'How do I find a healthcare provider?',
            'answer' => 'You can use our website provider directory to find a healthcare provider near you, or contact our office for assistance.',
            'category' => 'Healthcare Providers',
            'order' => 1,
            'is_active' => true,
            'show_on_plans_page' => false,
        ]);

        Faq::create([
            'question' => 'What is the difference between formal and informal sector plans?',
            'answer' => 'Formal sector plans are designed for employees working in registered organizations, while informal sector plans are designed for individuals working in the informal economy.',
            'category' => 'Healthcare Plans',
            'order' => 4,
            'is_active' => true,
            'show_on_plans_page' => false,
        ]);
    }
}
