@extends('layouts.admin')

@section('title', 'Edit Translation')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Translation</h1>
            <a href="{{ route('admin.translations.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                Back to Translations
            </a>
        </div>
        
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <form action="{{ route('admin.translations.update', $translation->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label for="locale" class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                        <select name="locale" id="locale" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50">
                            @foreach($locales as $locale)
                                <option value="{{ $locale }}" {{ (old('locale', $translation->locale) == $locale) ? 'selected' : '' }}>{{ $locale }}</option>
                            @endforeach
                        </select>
                        @error('locale')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="group" class="block text-sm font-medium text-gray-700 mb-1">Group</label>
                        <select name="group" id="group" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50">
                            @foreach($groups as $group)
                                <option value="{{ $group }}" {{ (old('group', $translation->group) == $group) ? 'selected' : '' }}>{{ $group }}</option>
                            @endforeach
                            <option value="_new_group_">+ Add New Group</option>
                        </select>
                        <div id="new_group_container" class="mt-2 hidden">
                            <input type="text" name="new_group" id="new_group" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50" placeholder="Enter new group name">
                        </div>
                        @error('group')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="key" class="block text-sm font-medium text-gray-700 mb-1">Key</label>
                        <input type="text" name="key" id="key" value="{{ old('key', $translation->key) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50" placeholder="e.g., buttons.submit">
                        @error('key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                    <textarea name="value" id="value" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50" placeholder="Enter translation value">{{ old('value', $translation->value) }}</textarea>
                    @error('value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                @if(!$translation->is_custom)
                <div class="mt-4 bg-blue-50 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-blue-700">
                                This is a file-based translation. Editing will convert it to a custom translation that overrides the file-based one.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="mt-6 flex justify-between">
                    <div>
                        <form action="{{ route('admin.translations.destroy', $translation->id) }}" method="POST" class="inline-block" id="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="confirmDelete()">
                                Delete Translation
                            </button>
                        </form>
                    </div>
                    <button type="submit" class="bg-[#74BA03] hover:bg-[#65a203] text-white font-bold py-2 px-4 rounded">
                        Update Translation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const groupSelect = document.getElementById('group');
        const newGroupContainer = document.getElementById('new_group_container');
        
        groupSelect.addEventListener('change', function() {
            if (this.value === '_new_group_') {
                newGroupContainer.classList.remove('hidden');
            } else {
                newGroupContainer.classList.add('hidden');
            }
        });
    });
    
    function confirmDelete() {
        if (confirm('Are you sure you want to delete this translation?')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection 