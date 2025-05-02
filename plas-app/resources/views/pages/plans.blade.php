@extends('layouts.app')

@section('title', 'Health Plans')

@section('content')
    <!-- Hero Section -->
    <section class="bg-plaschema-dark text-white py-16 md:py-24">
        <div class="container-custom">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 text-white slide-up">Healthcare Plans</h1>
                <p class="text-xl mb-8 slide-up">Choose from our range of healthcare plans designed to meet the needs of different sectors of the population.</p>
            </div>
        </div>
    </section>

    <!-- Plans Section -->
    <x-section
        title="Our Healthcare Plans"
        subtitle="We offer comprehensive healthcare plans tailored to different sectors of the population."
    >
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
            <x-card 
                title="Formal Sector Plan" 
                animation="slide-up"
                class="bg-white hover:shadow-lg transition-shadow"
            >
                <div class="mb-6">
                    <p class="text-gray-600 mb-4">A comprehensive health insurance plan for employees in the formal sector, covering a wide range of medical services for individuals and families.</p>
                    
                    <div class="mt-6 space-y-3">
                        <h4 class="font-bold text-plaschema">Key Benefits:</h4>
                        <ul class="list-disc pl-5 space-y-2 text-gray-600">
                            <li>Access to over 200 healthcare providers</li>
                            <li>Comprehensive outpatient services</li>
                            <li>Inpatient care coverage</li>
                            <li>Maternal and child health services</li>
                            <li>Pharmaceutical services</li>
                            <li>Diagnostic services</li>
                        </ul>
                    </div>
                    
                    <div class="mt-6">
                        <p class="font-bold text-plaschema">Premium:</p>
                        <p class="text-gray-600">3% of basic salary for employees, with employer contributing 9% (total 12%).</p>
                    </div>
                </div>
                
                <x-button href="#" class="w-full justify-center">Enroll Now</x-button>
            </x-card>

            <x-card 
                title="Informal Sector Plan" 
                animation="slide-up" 
                class="bg-white hover:shadow-lg transition-shadow"
            >
                <div class="mb-6">
                    <p class="text-gray-600 mb-4">Tailored health coverage for traders, artisans, and other workers in the informal economy, providing essential healthcare services at affordable rates.</p>
                    
                    <div class="mt-6 space-y-3">
                        <h4 class="font-bold text-plaschema">Key Benefits:</h4>
                        <ul class="list-disc pl-5 space-y-2 text-gray-600">
                            <li>Access to healthcare providers across the state</li>
                            <li>Basic outpatient services</li>
                            <li>Limited inpatient care</li>
                            <li>Maternal services</li>
                            <li>Basic pharmaceutical services</li>
                            <li>Essential diagnostic services</li>
                        </ul>
                    </div>
                    
                    <div class="mt-6">
                        <p class="font-bold text-plaschema">Premium:</p>
                        <p class="text-gray-600">Annual payment of ₦12,000 per individual or ₦30,000 for a family of 4.</p>
                    </div>
                </div>
                
                <x-button href="#" class="w-full justify-center">Enroll Now</x-button>
            </x-card>

            <x-card 
                title="BHCPF Plan" 
                animation="slide-up" 
                class="bg-white hover:shadow-lg transition-shadow"
            >
                <div class="mb-6">
                    <p class="text-gray-600 mb-4">Basic Healthcare Provision Fund for vulnerable populations, ensuring essential health services for those who cannot afford regular premiums.</p>
                    
                    <div class="mt-6 space-y-3">
                        <h4 class="font-bold text-plaschema">Key Benefits:</h4>
                        <ul class="list-disc pl-5 space-y-2 text-gray-600">
                            <li>Access to primary healthcare centers</li>
                            <li>Basic outpatient services</li>
                            <li>Essential medicines</li>
                            <li>Maternal and child health services</li>
                            <li>Treatment for common illnesses</li>
                            <li>Basic diagnostic services</li>
                        </ul>
                    </div>
                    
                    <div class="mt-6">
                        <p class="font-bold text-plaschema">Eligibility:</p>
                        <p class="text-gray-600">Vulnerable individuals as identified through community-based targeting mechanism.</p>
                    </div>
                </div>
                
                <x-button href="#" variant="secondary" class="w-full justify-center">Check Eligibility</x-button>
            </x-card>

            <x-card 
                title="Equity Program" 
                animation="slide-up" 
                class="bg-white hover:shadow-lg transition-shadow"
            >
                <div class="mb-6">
                    <p class="text-gray-600 mb-4">Healthcare support for the poorest and most vulnerable groups in Plateau State, funded by the state government and partners.</p>
                    
                    <div class="mt-6 space-y-3">
                        <h4 class="font-bold text-plaschema">Key Benefits:</h4>
                        <ul class="list-disc pl-5 space-y-2 text-gray-600">
                            <li>Access to designated healthcare facilities</li>
                            <li>Basic healthcare services</li>
                            <li>Essential medicines</li>
                            <li>Maternal and child health services</li>
                            <li>Treatment for common illnesses</li>
                            <li>Referral services for complex cases</li>
                        </ul>
                    </div>
                    
                    <div class="mt-6">
                        <p class="font-bold text-plaschema">Eligibility:</p>
                        <p class="text-gray-600">Targeted vulnerable groups such as pregnant women, children under 5, elderly, and persons with disabilities from poorest households.</p>
                    </div>
                </div>
                
                <x-button href="#" variant="secondary" class="w-full justify-center">Check Eligibility</x-button>
            </x-card>
        </div>
    </x-section>

    <!-- Comparison Table -->
    <x-section
        background="light"
        title="Plan Comparison"
        subtitle="Compare our healthcare plans to find the best option for you and your family."
    >
        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-plaschema-dark text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">Features</th>
                        <th class="py-3 px-4 text-center">Formal Sector</th>
                        <th class="py-3 px-4 text-center">Informal Sector</th>
                        <th class="py-3 px-4 text-center">BHCPF</th>
                        <th class="py-3 px-4 text-center">Equity Program</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200">
                        <td class="py-3 px-4 font-medium">Outpatient Services</td>
                        <td class="py-3 px-4 text-center">Comprehensive</td>
                        <td class="py-3 px-4 text-center">Basic</td>
                        <td class="py-3 px-4 text-center">Basic</td>
                        <td class="py-3 px-4 text-center">Basic</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="py-3 px-4 font-medium">Inpatient Care</td>
                        <td class="py-3 px-4 text-center">Full Coverage</td>
                        <td class="py-3 px-4 text-center">Limited</td>
                        <td class="py-3 px-4 text-center">Limited</td>
                        <td class="py-3 px-4 text-center">Limited</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="py-3 px-4 font-medium">Maternal Services</td>
                        <td class="py-3 px-4 text-center">Full Coverage</td>
                        <td class="py-3 px-4 text-center">Basic</td>
                        <td class="py-3 px-4 text-center">Basic</td>
                        <td class="py-3 px-4 text-center">Enhanced</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="py-3 px-4 font-medium">Pharmaceutical Services</td>
                        <td class="py-3 px-4 text-center">Comprehensive</td>
                        <td class="py-3 px-4 text-center">Basic</td>
                        <td class="py-3 px-4 text-center">Essential Only</td>
                        <td class="py-3 px-4 text-center">Essential Only</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="py-3 px-4 font-medium">Diagnostic Services</td>
                        <td class="py-3 px-4 text-center">Comprehensive</td>
                        <td class="py-3 px-4 text-center">Limited</td>
                        <td class="py-3 px-4 text-center">Basic</td>
                        <td class="py-3 px-4 text-center">Basic</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="py-3 px-4 font-medium">Provider Network</td>
                        <td class="py-3 px-4 text-center">200+ facilities</td>
                        <td class="py-3 px-4 text-center">150+ facilities</td>
                        <td class="py-3 px-4 text-center">Primary facilities</td>
                        <td class="py-3 px-4 text-center">Designated facilities</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-medium">Cost</td>
                        <td class="py-3 px-4 text-center">12% of basic salary</td>
                        <td class="py-3 px-4 text-center">₦12,000/individual</td>
                        <td class="py-3 px-4 text-center">Subsidized</td>
                        <td class="py-3 px-4 text-center">Free</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-section>

    <!-- Steps Section -->
    <x-section
        title="How to Enroll"
        subtitle="Follow these simple steps to enroll in one of our healthcare plans."
    >
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center bg-white rounded-lg shadow-md p-6 md:p-8 hover:shadow-lg transition-shadow">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-plaschema text-white rounded-full mb-4">
                    <span class="text-xl font-bold">1</span>
                </div>
                <h3 class="text-xl font-bold mb-4">Choose a Plan</h3>
                <p class="text-gray-600">Select the healthcare plan that best suits your needs and circumstances.</p>
            </div>
            
            <div class="text-center bg-white rounded-lg shadow-md p-6 md:p-8 hover:shadow-lg transition-shadow">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-plaschema text-white rounded-full mb-4">
                    <span class="text-xl font-bold">2</span>
                </div>
                <h3 class="text-xl font-bold mb-4">Complete Registration</h3>
                <p class="text-gray-600">Fill out the enrollment form and provide the required documentation.</p>
            </div>
            
            <div class="text-center bg-white rounded-lg shadow-md p-6 md:p-8 hover:shadow-lg transition-shadow">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-plaschema text-white rounded-full mb-4">
                    <span class="text-xl font-bold">3</span>
                </div>
                <h3 class="text-xl font-bold mb-4">Start Using Benefits</h3>
                <p class="text-gray-600">Receive your PLASCHEMA ID card and start accessing healthcare services.</p>
            </div>
        </div>
    </x-section>

    <!-- FAQs -->
    <x-section
        background="light"
        title="Frequently Asked Questions"
        subtitle="Find answers to common questions about our healthcare plans."
    >
        <div class="max-w-3xl mx-auto">
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-md p-6 md:p-8">
                    <h3 class="text-xl font-bold mb-4">How do I enroll in a PLASCHEMA health plan?</h3>
                    <p class="text-gray-600">You can enroll by visiting any PLASCHEMA office, filling out an enrollment form, providing the required documentation, and making the appropriate premium payment based on your selected plan.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 md:p-8">
                    <h3 class="text-xl font-bold mb-4">Can I enroll my family members?</h3>
                    <p class="text-gray-600">You can enroll your family by visiting any PLASCHEMA office or enrollment center with your family details and making the required family premium payment.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6 md:p-8">
                    <h3 class="text-xl font-bold mb-4">Am I eligible for the BHCPF or Equity Program?</h3>
                    <p class="text-gray-600">Eligibility for these programs is determined through community-based targeting. You can visit your local government healthcare office or contact PLASCHEMA directly to check your eligibility.</p>
                </div>
            </div>
            
            <div class="mt-8 text-center">
                <x-button href="{{ route('faq') }}" variant="outline" class="px-6 py-3">View All FAQs</x-button>
            </div>
        </div>
    </x-section>

    <!-- Call to Action -->
    <section class="bg-plaschema-dark text-white py-16">
        <div class="container-custom text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Enroll?</h2>
            <p class="text-xl mb-8 max-w-3xl mx-auto">Take the first step towards quality healthcare coverage for you and your family. Contact us or visit any PLASCHEMA office to enroll today.</p>
            <div class="flex justify-center space-x-4">
                <x-button href="{{ route('contact') }}" class="text-lg px-6 py-3">Contact Us</x-button>
                <x-button href="#" variant="outline" class="text-lg px-6 py-3">Find Office Locations</x-button>
            </div>
        </div>
    </section>
@endsection 