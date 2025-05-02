@extends('layouts.admin')

@section('title', 'Edit News Article')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.news.index') }}" class="text-blue-600 hover:text-blue-800 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Back to News
        </a>
    </div>

    <h1 class="text-3xl font-bold mb-6">Edit News Article</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $news->title) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('title') border-red-500 @enderror" required>
                @error('title')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="slug" class="block text-gray-700 text-sm font-bold mb-2">Slug (URL identifier)</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $news->slug) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('slug') border-red-500 @enderror">
                <p class="text-gray-500 text-xs mt-1">Leave blank to auto-generate from title</p>
                @error('slug')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="excerpt" class="block text-gray-700 text-sm font-bold mb-2">Excerpt (Short description)</label>
                <textarea name="excerpt" id="excerpt" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('excerpt') border-red-500 @enderror" rows="3" required>{{ old('excerpt', $news->excerpt) }}</textarea>
                @error('excerpt')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Content</label>
                <textarea name="content" id="content" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('content') border-red-500 @enderror" rows="10" required>{{ old('content', $news->content) }}</textarea>
                @error('content')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Featured Image</label>
                @if($news->image_path)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $news->image_path) }}" alt="{{ $news->title }}" class="w-48 h-auto mb-2" loading="lazy">
                    <p class="text-sm text-gray-500">Current image</p>
                </div>
                @endif
                <input type="file" name="image" id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('image') border-red-500 @enderror">
                <p class="text-gray-500 text-xs mt-1">Accepted formats: JPG, PNG, GIF (max: 2MB). Leave blank to keep current image.</p>
                @error('image')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="published_at" class="block text-gray-700 text-sm font-bold mb-2">Publish Date</label>
                <input type="date" name="published_at" id="published_at" value="{{ old('published_at', $news->published_at ? $news->published_at->format('Y-m-d') : '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('published_at') border-red-500 @enderror">
                <p class="text-gray-500 text-xs mt-1">Leave blank to save as draft</p>
                @error('published_at')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $news->is_featured) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-blue-600">
                    <span class="ml-2 text-gray-700">Feature this article</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update News Article
                </button>
                <a href="{{ route('admin.news.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-generate slug from title if slug is empty
    document.getElementById('title').addEventListener('blur', function() {
        const title = this.value;
        const slugField = document.getElementById('slug');
        
        if (slugField.value === '') {
            // Only auto-generate if slug field is empty
            slugField.value = title
                .toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-') // Replace spaces with hyphens
                .replace(/--+/g, '-') // Replace multiple hyphens with single hyphen
                .trim(); // Trim leading/trailing spaces
        }
    });
</script>
@endsection 