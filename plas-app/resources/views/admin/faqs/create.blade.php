@extends('layouts.admin')

@section('title', 'Add FAQ')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.faqs.index') }}" class="text-blue-600 hover:text-blue-800 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Back to FAQs
        </a>
    </div>

    <h1 class="text-3xl font-bold mb-6">Add FAQ</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.faqs.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="question" class="block text-gray-700 text-sm font-bold mb-2">Question</label>
                <input type="text" name="question" id="question" value="{{ old('question') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('question') border-red-500 @enderror" required>
                @error('question')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="answer" class="block text-gray-700 text-sm font-bold mb-2">Answer</label>
                <textarea name="answer" id="answer" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('answer') border-red-500 @enderror" required>{{ old('answer') }}</textarea>
                @error('answer')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                    <select name="category" id="category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('category') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        <option value="General" {{ old('category') == 'General' ? 'selected' : '' }}>General</option>
                        <option value="Enrollment" {{ old('category') == 'Enrollment' ? 'selected' : '' }}>Enrollment</option>
                        <option value="Benefits" {{ old('category') == 'Benefits' ? 'selected' : '' }}>Benefits</option>
                        <option value="Healthcare Providers" {{ old('category') == 'Healthcare Providers' ? 'selected' : '' }}>Healthcare Providers</option>
                        <option value="Claims & Payments" {{ old('category') == 'Claims & Payments' ? 'selected' : '' }}>Claims & Payments</option>
                    </select>
                    @error('category')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="order" class="block text-gray-700 text-sm font-bold mb-2">Display Order</label>
                    <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('order') border-red-500 @enderror">
                    <p class="text-gray-500 text-xs mt-1">Lower numbers appear first</p>
                    @error('order')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-blue-600">
                    <span class="ml-2 text-gray-700">Active (visible on website)</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Create FAQ
                </button>
                <a href="{{ route('admin.faqs.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 