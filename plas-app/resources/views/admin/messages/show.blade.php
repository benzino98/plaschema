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
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-envelope me-1"></i>
                                Message Content
                            </div>
                            <div>
                                @if($message->status === 'new')
                                    <span class="badge bg-danger">New</span>
                                @elseif($message->status === 'read')
                                    <span class="badge bg-primary">Read</span>
                                @elseif($message->status === 'responded')
                                    <span class="badge bg-success">Responded</span>
                                @elseif($message->status === 'archived')
                                    <span class="badge bg-secondary">Archived</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $message->subject }}</h5>
                        <h6 class="card-subtitle mb-3 text-muted">From: {{ $message->name }} &lt;{{ $message->email }}&gt;</h6>
                        
                        <hr class="my-3">
                        
                        <div class="message-content mb-4">
                            {!! nl2br(e($message->message)) !!}
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" class="btn btn-primary me-2">
                                <i class="fas fa-reply"></i> Reply via Email
                            </a>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        Received: {{ $message->created_at->format('F d, Y \a\t h:i A') }}
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-1"></i>
                        Message Information
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">ID:</dt>
                            <dd class="col-sm-8">{{ $message->id }}</dd>
                            
                            <dt class="col-sm-4">Name:</dt>
                            <dd class="col-sm-8">{{ $message->name }}</dd>
                            
                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8">
                                <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                            </dd>
                            
                            <dt class="col-sm-4">Phone:</dt>
                            <dd class="col-sm-8">{{ $message->phone ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Category:</dt>
                            <dd class="col-sm-8">{{ $message->category->name ?? 'N/A' }}</dd>
                            
                            <dt class="col-sm-4">Received:</dt>
                            <dd class="col-sm-8">{{ $message->created_at->format('M d, Y H:i') }}</dd>
                            
                            <dt class="col-sm-4">Status:</dt>
                            <dd class="col-sm-8">
                                @if($message->status === 'new')
                                    <span class="badge bg-danger">New</span>
                                @elseif($message->status === 'read')
                                    <span class="badge bg-primary">Read</span>
                                @elseif($message->status === 'responded')
                                    <span class="badge bg-success">Responded</span>
                                @elseif($message->status === 'archived')
                                    <span class="badge bg-secondary">Archived</span>
                                @endif
                            </dd>
                            
                            @if($message->responded_by)
                                <dt class="col-sm-4">Responded By:</dt>
                                <dd class="col-sm-8">{{ $message->respondedBy->name ?? 'Unknown' }}</dd>
                                
                                <dt class="col-sm-4">Responded At:</dt>
                                <dd class="col-sm-8">{{ $message->responded_at ? $message->responded_at->format('M d, Y H:i') : 'N/A' }}</dd>
                            @endif
                            
                            @if($message->archived_at)
                                <dt class="col-sm-4">Archived At:</dt>
                                <dd class="col-sm-8">{{ $message->archived_at->format('M d, Y H:i') }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-tasks me-1"></i>
                        Actions
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.messages.status.update', $message) }}" method="POST" class="mb-3">
                            @csrf
                            @method('PUT')
                            <div class="input-group">
                                <select name="status" id="status" class="form-select">
                                    <option value="new" {{ $message->status === 'new' ? 'selected' : '' }}>New</option>
                                    <option value="read" {{ $message->status === 'read' ? 'selected' : '' }}>Read</option>
                                    <option value="responded" {{ $message->status === 'responded' ? 'selected' : '' }}>Responded</option>
                                    <option value="archived" {{ $message->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </div>
                        </form>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Messages
                            </a>
                            
                            @if($message->status !== 'responded')
                                <form action="{{ route('admin.messages.respond', $message) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check"></i> Mark as Responded
                                    </button>
                                </form>
                            @endif
                            
                            @if($message->status !== 'archived')
                                <form action="{{ route('admin.messages.archive', $message) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="fas fa-archive"></i> Archive Message
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