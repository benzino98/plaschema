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
        <form action="{{ route('admin.faqs.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search Box -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input 
                        type="text" 
                        name="search" 
                        id="search"
                        placeholder="Search FAQs..." 
                        value="{{ request('search') }}" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                    >
                </div>
                
                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select 
                        name="category" 
                        id="category" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600"
                    >
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Apply Filters
                    </button>
                    @if(request('search') || request('category'))
                        <a href="{{ route('admin.faqs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            Clear Filters
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Form -->
    <form id="bulk-action-form" action="{{ route('admin.faqs.bulk-action') }}" method="POST">
        @csrf
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center">
                    <input type="checkbox" id="select-all" class="mr-2 h-5 w-5 text-blue-600">
                    <label for="select-all" class="text-sm font-medium text-gray-700">Select All</label>
                </div>
                <div class="flex items-center flex-wrap gap-2">
                    <select name="action" id="bulk-action-select" class="mr-2 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="">-- Select Action --</option>
                        <option value="delete">Delete</option>
                        <option value="change-category">Change Category</option>
                    </select>
                    
                    <div id="category-select-container" class="hidden">
                        <select name="category" class="mr-2 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                            <option value="General">General</option>
                            <option value="new-category">+ Add New Category</option>
                        </select>
                        
                        <input type="text" id="new-category-input" name="new_category" placeholder="Enter new category name" class="hidden mr-2 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    
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
                            Question
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Order
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
                    @forelse($faqs as $faq)
                    <tr>
                        <td class="px-6 py-4">
                            <input type="checkbox" name="ids[]" value="{{ $faq->id }}" class="item-checkbox h-5 w-5 text-blue-600">
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ Str::limit($faq->question, 60) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $faq->category ?? 'General' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $faq->order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $faq->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $faq->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-edit"></i>
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
                            @if(request('search') || request('category'))
                                No FAQs found matching your criteria. <a href="{{ route('admin.faqs.index') }}" class="text-blue-600 hover:underline">Clear filters</a>.
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
    // Make sure DOM is fully loaded before attaching events
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
        
        // Toggle category select visibility based on action
        document.getElementById('bulk-action-select').addEventListener('change', function() {
            const categoryContainer = document.getElementById('category-select-container');
            if (this.value === 'change-category') {
                categoryContainer.classList.remove('hidden');
            } else {
                categoryContainer.classList.add('hidden');
            }
        });
        
        // Handle new category input
        document.querySelector('select[name="category"]').addEventListener('change', function() {
            const newCategoryInput = document.getElementById('new-category-input');
            if (this.value === 'new-category') {
                newCategoryInput.classList.remove('hidden');
                newCategoryInput.focus();
                // Set the actual category field to empty, will be filled by the new category input
                this.selectedIndex = 0;
            } else {
                newCategoryInput.classList.add('hidden');
            }
        });
        
        // Handle new category submission
        document.getElementById('new-category-input').addEventListener('blur', function() {
            if (this.value.trim()) {
                // Find the category select
                const categorySelect = document.querySelector('select[name="category"]');
                
                // Check if this category already exists
                let exists = false;
                for (let i = 0; i < categorySelect.options.length; i++) {
                    if (categorySelect.options[i].value.toLowerCase() === this.value.trim().toLowerCase()) {
                        categorySelect.selectedIndex = i;
                        exists = true;
                        break;
                    }
                }
                
                // If it doesn't exist, add it as a new option
                if (!exists) {
                    const newOption = document.createElement('option');
                    newOption.value = this.value.trim();
                    newOption.text = this.value.trim();
                    
                    // Insert before the "Add New Category" option
                    const addNewOption = categorySelect.querySelector('option[value="new-category"]');
                    categorySelect.insertBefore(newOption, addNewOption);
                    
                    // Select the new option
                    newOption.selected = true;
                }
                
                // Hide the input field
                this.classList.add('hidden');
                this.value = '';
            }
        });
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
        
        // Check if category is selected for change-category action
        if (action === 'change-category') {
            const category = document.querySelector('select[name="category"]').value;
            const newCategory = document.getElementById('new-category-input').value.trim();
            
            if (!category && !newCategory) {
                alert('Please select or enter a category.');
                return false;
            }
            
            // If using new category input, transfer the value to a hidden input
            if (newCategory) {
                // Create hidden input if it doesn't exist
                let hiddenCategoryInput = document.querySelector('input[name="category"]');
                if (!hiddenCategoryInput) {
                    hiddenCategoryInput = document.createElement('input');
                    hiddenCategoryInput.type = 'hidden';
                    hiddenCategoryInput.name = 'category';
                    document.getElementById('bulk-action-form').appendChild(hiddenCategoryInput);
                }
                hiddenCategoryInput.value = newCategory;
            }
        }
        
        // Customized confirmation message based on action
        let message = '';
        
        switch (action) {
            case 'delete':
                message = `Are you sure you want to delete ${selectedCount} selected FAQ(s)? This action cannot be undone.`;
                break;
            case 'change-category':
                const categoryName = document.querySelector('select[name="category"]').value || document.getElementById('new-category-input').value.trim();
                message = `Are you sure you want to change the category of ${selectedCount} selected FAQ(s) to "${categoryName}"?`;
                break;
            default:
                message = `Are you sure you want to perform this action on ${selectedCount} selected FAQ(s)?`;
        }
        
        return confirm(message);
    }
</script>
@endpush
@endsection 