@extends('layouts.admin')

@section('title', 'Manage FAQs')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">FAQ Management</h1>
        <div class="flex space-x-2">
            @can('view-activity-logs')
            <a href="{{ route('admin.faqs.activity') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Activity Log
            </a>
            @endcan
            <a href="{{ route('admin.faqs.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add FAQ
            </a>
        </div>
    </div>

    <!-- Health Plan Info Message -->
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6" role="alert">
        <span class="font-bold">Health Plan Page Display:</span>
        <span class="block sm:inline">Only the first 3 FAQs with "Healthcare Plans" category and "Show on Health Plan Page" enabled will be displayed on the Health Plan page.</span>
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
        <form action="{{ route('admin.faqs.index') }}" method="GET" class="flex items-center">
            <div class="flex-grow">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search FAQs by question or answer..." 
                    value="{{ request('search') }}" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                >
            </div>
            <div class="ml-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Search
                </button>
                @if(request('search'))
                <a href="{{ route('admin.faqs.index') }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bulk Actions Form -->
    <form id="bulk-action-form" action="{{ route('admin.faqs.bulk-action') }}" method="POST">
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
                        <option value="delete">Delete</option>
                        <option value="change-category">Change Category</option>
                        <option value="show-on-plans-page">Show on Health Plan Page</option>
                        <option value="hide-from-plans-page">Hide from Health Plan Page</option>
                    </select>
                    <div id="category-select-container" class="hidden mr-2">
                        <select name="category" class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                            <option value="General">General</option>
                            <option value="new-category">+ Add New Category</option>
                        </select>
                    </div>
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
                            Question
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Health Plan
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($faqs as $faq)
                    <tr>
                        <td class="px-4 py-4">
                            <input type="checkbox" name="ids[]" value="{{ $faq->id }}" class="mr-1 item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ Str::limit($faq->question, 60) }}</div>
                            <div class="text-sm text-gray-500">Order: {{ $faq->order }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $faq->category ?? 'General' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $faq->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $faq->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $faq->show_on_plans_page ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $faq->show_on_plans_page ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this FAQ?');">
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
                            @if(request('search'))
                                No FAQs found matching "{{ request('search') }}". <a href="{{ route('admin.faqs.index') }}" class="text-blue-600 hover:underline">Clear search</a>.
                            @else
                                No FAQs found. <a href="{{ route('admin.faqs.create') }}" class="text-blue-600 hover:underline">Create one now</a>.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
    
    <div class="mt-4">
        {{ $faqs->links() }}
    </div>
</div>

@push('scripts')
<script>
    // Make sure DOM is fully loaded before attaching event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Fix for individual delete forms
        document.querySelectorAll('form[action*="/admin/faqs/"][method="POST"]').forEach(function(form) {
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
        
        // Show/hide category selector based on bulk action
        document.querySelector('select[name="action"]').addEventListener('change', function() {
            const categoryContainer = document.getElementById('category-select-container');
            if (this.value === 'change-category') {
                categoryContainer.classList.remove('hidden');
            } else {
                categoryContainer.classList.add('hidden');
            }
        });
    });
    
    // Confirm bulk action
    function confirmBulkAction() {
        const selectedCount = document.querySelectorAll('.item-checkbox:checked').length;
        if (selectedCount === 0) {
            alert('Please select at least one FAQ.');
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
                message = `Are you sure you want to delete ${selectedCount} selected FAQ(s)? This action cannot be undone.`;
                break;
            case 'change-category':
                const category = document.querySelector('select[name="category"]').value;
                if (!category) {
                    alert('Please select a category.');
                    return false;
                }
                message = `Are you sure you want to change the category of ${selectedCount} selected FAQ(s)?`;
                break;
            case 'show-on-plans-page':
                message = `Are you sure you want to show ${selectedCount} selected FAQ(s) on the Health Plan page?`;
                break;
            case 'hide-from-plans-page':
                message = `Are you sure you want to hide ${selectedCount} selected FAQ(s) from the Health Plan page?`;
                break;
            default:
                message = `Are you sure you want to perform this action on ${selectedCount} selected FAQ(s)?`;
        }
        
        return confirm(message);
    }

    // Add event listener to the bulk action form submit
    document.getElementById('bulk-action-form').addEventListener('submit', function(event) {
        // Prevent form from submitting if no checkboxes are selected
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (checkedBoxes.length === 0) {
            event.preventDefault();
            alert('Please select at least one FAQ.');
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