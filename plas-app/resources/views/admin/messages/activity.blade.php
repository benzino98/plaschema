@extends('layouts.admin')

@section('title', 'Contact Message Activity Logs')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Contact Message Activity Logs</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.messages.index') }}">Contact Messages</a></li>
            <li class="breadcrumb-item active">Activity Logs</li>
        </ol>
        
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-history me-1"></i>
                        Activity Logs
                    </div>
                    <div>
                        <a href="{{ route('admin.messages.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Back to Messages
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Performed By</th>
                                <th>Details</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>
                                        @if($log->action === 'create')
                                            <span class="badge bg-success">Created</span>
                                        @elseif($log->action === 'update')
                                            <span class="badge bg-primary">Updated</span>
                                        @elseif($log->action === 'delete')
                                            <span class="badge bg-danger">Deleted</span>
                                        @elseif($log->action === 'view')
                                            <span class="badge bg-info">Viewed</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->user->name ?? 'System' }}</td>
                                    <td>
                                        @if(isset($log->details['message']))
                                            {{ $log->details['message'] }}
                                        @elseif(isset($log->details['old_status']) && isset($log->details['new_status']))
                                            Changed status from <strong>{{ $log->details['old_status'] }}</strong> to <strong>{{ $log->details['new_status'] }}</strong>
                                        @else
                                            @foreach($log->details as $key => $value)
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                                @if(is_array($value))
                                                    {{ json_encode($value) }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                                <br>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No activity logs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection 