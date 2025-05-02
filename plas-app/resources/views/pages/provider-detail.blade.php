@extends('layouts.app')

@section('title', $provider->name)

@section('content')
    <!-- Hero Section -->
    <section class="bg-navy text-white py-16 md:py-24">
        <div class="container-custom">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 text-white slide-up">{{ $provider->name }}</h1>
                @if($provider->category)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary text-white mb-6 slide-up">
                        {{ $provider->category }}
                    </span>
                @endif
                <p class="text-xl mb-8 slide-up">{{ $provider->provider_type ?? 'Healthcare Provider' }}</p>
            </div>
        </div>
    </section>

    <x-section>
        <div class="max-w-6xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('providers.index') }}" class="text-primary hover:underline flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to All Providers
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Provider Logo/Image -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                        <img 
                            src="{{ $provider->logo_path ? asset('storage/' . $provider->logo_path) : asset('images/provider-placeholder.jpg') }}" 
                            alt="{{ $provider->name }}" 
                            class="w-full h-auto object-cover"
                            loading="lazy"
                        >
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4 text-gray-800">Contact Information</h2>
                        
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-primary mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-gray-700">{{ $provider->address }}</p>
                                    <p class="text-gray-700">{{ $provider->city }}, {{ $provider->state }}</p>
                                </div>
                            </div>
                            
                            @if($provider->phone)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <p class="text-gray-700">{{ $provider->phone }}</p>
                            </div>
                            @endif
                            
                            @if($provider->email)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <a href="mailto:{{ $provider->email }}" class="text-primary hover:underline">{{ $provider->email }}</a>
                            </div>
                            @endif
                            
                            @if($provider->website)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                </svg>
                                <a href="{{ $provider->website }}" target="_blank" class="text-primary hover:underline">Visit Website</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Provider Details -->
                <div class="md:col-span-2">
                    <!-- About -->
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 class="text-2xl font-bold mb-4 text-gray-800">About</h2>
                        
                        @if($provider->description)
                            <div class="prose max-w-none">
                                <p class="text-gray-700">{{ $provider->description }}</p>
                            </div>
                        @else
                            <p class="text-gray-600 italic">No description available for this provider.</p>
                        @endif
                    </div>
                    
                    <!-- Services -->
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 class="text-2xl font-bold mb-4 text-gray-800">Services</h2>
                        
                        @if(is_array($provider->services) && count($provider->services) > 0)
                            <ul class="list-disc list-inside space-y-2 text-gray-700">
                                @foreach($provider->services as $service)
                                    <li>{{ $service }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-600 italic">No specific services listed for this provider.</p>
                        @endif
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-2xl font-bold mb-4 text-gray-800">PLASCHEMA Coverage</h2>
                        <div class="prose max-w-none">
                            <p class="text-gray-700">This provider accepts PLASCHEMA health insurance plans. For specific coverage details and enrollment information, please contact our office or visit the Plans section of our website.</p>
                            <div class="mt-6">
                                <x-button href="{{ route('contact') }}" class="mr-4">Contact Us</x-button>
                                <x-button href="{{ url('/plans') }}" variant="outline">View Plans</x-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-section>
@endsection 