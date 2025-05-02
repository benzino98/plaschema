@extends('layouts.admin')

@section('title', 'Manage Contact Messages')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Contact Messages</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Contact Messages</li>
        </ol>
        
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-envelope me-1"></i>
                        All Messages
                    </div>
                    <div>
                        <a href="{{ route('admin.messages.activity') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-history"></i> Activity Logs
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.messages.index') }}" method="GET" class="row g-3">
                                    <div class="col-md-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-select">
                                            <option value="">All Statuses</option>
                                            <option value="new" {{ $status === 'new' ? 'selected' : '' }}>New</option>
                                            <option value="read" {{ $status === 'read' ? 'selected' : '' }}>Read</option>
                                            <option value="responded" {{ $status === 'responded' ? 'selected' : '' }}>Responded</option>
                                            <option value="archived" {{ $status === 'archived' ? 'selected' : '' }}>Archived</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select name="category_id" id="category_id" class="form-select">
                                            <option value="">All Categories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="date_from" class="form-label">Date From</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="date_to" class="form-label">Date To</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}">
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Messages Table -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Category</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Received</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($messages as $message)
                                <tr class="{{ $message->is_read ? '' : 'table-primary' }}">
                                    <td>{{ $message->id }}</td>
                                    <td>{{ $message->name }}</td>
                                    <td>
                                        <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                                    </td>
                                    <td>{{ $message->category->name ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($message->subject, 30) }}</td>
                                    <td>
                                        @if($message->status === 'new')
                                            <span class="badge bg-danger">New</span>
                                        @elseif($message->status === 'read')
                                            <span class="badge bg-primary">Read</span>
                                        @elseif($message->status === 'responded')
                                            <span class="badge bg-success">Responded</span>
                                        @elseif($message->status === 'archived')
                                            <span class="badge bg-secondary">Archived</span>
                                        @endif
                                    </td>
                                    <td>{{ $message->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($message->status !== 'responded')
                                            <form action="{{ route('admin.messages.respond', $message) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success" title="Mark as Responded">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($message->status !== 'archived')
                                            <form action="{{ route('admin.messages.archive', $message) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-secondary" title="Archive">
                                                    <i class="fas fa-archive"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No messages found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $messages->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection 