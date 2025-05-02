@extends('layouts.admin')

@section('title', 'View Message')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Message Details</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.messages.index') }}">Contact Messages</a></li>
            <li class="breadcrumb-item active">View Message</li>
        </ol>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="card shadow-sm mb-4 bg-white rounded-lg overflow-hidden">
                    <div class="bg-white border-b px-6 py-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-plaschema mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                                <span class="font-semibold text-lg">Message Content</span>
                            </div>
                            <div>
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
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h2 class="text-xl font-bold mb-2">{{ $message->subject }}</h2>
                        <p class="text-sm text-gray-600 mb-6">
                            From: {{ $message->name }} &lt;{{ $message->email }}&gt;
                        </p>
                        
                        <div class="border-t border-b border-gray-200 py-6 my-6">
                            <div class="prose max-w-none">
                                {!! nl2br(e($message->message)) !!}
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Reply via Email
                            </a>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 text-sm text-gray-500">
                        Received: {{ $message->created_at->format('F d, Y \a\t h:i A') }}
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-1">
                <div class="card shadow-sm mb-4 bg-white rounded-lg overflow-hidden">
                    <div class="bg-white border-b px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-plaschema mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold">Message Information</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <dl class="divide-y divide-gray-200">
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">ID:</dt>
                                <dd class="text-sm text-gray-900 mt-0">{{ $message->id }}</dd>
                            </div>
                            
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Name:</dt>
                                <dd class="text-sm text-gray-900 mt-0">{{ $message->name }}</dd>
                            </div>
                            
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Email:</dt>
                                <dd class="text-sm text-gray-900 mt-0">
                                    <a href="mailto:{{ $message->email }}" class="text-plaschema hover:text-plaschema-dark">
                                        {{ $message->email }}
                                    </a>
                                </dd>
                            </div>
                            
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Phone:</dt>
                                <dd class="text-sm text-gray-900 mt-0">{{ $message->phone ?? 'N/A' }}</dd>
                            </div>
                            
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Category:</dt>
                                <dd class="text-sm text-gray-900 mt-0">{{ $message->category->name ?? 'N/A' }}</dd>
                            </div>
                            
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Received:</dt>
                                <dd class="text-sm text-gray-900 mt-0">{{ $message->created_at->format('M d, Y H:i') }}</dd>
                            </div>
                            
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Status:</dt>
                                <dd class="text-sm text-gray-900 mt-0">
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
                                </dd>
                            </div>
                            
                            @if($message->responded_by)
                                <div class="py-3 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Responded By:</dt>
                                    <dd class="text-sm text-gray-900 mt-0">{{ $message->respondedBy->name ?? 'Unknown' }}</dd>
                                </div>
                                
                                <div class="py-3 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Responded At:</dt>
                                    <dd class="text-sm text-gray-900 mt-0">{{ $message->responded_at ? $message->responded_at->format('M d, Y H:i') : 'N/A' }}</dd>
                                </div>
                            @endif
                            
                            @if($message->archived_at)
                                <div class="py-3 flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Archived At:</dt>
                                    <dd class="text-sm text-gray-900 mt-0">{{ $message->archived_at->format('M d, Y H:i') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
                
                <div class="card shadow-sm mb-4 bg-white rounded-lg overflow-hidden">
                    <div class="bg-white border-b px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-plaschema mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-semibold">Actions</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('admin.messages.status.update', $message) }}" method="POST" class="mb-6">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Change Status</label>
                                <div class="flex">
                                    <select name="status" id="status" class="form-select rounded-none rounded-l-md shadow-sm flex-1 border-gray-300 focus:border-plaschema focus:ring focus:ring-plaschema focus:ring-opacity-50">
                                        <option value="new" {{ $message->status === 'new' ? 'selected' : '' }}>New</option>
                                        <option value="read" {{ $message->status === 'read' ? 'selected' : '' }}>Read</option>
                                        <option value="responded" {{ $message->status === 'responded' ? 'selected' : '' }}>Responded</option>
                                        <option value="archived" {{ $message->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                    </select>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-none rounded-r-md shadow-sm text-sm font-medium text-white bg-plaschema hover:bg-plaschema-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-plaschema">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="space-y-3">
                            <a href="{{ route('admin.messages.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-plaschema">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                </svg>
                                Back to Messages
                            </a>
                            
                            @if($message->status !== 'responded')
                                <form action="{{ route('admin.messages.respond', $message) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Mark as Responded
                                    </button>
                                </form>
                            @endif
                            
                            @if($message->status !== 'archived')
                                <form action="{{ route('admin.messages.archive', $message) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                                            <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Archive Message
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 