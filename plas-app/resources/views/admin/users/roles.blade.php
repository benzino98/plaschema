@extends('layouts.admin')

@section('title', 'Manage User Roles')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Manage Roles for: {{ $user->name }}</h1>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                Back to Users
            </a>
        </div>
        
        <div class="mt-6 bg-white shadow rounded-lg p-6">
            <div class="mb-4">
                <h2 class="text-lg font-medium text-gray-900">User Details</h2>
                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Name</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Email</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('admin.users.roles.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-3">Assign Roles</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($roles as $role)
                            <div class="flex items-center">
                                <input type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->id }}"
                                       class="rounded border-gray-300 text-[#74BA03] focus:ring-[#74BA03] h-5 w-5"
                                       {{ in_array($role->id, old('roles', $userRoles)) ? 'checked' : '' }}>
                                <label for="role_{{ $role->id }}" class="ml-2 text-sm text-gray-700">
                                    {{ $role->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    
                    @error('roles')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-[#74BA03] hover:bg-[#65a203] text-white font-bold py-2 px-4 rounded">
                        Update Roles
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 