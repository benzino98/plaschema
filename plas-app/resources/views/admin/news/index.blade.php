@extends('layouts.admin')

@section('title', 'Manage News')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">News Management</h1>
        <div class="flex space-x-2">
            @can('view-activity-logs')
            <a href="{{ route('admin.news.activity') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Activity Log
            </a>
            @endcan
            <a href="{{ route('admin.news.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add News
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Search Form -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form action="{{ route('admin.news.index') }}" method="GET" class="flex items-center">
            <div class="flex-grow">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search news by title, excerpt or content..." 
                    value="{{ request('search') }}" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                >
            </div>
            <div class="ml-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Search
                </button>
                @if(request('search'))
                <a href="{{ route('admin.news.index') }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bulk Actions Form -->
    <form id="bulk-action-form" action="{{ route('admin.news.bulk-action') }}" method="POST">
        @csrf
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <div class="p-4 border-b flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="select-all" class="mr-2 h-5 w-5 text-blue-600">
                    <label for="select-all" class="text-sm font-medium text-gray-700">Select All</label>
                </div>
                <div class="flex items-center">
                    <select name="action" class="mr-2 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="">-- Select Action --</option>
                        <option value="publish">Publish</option>
                        <option value="unpublish">Unpublish</option>
                        <option value="feature">Mark as Featured</option>
                        <option value="unfeature">Remove Featured</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" onclick="return confirmBulkAction()">
                        Apply
                    </button>
                </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Select
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Title
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Published
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Featured
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($news as $article)
                    <tr>
                        <td class="px-6 py-4">
                            <input type="checkbox" name="ids[]" value="{{ $article->id }}" class="item-checkbox h-5 w-5 text-blue-600">
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ Str::limit($article->title, 60) }}</div>
                            <div class="text-sm text-gray-500">{{ $article->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($article->published_at)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ \Carbon\Carbon::parse($article->published_at)->format('M d, Y') }}
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $article->is_featured ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $article->is_featured ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.news.edit', $article->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.news.show', $article->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.news.destroy', $article->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this news article?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            @if(request('search'))
                                No news articles found matching "{{ request('search') }}". <a href="{{ route('admin.news.index') }}" class="text-blue-600 hover:underline">Clear search</a>.
                            @else
                                No news articles found. <a href="{{ route('admin.news.create') }}" class="text-blue-600 hover:underline">Create one now</a>.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
    
    <div class="mt-4">
        {{ $news->links() }}
    </div>
</div>

@push('scripts')
<script>
    // Make sure DOM is fully loaded before attaching event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Select All functionality
        const selectAllCheckbox = document.getElementById('select-all');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('click', function() {
                // Toggle state for all checkboxes to match select-all checkbox
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
            
            // Also monitor individual checkboxes to update "select all" state
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('click', function() {
                    // If any checkbox is unchecked, uncheck "select all"
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } 
                    // If all checkboxes are checked, check "select all"
                    else {
                        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                        selectAllCheckbox.checked = allChecked;
                    }
                });
            });
        }
    });
    
    // Confirm bulk action
    function confirmBulkAction() {
        const selectedCount = document.querySelectorAll('.item-checkbox:checked').length;
        if (selectedCount === 0) {
            alert('Please select at least one item.');
            return false;
        }
        
        const action = document.querySelector('select[name="action"]').value;
        if (!action) {
            alert('Please select an action to perform.');
            return false;
        }
        
        // Customized confirmation message based on action
        let message = '';
        
        switch (action) {
            case 'delete':
                message = `Are you sure you want to delete ${selectedCount} selected item(s)? This action cannot be undone.`;
                break;
            case 'publish':
                message = `Are you sure you want to publish ${selectedCount} selected item(s)?`;
                break;
            case 'unpublish':
                message = `Are you sure you want to unpublish ${selectedCount} selected item(s)?`;
                break;
            case 'feature':
                message = `Are you sure you want to mark ${selectedCount} selected item(s) as featured?`;
                break;
            case 'unfeature':
                message = `Are you sure you want to remove ${selectedCount} selected item(s) from featured?`;
                break;
            default:
                message = `Are you sure you want to perform this action on ${selectedCount} selected item(s)?`;
        }
        
        return confirm(message);
    }
</script>
@endpush
@endsection 