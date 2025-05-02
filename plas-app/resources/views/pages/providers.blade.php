@extends('layouts.app')

@section('title', 'Healthcare Providers')

@section('content')
    <!-- Hero Section -->
    <section class="bg-plaschema-dark text-white py-16 md:py-24">
        <div class="container-custom">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 text-white slide-up">Healthcare Providers</h1>
                <p class="text-xl mb-8 slide-up">Find accredited healthcare providers in your area that accept PLASCHEMA health plans.</p>
            </div>
        </div>
    </section>

    <x-section>
        @if(count($providers) > 0)
            <!-- Search and Filter Section -->
            <div class="mb-8">
                <form action="{{ route('providers.index') }}" method="GET" class="space-y-4">
                    <!-- Search input -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-grow">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Providers</label>
                            <div class="relative">
                                <input type="text" id="search" name="search" value="{{ $searchQuery ?? '' }}" placeholder="Search by name, location, or type..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-plaschema focus:border-plaschema sm:text-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="md:w-1/3">
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Filter by Category</label>
                            <select id="category" name="category" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-plaschema focus:border-plaschema sm:text-sm rounded-md">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ $currentCategory == $category ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="md:flex-shrink-0 flex items-end">
                            <button type="submit" class="w-full md:w-auto bg-plaschema hover:bg-plaschema-dark text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-150">
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Search Results Summary -->
            @if(isset($searchQuery) && $searchQuery)
                <div class="mb-6">
                    <h2 class="text-xl font-semibold">
                        @if(count($providers) > 0)
                            Found {{ $providers->total() }} result(s) for "{{ $searchQuery }}"
                        @else
                            No results found for "{{ $searchQuery }}"
                        @endif
                    </h2>
                    @if($currentCategory)
                        <p class="text-gray-600">Filtered by category: {{ $currentCategory }}</p>
                    @endif
                </div>
            @endif

            <!-- Providers Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($providers as $provider)
                    <x-card 
                        title="{{ $provider->name }}" 
                        image="{{ $provider->logo_path ? asset('storage/' . $provider->logo_path) : asset('images/provider-placeholder.jpg') }}"
                        animation="slide-up"
                        url="{{ route('providers.show', $provider->id) }}"
                    >
                        <div class="mb-4">
                            @if($provider->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-plaschema mb-2">
                                    {{ $provider->category }}
                                </span>
                            @endif
                            <p class="text-gray-600">{{ $provider->address }}</p>
                        </div>
                        
                        <div class="space-y-2 mb-4">
                            @if($provider->phone)
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span>{{ $provider->phone }}</span>
                                </div>
                            @endif
                            
                            @if($provider->email)
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>{{ $provider->email }}</span>
                                </div>
                            @endif
                        </div>
                        
                        @if($provider->description)
                            <p class="text-gray-600 mb-4">{{ Str::limit($provider->description, 100) }}</p>
                        @endif
                        
                        <div class="mt-4 flex justify-between items-center">
                            <a href="{{ route('providers.show', $provider->id) }}" class="text-plaschema hover:underline flex items-center">
                                View Details
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                            
                            @if($provider->website)
                                <a href="{{ $provider->website }}" target="_blank" class="text-plaschema hover:underline flex items-center">
                                    Visit Website
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </x-card>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($providers->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $providers->links() }}
                </div>
            @endif
        @else
            <!-- No Providers or Coming Soon -->
            <div class="text-center py-16">
                @if(isset($searchQuery) && $searchQuery)
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-3xl font-bold mb-4">No results found</h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-8">We couldn't find any providers matching your search for "{{ $searchQuery }}".</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <x-button href="{{ route('providers.index') }}" class="text-lg px-6 py-3">View All Providers</x-button>
                        <x-button href="{{ route('contact') }}" variant="outline" class="text-lg px-6 py-3">Contact Us</x-button>
                    </div>
                @else
                    <svg class="w-24 h-24 text-plaschema mx-auto mb-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <h2 class="text-3xl font-bold mb-4">Provider Directory Coming Soon</h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-8">We're currently adding healthcare providers to our database. Please check back soon or contact our office for information about healthcare providers in your area.</p>
                    <x-button href="{{ route('contact') }}" class="text-lg px-6 py-3">Contact Us</x-button>
                @endif
            </div>
        @endif
    </x-section>
@endsection 