@extends('layouts.app')

@section('title', $news->title)

@section('content')
    <!-- Hero Section -->
    <section class="bg-plaschema-dark text-white py-16 md:py-24">
        <div class="container-custom">
            <div class="max-w-4xl mx-auto">
                <div class="mb-3">
                    <a href="{{ route('news') }}" class="text-white hover:text-plaschema flex items-center slide-up">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to News
                    </a>
                </div>
                <h1 class="text-3xl md:text-5xl font-bold mb-4 text-white slide-up">
                    {{ $news->title }}
                </h1>
                <div class="flex items-center slide-up">
                    <span class="text-sm text-gray-300">{{ $news->published_at->format('F d, Y') }}</span>
                    <span class="mx-3">|</span>
                    <span class="text-sm text-gray-300">PLASCHEMA News</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Article Content -->
    <x-section>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                @if($news->image_path)
                <div class="mb-8 rounded-lg overflow-hidden" style="aspect-ratio: 16/9;">
                    <x-responsive-image
                        :path-small="$news->image_path_small"
                        :path-medium="$news->image_path_medium"
                        :path-large="$news->image_path_large"
                        :path-original="$news->image_path"
                        :alt="$news->title"
                        class="w-full h-full object-cover"
                        loading="lazy"
                        objectPosition="center 30%"
                    />
                </div>
                @endif
                
                <div class="prose max-w-none">
                    {!! $news->content !!}
                </div>
                
                <!-- Tags -->
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Healthcare</span>
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">PLASCHEMA</span>
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Plateau State</span>
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Health Insurance</span>
                    </div>
                </div>
                
                <!-- Share -->
                <div class="mt-6">
                    <p class="text-sm font-medium text-gray-700 mb-2">Share this article:</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-500 hover:text-plaschema">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-plaschema">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723 10.054 10.054 0 01-3.127 1.184 4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-plaschema">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-plaschema">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 .4C4.698.4.4 4.698.4 10s4.298 9.6 9.6 9.6 9.6-4.298 9.6-9.6S15.302.4 10 .4zm3.905 7.864c.004.082.005.164.005.244 0 2.5-1.901 5.381-5.379 5.381a5.335 5.335 0 01-2.898-.85c.147.018.298.025.451.025.887 0 1.704-.301 2.351-.809a1.895 1.895 0 01-1.767-1.312 1.9 1.9 0 00.853-.033 1.892 1.892 0 01-1.517-1.854v-.023c.255.141.547.227.857.237a1.89 1.89 0 01-.585-2.526 5.376 5.376 0 003.897 1.977 1.891 1.891 0 013.222-1.725 3.797 3.797 0 001.2-.459 1.9 1.9 0 01-.831 1.047 3.799 3.799 0 001.086-.299 3.834 3.834 0 01-.943.979z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-xl font-bold mb-4">Recent News</h3>
                    <div class="space-y-4">
                        @foreach($relatedNews as $relatedItem)
                            <a href="{{ route('news.show', $relatedItem->slug) }}" class="flex items-start group">
                                <div class="w-16 h-16 rounded overflow-hidden flex-shrink-0">
                                    @if($relatedItem->image_path_small)
                                        <img src="{{ asset('storage/' . $relatedItem->image_path_small) }}" alt="{{ $relatedItem->title }}" class="w-full h-full object-cover" loading="lazy" style="object-position: center 20%;">
                                    @else
                                        <img src="{{ asset('images/news-placeholder.jpg') }}" alt="News" class="w-full h-full object-cover" loading="lazy">
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-medium group-hover:text-plaschema transition-colors">{{ $relatedItem->title }}</h4>
                                    <span class="text-sm text-gray-500">{{ $relatedItem->published_at->format('F d, Y') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 text-center">
                        <x-button href="{{ route('news') }}" variant="text" class="text-sm">View All News</x-button>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="flex justify-between items-center hover:text-plaschema">
                            <span>Press Releases</span>
                            <span class="bg-gray-100 text-gray-700 rounded-full px-2 py-0.5 text-xs">12</span>
                        </a></li>
                        <li><a href="#" class="flex justify-between items-center hover:text-plaschema">
                            <span>Events & Activities</span>
                            <span class="bg-gray-100 text-gray-700 rounded-full px-2 py-0.5 text-xs">8</span>
                        </a></li>
                        <li><a href="#" class="flex justify-between items-center hover:text-plaschema">
                            <span>Health Updates</span>
                            <span class="bg-gray-100 text-gray-700 rounded-full px-2 py-0.5 text-xs">15</span>
                        </a></li>
                        <li><a href="#" class="flex justify-between items-center hover:text-plaschema">
                            <span>Policy Changes</span>
                            <span class="bg-gray-100 text-gray-700 rounded-full px-2 py-0.5 text-xs">6</span>
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </x-section>
    
    <!-- Related Articles -->
    @if(isset($relatedNews) && count($relatedNews) > 0)
        <x-section 
            background="bg-light-gray" 
            title="Related Articles"
            subtitle="More news and updates from PLASCHEMA"
        >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($relatedNews->take(3) as $item)
                    <x-card 
                        title="{{ $item->title }}" 
                        image="{{ $item->image_path }}"
                        imageSmall="{{ $item->image_path_small }}"
                        imageMedium="{{ $item->image_path_medium }}"
                        imageLarge="{{ $item->image_path_large }}"
                        animation="slide-up"
                        url="{{ route('news.show', $item->slug) }}"
                        aspectRatio="16/9"
                        objectPosition="center 25%"
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
        </x-section>
    @endif
@endsection 