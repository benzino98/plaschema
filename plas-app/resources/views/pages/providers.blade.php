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
                        
                        <!-- Advanced Filter Options -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Filter by Category</label>
                                <select id="category" name="category" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-plaschema focus:border-plaschema sm:text-sm rounded-md">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ $currentCategory == $category ? 'selected' : '' }}>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Filter by Location</label>
                                <select id="city" name="city" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-plaschema focus:border-plaschema sm:text-sm rounded-md">
                                    <option value="">All Locations</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}" {{ $currentCity == $city ? 'selected' : '' }}>{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="provider_type" class="block text-sm font-medium text-gray-700 mb-1">Filter by Provider Type</label>
                                <select id="provider_type" name="provider_type" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-plaschema focus:border-plaschema sm:text-sm rounded-md">
                                    <option value="">All Provider Types</option>
                                    @foreach($providerTypes as $type)
                                        <option value="{{ $type }}" {{ $currentProviderType == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Search Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="bg-plaschema hover:bg-plaschema-dark text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition duration-150">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Search
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Search Results Summary -->
            @if(isset($searchQuery) && $searchQuery || isset($currentCategory) && $currentCategory || isset($currentCity) && $currentCity || isset($currentProviderType) && $currentProviderType)
                <div class="mb-6">
                    <h2 class="text-xl font-semibold">
                        @if(count($providers) > 0)
                            Found {{ $providers->total() }} result(s)
                            @if(isset($searchQuery) && $searchQuery)
                                for "{{ $searchQuery }}"
                            @endif
                        @else
                            No results found
                            @if(isset($searchQuery) && $searchQuery)
                                for "{{ $searchQuery }}"
                            @endif
                        @endif
                    </h2>
                    <div class="text-gray-600 flex flex-wrap gap-2 mt-2">
                        @if($currentCategory)
                            <div class="inline-flex items-center bg-gray-100 rounded-full px-3 py-1 text-sm">
                                Category: {{ $currentCategory }}
                                <a href="{{ route('providers.index', array_merge(request()->except('category'), ['page' => 1])) }}" class="ml-2 text-gray-500 hover:text-gray-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                        
                        @if($currentCity)
                            <div class="inline-flex items-center bg-gray-100 rounded-full px-3 py-1 text-sm">
                                Location: {{ $currentCity }}
                                <a href="{{ route('providers.index', array_merge(request()->except('city'), ['page' => 1])) }}" class="ml-2 text-gray-500 hover:text-gray-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                        
                        @if($currentProviderType)
                            <div class="inline-flex items-center bg-gray-100 rounded-full px-3 py-1 text-sm">
                                Provider Type: {{ $currentProviderType }}
                                <a href="{{ route('providers.index', array_merge(request()->except('provider_type'), ['page' => 1])) }}" class="ml-2 text-gray-500 hover:text-gray-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                        
                        @if($searchQuery || $currentCategory || $currentCity || $currentProviderType)
                            <div class="inline-flex items-center">
                                <a href="{{ route('providers.index') }}" class="text-plaschema hover:underline text-sm">
                                    Clear all filters
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Providers Table -->
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="provider-list">
                        @foreach($providers as $provider)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                       
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $provider->name }}</div>
                                            @if($provider->description)
                                                <div class="text-sm text-gray-500">{{ Str::limit($provider->description, 50) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($provider->category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-plaschema">
                                            {{ $provider->category }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($provider->phone)
                                        <div class="text-sm text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            <span>{{ $provider->phone }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($provider->email)
                                        <div class="text-sm text-gray-600 flex items-center mt-1">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>{{ $provider->email }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">{{ $provider->address }}</div>
                                    @if($provider->city)
                                        <div class="text-sm text-gray-500">{{ $provider->city }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('providers.show', $provider->id) }}" class="text-plaschema hover:text-plaschema-dark">
                                            View Details
                                        </a>
                                        @if($provider->website)
                                            <a href="{{ $provider->website }}" target="_blank" class="text-plaschema hover:text-plaschema-dark">
                                                Website
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Skeleton Loading Template (Hidden by default) -->
            <template id="provider-skeleton-template">
                <tr>
                    <td colspan="5" class="px-6 py-4">
                        <x-skeleton-loader type="table-row" />
                    </td>
                </tr>
            </template>

            <!-- Pagination -->
            @if($providers->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $providers->links() }}
                </div>
            @endif

            <!-- Progressive Loading Script -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Set up intersection observer for pagination links
                    if (document.querySelector('.pagination')) {
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    const nextPageLink = document.querySelector('.pagination a[rel="next"]');
                                    if (nextPageLink) {
                                        loadNextPage(nextPageLink.href);
                                    }
                                }
                            });
                        }, { threshold: 0.5 });
                        
                        // Observe the pagination element
                        observer.observe(document.querySelector('.pagination'));
                        
                        // Function to load next page of providers
                        function loadNextPage(url) {
                            // Show skeleton loaders
                            showSkeletonLoaders(3);
                            
                            fetch(url)
                                .then(response => response.text())
                                .then(html => {
                                    // Create a temporary container
                                    const tempContainer = document.createElement('div');
                                    tempContainer.innerHTML = html;
                                    
                                    // Remove skeleton loaders
                                    removeSkeletonLoaders();
                                    
                                    // Extract providers from next page
                                    const newProviders = tempContainer.querySelectorAll('#provider-list tr');
                                    const providerList = document.getElementById('provider-list');
                                    
                                    // Append new providers
                                    newProviders.forEach(provider => {
                                        providerList.appendChild(provider.cloneNode(true));
                                    });
                                    
                                    // Update pagination with the new one
                                    const newPagination = tempContainer.querySelector('.pagination');
                                    if (newPagination) {
                                        document.querySelector('.pagination').innerHTML = newPagination.innerHTML;
                                    }
                                })
                                .catch(error => {
                                    console.error('Error loading providers:', error);
                                    removeSkeletonLoaders();
                                });
                        }
                        
                        // Function to show skeleton loaders
                        function showSkeletonLoaders(count) {
                            const template = document.getElementById('provider-skeleton-template');
                            const providerList = document.getElementById('provider-list');
                            
                            for (let i = 0; i < count; i++) {
                                const clone = document.importNode(template.content, true);
                                clone.querySelector('tr').classList.add('skeleton-loader');
                                providerList.appendChild(clone);
                            }
                        }
                        
                        // Function to remove skeleton loaders
                        function removeSkeletonLoaders() {
                            document.querySelectorAll('.skeleton-loader').forEach(loader => {
                                loader.remove();
                            });
                        }
                    }
                });
            </script>
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