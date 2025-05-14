@extends('layouts.admin')

@section('title', 'Translation Management')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Translation Management</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.translations.create') }}" class="bg-[#74BA03] hover:bg-[#65a203] text-white font-bold py-2 px-4 rounded">
                    Add Translation
                </a>
                
                <form action="{{ route('admin.translations.import') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Import from Files
                    </button>
                </form>
                
                <form action="{{ route('admin.translations.export') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Export to Files
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="mt-6 bg-white shadow rounded-lg p-4 border-b border-gray-200">
            <h2 class="text-sm font-medium text-gray-700 mb-3">Filter Translations</h2>
            <form action="{{ route('admin.translations.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="locale" class="block text-xs font-medium text-gray-700 mb-1">Language</label>
                    <select name="locale" id="locale" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50 text-sm">
                        <option value="">All Languages</option>
                        @foreach($locales as $locale)
                            <option value="{{ $locale }}" {{ request('locale') == $locale ? 'selected' : '' }}>{{ $locale }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="group" class="block text-xs font-medium text-gray-700 mb-1">Group</label>
                    <select name="group" id="group" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50 text-sm">
                        <option value="">All Groups</option>
                        @foreach($groups as $group)
                            <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="filter" class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                    <select name="filter" id="filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50 text-sm">
                        <option value="">All Types</option>
                        <option value="file" {{ request('filter') == 'file' ? 'selected' : '' }}>File-based</option>
                        <option value="custom" {{ request('filter') == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>
                
                <div>
                    <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search key or value..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50 text-sm">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded text-sm">
                        Filter
                    </button>
                    <a href="{{ route('admin.translations.index') }}" class="ml-2 text-gray-600 hover:text-gray-900 font-medium py-2 px-4 rounded text-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>
        
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Language</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($translations as $translation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $translation->locale }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $translation->group }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $translation->key }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-md truncate">
                                    {{ $translation->value }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $translation->is_custom ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $translation->is_custom ? 'Custom' : 'File-based' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.translations.edit', $translation->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit" title="Edit Translation"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.translations.destroy', $translation->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this translation?')">
                                            <i class="fas fa-trash" title="Delete Translation"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No translations found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $translations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 