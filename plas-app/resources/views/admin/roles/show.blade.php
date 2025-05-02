@extends('layouts.admin')

@section('title', 'View Role')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Role Details: {{ $role->name }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.roles.edit', $role) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Edit Role
                </a>
                <a href="{{ route('admin.roles.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                    Back to Roles
                </a>
            </div>
        </div>
        
        <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Role Information</h2>
                
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Name</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $role->name }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Slug</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $role->slug }}</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500">Description</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $role->description ?? 'No description provided' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Permissions</h2>
                
                @if($role->permissions->isEmpty())
                    <p class="mt-4 text-sm text-gray-500">This role has no permissions assigned.</p>
                @else
                    <div class="mt-4">
                        @php
                            $permissionsByModule = $role->permissions->groupBy('module');
                        @endphp
                        
                        @foreach($permissionsByModule as $module => $permissions)
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-700 uppercase">{{ $module }}</h3>
                                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach($permissions as $permission)
                                        <div class="px-3 py-1 bg-gray-100 rounded-md text-sm text-gray-700">
                                            {{ $permission->name }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 