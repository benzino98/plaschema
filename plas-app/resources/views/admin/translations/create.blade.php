@extends('layouts.admin')

@section('title', 'Add Translation')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Add Translation</h1>
            <a href="{{ route('admin.translations.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                Back to Translations
            </a>
        </div>
        
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <form action="{{ route('admin.translations.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label for="locale" class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                        <select name="locale" id="locale" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50">
                            @foreach($locales as $locale)
                                <option value="{{ $locale }}" {{ old('locale') == $locale ? 'selected' : '' }}>{{ $locale }}</option>
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
                                <option value="{{ $group }}" {{ old('group') == $group ? 'selected' : '' }}>{{ $group }}</option>
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
                        <input type="text" name="key" id="key" value="{{ old('key') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50" placeholder="e.g., buttons.submit">
                        @error('key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                    <textarea name="value" id="value" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50" placeholder="Enter translation value">{{ old('value') }}</textarea>
                    @error('value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-[#74BA03] hover:bg-[#65a203] text-white font-bold py-2 px-4 rounded">
                        Save Translation
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
</script>
@endpush
@endsection 