@extends('layouts.app')

@section('content')
<div class="bg-gray-50">
    <!-- Hero Section -->
    <div class="relative bg-blue-900 text-white">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900 to-blue-800 opacity-90"></div>
        </div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                Resources
            </h1>
            <p class="mt-6 text-xl max-w-3xl">
                Access forms, documents, and helpful guides from PLASCHEMA. Browse by category or search for specific resources.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Filter and Search Section -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <form action="{{ route('resources.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Search for resources...">
                </div>
                
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="category" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i> Search
                    </button>
                    <a href="{{ route('resources.index') }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                        <i class="fas fa-times mr-2"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Featured Resources Section (if any) -->
        @if($featuredResources && $featuredResources->count() > 0 && !request('search') && !request('category'))
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Resources</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($featuredResources as $resource)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 ease-in-out hover:shadow-lg hover:translate-y-[-4px]">
                            <div class="p-5">
                                <div class="flex items-center mb-3">
                                    <span class="flex-shrink-0 bg-blue-100 p-2 rounded-md text-blue-600">
                                        <i class="fas fa-{{ getFileIcon($resource->file_extension) }} fa-lg"></i>
                                    </span>
                                    <h3 class="ml-3 text-lg font-semibold text-gray-900 truncate">{{ $resource->title }}</h3>
                                </div>
                                <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $resource->description }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">{{ $resource->formatted_file_size }} • {{ strtoupper($resource->file_extension) }}</span>
                                    <a href="{{ route('resources.download', $resource->slug) }}" class="inline-flex items-center px-3 py-1 border border-blue-600 text-blue-600 rounded-md hover:bg-blue-600 hover:text-white transition">
                                        <i class="fas fa-download mr-2"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- All Resources Section -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    @if(request('category') && $categories->where('id', request('category'))->first())
                        {{ $categories->where('id', request('category'))->first()->name }} Resources
                    @elseif(request('search'))
                        Search Results for "{{ request('search') }}"
                    @else
                        All Resources
                    @endif
                </h2>
                <div class="text-sm text-gray-600">{{ $resources->total() }} resources found</div>
            </div>

            @if($resources->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    @foreach($resources as $resource)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 ease-in-out hover:shadow-lg hover:translate-y-[-4px]">
                            <div class="p-5">
                                <div class="flex items-center mb-3">
                                    <span class="flex-shrink-0 bg-blue-100 p-2 rounded-md text-blue-600">
                                        <i class="fas fa-{{ getFileIcon($resource->file_extension) }} fa-lg"></i>
                                    </span>
                                    <h3 class="ml-3 text-lg font-semibold text-gray-900 truncate">{{ $resource->title }}</h3>
                                </div>
                                <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $resource->description }}</p>
                                <div class="flex flex-wrap justify-between items-center">
                                    <span class="text-xs text-gray-500 mb-2 md:mb-0">
                                        {{ $resource->formatted_file_size }} • {{ strtoupper($resource->file_extension) }}
                                    </span>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('resources.show', $resource->slug) }}" class="inline-flex items-center px-3 py-1 border border-gray-600 text-gray-600 rounded-md hover:bg-gray-600 hover:text-white transition">
                                            <i class="fas fa-info-circle mr-2"></i> Details
                                        </a>
                                        <a href="{{ route('resources.download', $resource->slug) }}" class="inline-flex items-center px-3 py-1 border border-blue-600 text-blue-600 rounded-md hover:bg-blue-600 hover:text-white transition">
                                            <i class="fas fa-download mr-2"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $resources->appends(request()->query())->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-file-alt text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-1">No resources found</h3>
                    <p class="text-gray-600 mb-4">
                        @if(request('search'))
                            No resources match your search criteria. Please try different keywords.
                        @elseif(request('category'))
                            No resources are available in this category yet.
                        @else
                            No resources are available at the moment. Please check back later.
                        @endif
                    </p>
                    <a href="{{ route('resources.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-redo mr-2"></i> Reset Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@php
function getFileIcon($extension) {
    $extension = strtolower($extension);
    switch($extension) {
        case 'pdf':
            return 'file-pdf';
        case 'doc':
        case 'docx':
            return 'file-word';
        case 'xls':
        case 'xlsx':
            return 'file-excel';
        case 'ppt':
        case 'pptx':
            return 'file-powerpoint';
        case 'zip':
        case 'rar':
            return 'file-archive';
        case 'txt':
            return 'file-alt';
        default:
            return 'file';
    }
}
@endphp
@endsection 