@extends('layouts.admin')

@section('title', 'View Resource')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">Resource Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.resources.edit', $resource) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('admin.resources.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Resource Info -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ $resource->title }}</h2>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-md font-medium text-gray-700 mb-2">Description</h3>
                        <p class="text-gray-600">{{ $resource->description }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-1">Category</h3>
                            <p class="text-gray-600">{{ $resource->category ? $resource->category->name : 'Uncategorized' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-1">Publication Date</h3>
                            <p class="text-gray-600">{{ $resource->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-1">Status</h3>
                            <div class="flex items-center">
                                @if($resource->is_featured)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Featured</span>
                                @endif
                                @if($resource->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 ml-1">Active</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 ml-1">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-1">Downloads</h3>
                            <p class="text-gray-600">{{ $downloadCount }}</p>
                        </div>
                    </div>
                    
                    <!-- File Information -->
                    <div class="border rounded-lg p-4 mb-6">
                        <h3 class="text-md font-medium text-gray-700 mb-2">File Information</h3>
                        <div class="flex items-center">
                            <i class="far fa-file text-2xl text-gray-500 mr-3"></i>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $resource->file_name }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ strtoupper($resource->file_extension ?? 'N/A') }} • 
                                    {{ $resource->file_size ? number_format($resource->file_size / 1024, 2) . ' KB' : 'N/A' }}
                                </div>
                            </div>
                            <a href="{{ route('resources.download', $resource->slug) }}" target="_blank" class="ml-auto text-blue-600 hover:text-blue-800">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex justify-between">
                        <form action="{{ route('admin.resources.destroy', $resource->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this resource? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                <i class="fas fa-trash mr-2"></i> Delete Resource
                            </button>
                        </form>
                        <a href="{{ route('admin.resources.edit', $resource->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-edit mr-2"></i> Edit Resource
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div>
            <!-- Download Statistics -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="bg-indigo-600 px-4 py-3">
                    <h3 class="text-md font-semibold text-white">Download Statistics</h3>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg mb-2">
                        <span class="text-gray-600">Total Downloads</span>
                        <span class="font-semibold">{{ $downloadCount }}</span>
                    </div>
                    <div class="text-right mt-4">
                        <a href="{{ route('admin.resources.stats.downloads', ['resource_id' => $resource->id]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            <i class="fas fa-chart-bar mr-1"></i> View Detailed Statistics
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Activity Log -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-green-600 px-4 py-3">
                    <h3 class="text-md font-semibold text-white">Recent Activity</h3>
                </div>
                <div class="p-4">
                    <div class="text-right mt-2">
                        <a href="{{ route('admin.resources.activity') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                            <i class="fas fa-history mr-1"></i> View All Activity
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 