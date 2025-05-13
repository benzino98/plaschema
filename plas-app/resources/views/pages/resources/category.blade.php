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
                {{ $category->name }}
            </h1>
            <p class="mt-6 text-xl max-w-3xl">
                {{ $category->description ?? 'Browse resources in the '.$category->name.' category.' }}
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <div>
                        <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-home"></i>
                            <span class="sr-only">Home</span>
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                        <a href="{{ route('resources.index') }}" class="ml-4 text-gray-500 hover:text-gray-700">Resources</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                        <span class="ml-4 text-gray-900 font-medium">{{ $category->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Category Selector -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h2 class="text-lg font-medium text-gray-900 mb-4 md:mb-0">Browse By Category</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:flex md:flex-wrap gap-2">
                    <a href="{{ route('resources.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-full text-sm {{ !request('category') ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                        All Resources
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('resources.category', $cat->slug) }}" class="inline-flex items-center px-3 py-1.5 rounded-full text-sm {{ $category->id === $cat->id ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Resources List -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $category->name }} Resources
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
                    {{ $resources->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-folder-open text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-1">No resources found</h3>
                    <p class="text-gray-600 mb-4">
                        There are currently no resources available in this category.
                    </p>
                    <a href="{{ route('resources.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i> Browse All Resources
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