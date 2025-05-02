@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">News Management</h2>
            <p class="text-gray-600 mb-4">Manage news articles and announcements</p>
            <a href="{{ route('admin.news.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg">
                Manage News
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Healthcare Providers</h2>
            <p class="text-gray-600 mb-4">Manage healthcare provider listings</p>
            <a href="{{ route('admin.providers.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg">
                Manage Providers
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">FAQ Management</h2>
            <p class="text-gray-600 mb-4">Manage frequently asked questions</p>
            <a href="{{ route('admin.faqs.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg">
                Manage FAQs
            </a>
        </div>
    </div>
</div>
@endsection 