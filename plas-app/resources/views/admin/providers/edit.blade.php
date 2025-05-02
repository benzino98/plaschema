@extends('layouts.admin')

@section('title', 'Edit Healthcare Provider')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.providers.index') }}" class="text-blue-600 hover:text-blue-800 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Back to Providers
        </a>
    </div>

    <h1 class="text-3xl font-bold mb-6">Edit Healthcare Provider</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.providers.update', $provider->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Provider Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $provider->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" required>
                    @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="provider_type" class="block text-gray-700 text-sm font-bold mb-2">Provider Type</label>
                    <select name="provider_type" id="provider_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('provider_type') border-red-500 @enderror" required>
                        <option value="">Select Provider Type</option>
                        <option value="Hospital" {{ old('provider_type', $provider->provider_type) == 'Hospital' ? 'selected' : '' }}>Hospital</option>
                        <option value="Clinic" {{ old('provider_type', $provider->provider_type) == 'Clinic' ? 'selected' : '' }}>Clinic</option>
                        <option value="Medical Center" {{ old('provider_type', $provider->provider_type) == 'Medical Center' ? 'selected' : '' }}>Medical Center</option>
                        <option value="Specialist" {{ old('provider_type', $provider->provider_type) == 'Specialist' ? 'selected' : '' }}>Specialist</option>
                        <option value="Pharmacy" {{ old('provider_type', $provider->provider_type) == 'Pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                        <option value="Laboratory" {{ old('provider_type', $provider->provider_type) == 'Laboratory' ? 'selected' : '' }}>Laboratory</option>
                    </select>
                    @error('provider_type')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea name="description" id="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror" rows="4">{{ old('description', $provider->description) }}</textarea>
                @error('description')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="address" class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                    <textarea name="address" id="address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('address') border-red-500 @enderror" rows="2" required>{{ old('address', $provider->address) }}</textarea>
                    @error('address')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="mb-4">
                        <label for="city" class="block text-gray-700 text-sm font-bold mb-2">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $provider->city) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('city') border-red-500 @enderror" required>
                        @error('city')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                
                    <div class="mb-4">
                        <label for="state" class="block text-gray-700 text-sm font-bold mb-2">State</label>
                        <input type="text" name="state" id="state" value="{{ old('state', $provider->state) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('state') border-red-500 @enderror" required>
                        @error('state')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $provider->phone) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('phone') border-red-500 @enderror" required>
                    @error('phone')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $provider->email) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="website" class="block text-gray-700 text-sm font-bold mb-2">Website</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $provider->website) }}" placeholder="https://example.com" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('website') border-red-500 @enderror">
                    @error('website')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                    <select name="category" id="category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('category') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        <option value="Primary" {{ old('category', $provider->category) == 'Primary' ? 'selected' : '' }}>Primary</option>
                        <option value="Secondary" {{ old('category', $provider->category) == 'Secondary' ? 'selected' : '' }}>Secondary</option>
                        <option value="Tertiary" {{ old('category', $provider->category) == 'Tertiary' ? 'selected' : '' }}>Tertiary</option>
                        <option value="Specialized" {{ old('category', $provider->category) == 'Specialized' ? 'selected' : '' }}>Specialized</option>
                    </select>
                    @error('category')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="services" class="block text-gray-700 text-sm font-bold mb-2">Services Offered</label>
                <textarea name="services" id="services" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('services') border-red-500 @enderror" rows="3" placeholder="List services separated by commas">{{ old('services', is_array($provider->services) ? implode(', ', $provider->services) : $provider->services) }}</textarea>
                <p class="text-gray-500 text-xs mt-1">Enter services separated by commas (e.g., General Medicine, Pediatrics, Surgery)</p>
                @error('services')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="logo" class="block text-gray-700 text-sm font-bold mb-2">Logo</label>
                @if($provider->logo_path)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $provider->logo_path) }}" alt="{{ $provider->name }} Logo" class="h-20 w-20 object-cover rounded" loading="lazy">
                    <p class="text-sm text-gray-500 mt-1">Current logo</p>
                </div>
                @endif
                <input type="file" name="logo" id="logo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('logo') border-red-500 @enderror">
                <p class="text-gray-500 text-xs mt-1">Accepted formats: JPG, PNG, GIF (max: 2MB). Leave blank to keep current logo.</p>
                @error('logo')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $provider->is_featured) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-green-600">
                    <span class="ml-2 text-gray-700">Feature this provider</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Provider
                </button>
                <a href="{{ route('admin.providers.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 