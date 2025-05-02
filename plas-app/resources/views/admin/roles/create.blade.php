@extends('layouts.admin')

@section('title', 'Create Role')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Create New Role</h1>
            <a href="{{ route('admin.roles.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                Back to Roles
            </a>
        </div>
        
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3" 
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-3">Permissions</h3>
                    
                    @foreach($permissions as $module => $modulePermissions)
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2 uppercase">{{ $module }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($modulePermissions as $permission)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->id }}"
                                               class="rounded border-gray-300 text-[#74BA03] focus:ring-[#74BA03] h-5 w-5"
                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                        <label for="permission_{{ $permission->id }}" class="ml-2 text-sm text-gray-700">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    
                    @error('permissions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-[#74BA03] hover:bg-[#65a203] text-white font-bold py-2 px-4 rounded">
                        Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 