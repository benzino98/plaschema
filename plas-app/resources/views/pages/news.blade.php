@extends('layouts.app')

@section('title', 'News & Updates')

@section('content')
    <!-- Hero Section -->
    <section class="bg-plaschema-dark text-white py-16 md:py-24">
        <div class="container-custom">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 text-white slide-up">News & Updates</h1>
                <p class="text-xl mb-8 slide-up">Stay informed about the latest developments at PLASCHEMA and healthcare initiatives in Plateau State.</p>
            </div>
        </div>
    </section>

    <!-- News Grid -->
    <x-section>
        <!-- Search Form -->
        <div class="mb-10">
            <form action="{{ route('news') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="flex">
                    <div class="flex-grow">
                        <label for="search" class="sr-only">Search news</label>
                        <div class="relative">
                            <input type="text" id="search" name="search" value="{{ $searchQuery ?? '' }}" placeholder="Search news articles..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-l-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-plaschema focus:border-plaschema sm:text-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-r-md shadow-sm text-sm font-medium text-white bg-plaschema hover:bg-plaschema-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-plaschema">
                        Search
                    </button>
                </div>
            </form>
        </div>
        
        @if(isset($searchQuery) && $searchQuery)
            <div class="mb-8">
                <h2 class="text-xl font-semibold">
                    @if(count($latestNews) > 0)
                        Found {{ $latestNews->total() }} result(s) for "{{ $searchQuery }}"
                    @else
                        No results found for "{{ $searchQuery }}"
                    @endif
                </h2>
                <a href="{{ route('news') }}" class="text-plaschema hover:underline">Clear search</a>
            </div>
        @endif

        @if(count($featuredNews) > 0 && !isset($searchQuery))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($featuredNews as $item)
                    <x-card 
                        title="{{ $item->title }}" 
                        image="{{ $item->image_path ? asset('storage/' . $item->image_path) : asset('images/news-placeholder.jpg') }}"
                        imageSmall="{{ $item->image_path_small ? asset('storage/' . $item->image_path_small) : null }}"
                        imageMedium="{{ $item->image_path_medium ? asset('storage/' . $item->image_path_medium) : null }}"
                        imageLarge="{{ $item->image_path_large ? asset('storage/' . $item->image_path_large) : null }}"
                        animation="slide-up"
                        url="{{ route('news.show', $item->slug) }}"
                    >
                        <p class="text-gray-600 mb-4">{{ $item->excerpt }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">{{ $item->published_at->format('F d, Y') }}</span>
                            <x-button href="{{ route('news.show', $item->slug) }}" variant="text" class="flex items-center">
                                Read More
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </x-button>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif

        @if(count($latestNews) > 0)
            @if(!isset($searchQuery))
                <h2 class="text-2xl font-bold mt-16 mb-8">Latest News</h2>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($latestNews as $item)
                    <x-card 
                        title="{{ $item->title }}" 
                        image="{{ $item->image_path ? asset('storage/' . $item->image_path) : asset('images/news-placeholder.jpg') }}"
                        imageSmall="{{ $item->image_path_small ? asset('storage/' . $item->image_path_small) : null }}"
                        imageMedium="{{ $item->image_path_medium ? asset('storage/' . $item->image_path_medium) : null }}"
                        imageLarge="{{ $item->image_path_large ? asset('storage/' . $item->image_path_large) : null }}"
                        animation="slide-up"
                        url="{{ route('news.show', $item->slug) }}"
                    >
                        <p class="text-gray-600 mb-4">{{ $item->excerpt }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">{{ $item->published_at->format('F d, Y') }}</span>
                            <x-button href="{{ route('news.show', $item->slug) }}" variant="text" class="flex items-center">
                                Read More
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </x-button>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                @if(isset($searchQuery) && $searchQuery)
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-3xl font-bold mb-4">No results found</h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-8">We couldn't find any news articles matching your search for "{{ $searchQuery }}".</p>
                    <x-button href="{{ route('news') }}" class="text-lg px-6 py-3">View All News</x-button>
                @else
                    <p class="text-gray-600">No news articles available at this time.</p>
                @endif
            </div>
        @endif

        <!-- Pagination -->
        @if($latestNews->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $latestNews->links() }}
            </div>
        @endif
    </x-section>
@endsection 