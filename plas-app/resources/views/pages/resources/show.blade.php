@extends('layouts.app')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                @if($resource->category)
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                        <a href="{{ route('resources.category', $resource->category->slug) }}" class="ml-4 text-gray-500 hover:text-gray-700">{{ $resource->category->name }}</a>
                    </div>
                </li>
                @endif
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-sm"></i>
                        <span class="ml-4 text-gray-900 font-medium">{{ $resource->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 md:p-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $resource->title }}</h1>
                        <div class="flex flex-wrap gap-3 text-sm text-gray-500">
                            @if($resource->category)
                                <a href="{{ route('resources.category', $resource->category->slug) }}" class="inline-flex items-center">
                                    <i class="fas fa-folder mr-2"></i> {{ $resource->category->name }}
                                </a>
                            @endif
                            <span class="inline-flex items-center">
                                <i class="fas fa-calendar-alt mr-2"></i> Added {{ $resource->created_at->format('M d, Y') }}
                            </span>
                            <span class="inline-flex items-center">
                                <i class="fas fa-download mr-2"></i> {{ $resource->download_count }} download(s)
                            </span>
                            <span class="inline-flex items-center">
                                <i class="fas fa-{{ getFileIcon($resource->file_extension) }} mr-2"></i> 
                                {{ strtoupper($resource->file_extension) }} • {{ $resource->formatted_file_size }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('resources.download', $resource->slug) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-download mr-2"></i> Download
                    </a>
                </div>

                <div class="prose max-w-none">
                    <h3 class="text-xl font-semibold mb-3">Description</h3>
                    <div class="text-gray-700">
                        {{ $resource->description }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Resources -->
        @if($relatedResources && $relatedResources->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Resources</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedResources as $relatedResource)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 ease-in-out hover:shadow-lg hover:translate-y-[-4px]">
                            <div class="p-5">
                                <div class="flex items-center mb-3">
                                    <span class="flex-shrink-0 bg-blue-100 p-2 rounded-md text-blue-600">
                                        <i class="fas fa-{{ getFileIcon($relatedResource->file_extension) }} fa-lg"></i>
                                    </span>
                                    <h3 class="ml-3 text-lg font-semibold text-gray-900 truncate">{{ $relatedResource->title }}</h3>
                                </div>
                                <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $relatedResource->description }}</p>
                                <div class="flex flex-wrap justify-between items-center">
                                    <span class="text-xs text-gray-500 mb-2 md:mb-0">
                                        {{ $relatedResource->formatted_file_size }} • {{ strtoupper($relatedResource->file_extension) }}
                                    </span>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('resources.show', $relatedResource->slug) }}" class="inline-flex items-center px-3 py-1 border border-gray-600 text-gray-600 rounded-md hover:bg-gray-600 hover:text-white transition">
                                            <i class="fas fa-info-circle mr-2"></i> Details
                                        </a>
                                        <a href="{{ route('resources.download', $relatedResource->slug) }}" class="inline-flex items-center px-3 py-1 border border-blue-600 text-blue-600 rounded-md hover:bg-blue-600 hover:text-white transition">
                                            <i class="fas fa-download mr-2"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Back to Resources button -->
        <div class="mt-8 text-center">
            <a href="{{ route('resources.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-arrow-left mr-2"></i> Back to All Resources
            </a>
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