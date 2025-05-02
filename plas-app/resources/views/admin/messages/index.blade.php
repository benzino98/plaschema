@extends('layouts.admin')

@section('title', 'Manage Contact Messages')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Contact Messages</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Contact Messages</li>
        </ol>
        
        <div class="card shadow-sm mb-4 bg-white rounded-lg">
            <div class="card-header bg-white py-3 border-b">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-plaschema mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        <span class="font-semibold text-lg">All Messages</span>
                    </div>
                    <div>
                        <a href="{{ route('admin.messages.activity') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-plaschema">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Activity Logs
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-6">
                <!-- Filters -->
                <div class="mb-6">
                    <div class="p-5 bg-gray-50 rounded-lg border mb-6">
                        <form action="{{ route('admin.messages.index') }}" method="GET">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-plaschema focus:ring focus:ring-plaschema focus:ring-opacity-50">
                                        <option value="">All Statuses</option>
                                        <option value="new" {{ $status === 'new' ? 'selected' : '' }}>New</option>
                                        <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Read</option>
                                        <option value="responded" {{ $status === 'responded' ? 'selected' : '' }}>Responded</option>
                                        <option value="archived" {{ $status === 'archived' ? 'selected' : '' }}>Archived</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <select name="category_id" id="category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-plaschema focus:ring focus:ring-plaschema focus:ring-opacity-50">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                                    <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-plaschema focus:ring focus:ring-plaschema focus:ring-opacity-50" 
                                           id="date_from" name="date_from" value="{{ $dateFrom }}">
                                </div>
                                <div>
                                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                                    <input type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-plaschema focus:ring focus:ring-plaschema focus:ring-opacity-50" 
                                           id="date_to" name="date_to" value="{{ $dateTo }}">
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end space-x-2">
                                <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-plaschema">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                    </svg>
                                    Reset
                                </a>
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-plaschema hover:bg-plaschema-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-plaschema">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"></path>
                                    </svg>
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Messages Table -->
                <div class="overflow-x-auto bg-white shadow-sm rounded-lg border">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($messages as $message)
                                <tr class="{{ !$message->is_read ? 'bg-blue-50' : '' }} hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $message->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $message->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="mailto:{{ $message->email }}" class="text-plaschema hover:text-plaschema-dark">
                                            {{ $message->email }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $message->category->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($message->subject, 30) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($message->status === 'new')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                New
                                            </span>
                                        @elseif($message->status === 'read')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Read
                                            </span>
                                        @elseif($message->status === 'responded')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Responded
                                            </span>
                                        @elseif($message->status === 'archived')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Archived
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $message->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-1">
                                        <a href="{{ route('admin.messages.show', $message) }}" class="text-indigo-600 hover:text-indigo-900 inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-50 hover:bg-indigo-100" title="View">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        
                                        @if($message->status !== 'responded')
                                            <form action="{{ route('admin.messages.respond', $message) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="text-green-600 hover:text-green-900 inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-50 hover:bg-green-100" title="Mark as Responded">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($message->status !== 'archived')
                                            <form action="{{ route('admin.messages.archive', $message) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="text-gray-600 hover:text-gray-900 inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-50 hover:bg-gray-100" title="Archive">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
                                                        <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">No messages found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6 px-2">
                    {{ $messages->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection 