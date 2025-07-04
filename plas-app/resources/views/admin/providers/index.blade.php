@extends('layouts.admin')

@section('title', 'Manage Healthcare Providers')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Healthcare Providers</h1>
        <div class="flex space-x-2">
            @can('view-activity-logs')
            <a href="{{ route('admin.providers.activity') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Activity Log
            </a>
            @endcan
            <a href="{{ route('admin.providers.import-form') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
                Import Providers
            </a>
            <a href="{{ route('admin.providers.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Provider
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

    <!-- Search and Filter Form -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form action="{{ route('admin.providers.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search Box -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input 
                        type="text" 
                        name="search" 
                        id="search"
                        placeholder="Search providers..." 
                        value="{{ request('search') }}" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                    >
                </div>
                
                <!-- Type Filter -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Provider Type</label>
                    <select 
                        name="type" 
                        id="type" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                    >
                        <option value="">All Types</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Apply Filters
                    </button>
                    @if(request('search') || request('type'))
                        <a href="{{ route('admin.providers.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            Clear Filters
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Form -->
    <form id="bulk-action-form" action="{{ route('admin.providers.bulk-action') }}" method="POST">
        @csrf
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <div class="p-4 border-b flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="select-all" class="mr-2 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="select-all" class="text-sm font-medium text-gray-700">Select All</label>
                </div>
                <div class="flex items-center">
                    <select name="action" class="mr-2 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="">-- Select Action --</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
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
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                            Select
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Provider
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Location
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($providers as $provider)
                    <tr>
                        <td class="px-4 py-4">
                            <input type="checkbox" name="ids[]" value="{{ $provider->id }}" class="mr-1 item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $provider->name }}</div>
                            <div class="text-sm text-gray-500">{{ $provider->type }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $provider->city }}</div>
                            <div class="text-sm text-gray-500">{{ $provider->state }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $provider->phone }}</div>
                            <div class="text-sm text-gray-500">{{ $provider->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col space-y-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $provider->is_featured ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $provider->is_featured ? 'Featured' : 'Standard' }}
                                </span>
                                @if(isset($provider->is_active))
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $provider->is_active ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $provider->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.providers.edit', $provider->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.providers.show', $provider->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.providers.destroy', $provider->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this provider?');">
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
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            @if(request('search') || request('type'))
                                No healthcare providers found matching your criteria. <a href="{{ route('admin.providers.index') }}" class="text-blue-600 hover:underline">Clear filters</a>.
                            @else
                                No healthcare providers found. <a href="{{ route('admin.providers.create') }}" class="text-blue-600 hover:underline">Create one now</a>.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
    
    <div class="mt-4">
        {{ $providers->links() }}
    </div>
</div>

@push('scripts')
<script>
    // Make sure DOM is fully loaded before attaching event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Fix for individual delete forms
        document.querySelectorAll('form[action*="/admin/providers/"][method="POST"]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.stopPropagation(); // Prevent event bubbling up to parent forms
                
                // Confirm is already handled by the onsubmit attribute
                // No need to add another confirmation dialog
            });
        });
    
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
                message = `Are you sure you want to delete ${selectedCount} selected provider(s)? This action cannot be undone.`;
                break;
            case 'activate':
                message = `Are you sure you want to activate ${selectedCount} selected provider(s)?`;
                break;
            case 'deactivate':
                message = `Are you sure you want to deactivate ${selectedCount} selected provider(s)?`;
                break;
            case 'feature':
                message = `Are you sure you want to mark ${selectedCount} selected provider(s) as featured?`;
                break;
            case 'unfeature':
                message = `Are you sure you want to remove ${selectedCount} selected provider(s) from featured?`;
                break;
            default:
                message = `Are you sure you want to perform this action on ${selectedCount} selected provider(s)?`;
        }
        
        return confirm(message);
    }

    // Add event listener to the bulk action form submit
    document.getElementById('bulk-action-form').addEventListener('submit', function(event) {
        // Prevent form from submitting if no checkboxes are selected
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            event.preventDefault();
            alert('Please select at least one provider.');
            return false;
        }
        
        // Make sure the form submission includes all checked boxes
        // This resolves an issue where the form might be submitted without the proper IDs
        checkedBoxes.forEach(function(checkbox) {
            // Ensure the checkbox value is included in the form submission
            if (!checkbox.getAttribute('name') || checkbox.getAttribute('name') !== 'ids[]') {
                // If the checkbox doesn't have the proper name attribute, add a hidden input
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'ids[]';
                hiddenInput.value = checkbox.value;
                event.target.appendChild(hiddenInput);
            }
        });
        
        return true;
    });
</script>
@endpush
@endsection 