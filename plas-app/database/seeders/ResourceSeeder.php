<?php

namespace Database\Seeders;

use App\Models\Resource;
use App\Models\ResourceCategory;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create parent categories
        $policyDocs = ResourceCategory::factory()->create([
            'name' => 'Policy Documents',
            'slug' => 'policy-documents',
            'description' => 'Official policy documents and guidelines for PLASCHEMA members and providers.',
        ]);
        
        $forms = ResourceCategory::factory()->create([
            'name' => 'Forms',
            'slug' => 'forms',
            'description' => 'Official forms required for various PLASCHEMA processes and applications.',
        ]);
        
        $guidelines = ResourceCategory::factory()->create([
            'name' => 'Guidelines',
            'slug' => 'guidelines',
            'description' => 'Medical and administrative guidelines for healthcare providers and members.',
        ]);
        
        // Create subcategories
        $providerPolicies = ResourceCategory::factory()->withParent($policyDocs->id)->create([
            'name' => 'Provider Policies',
            'slug' => 'provider-policies',
            'description' => 'Policies specifically for healthcare providers participating in PLASCHEMA.',
        ]);
        
        $memberPolicies = ResourceCategory::factory()->withParent($policyDocs->id)->create([
            'name' => 'Member Policies',
            'slug' => 'member-policies',
            'description' => 'Policies specifically for PLASCHEMA members.',
        ]);
        
        $medicalForms = ResourceCategory::factory()->withParent($forms->id)->create([
            'name' => 'Medical Forms',
            'slug' => 'medical-forms',
            'description' => 'Forms related to medical procedures and claims.',
        ]);
        
        $administrativeForms = ResourceCategory::factory()->withParent($forms->id)->create([
            'name' => 'Administrative Forms',
            'slug' => 'administrative-forms',
            'description' => 'Forms related to administrative procedures and applications.',
        ]);
        
        // Create additional random categories
        ResourceCategory::factory(3)->create();
        
        // Create resources for each category
        // Policy Documents
        Resource::factory(3)->withCategory($policyDocs->id)->featured()->published()->create();
        Resource::factory(5)->withCategory($policyDocs->id)->published()->create();
        
        // Provider Policies
        Resource::factory(2)->withCategory($providerPolicies->id)->featured()->published()->create();
        Resource::factory(4)->withCategory($providerPolicies->id)->published()->create();
        
        // Member Policies
        Resource::factory(2)->withCategory($memberPolicies->id)->featured()->published()->create();
        Resource::factory(3)->withCategory($memberPolicies->id)->published()->create();
        
        // Forms
        Resource::factory(2)->withCategory($forms->id)->featured()->published()->create();
        Resource::factory(4)->withCategory($forms->id)->published()->create();
        
        // Medical Forms
        Resource::factory(3)->withCategory($medicalForms->id)->featured()->popular()->published()->create();
        Resource::factory(5)->withCategory($medicalForms->id)->published()->create();
        
        // Administrative Forms
        Resource::factory(2)->withCategory($administrativeForms->id)->featured()->published()->create();
        Resource::factory(4)->withCategory($administrativeForms->id)->published()->create();
        
        // Guidelines
        Resource::factory(3)->withCategory($guidelines->id)->featured()->published()->create();
        Resource::factory(7)->withCategory($guidelines->id)->published()->create();
        
        // Create some unpublished resources
        Resource::factory(5)->unpublished()->create();
        
        // Create some inactive resources
        Resource::factory(3)->inactive()->create();
    }
} 