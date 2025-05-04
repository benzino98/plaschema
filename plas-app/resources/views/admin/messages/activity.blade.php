@extends('layouts.admin')

@section('title', 'Contact Message Activity Logs')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4 text-2xl font-bold">Contact Message Activity Logs</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.messages.index') }}">Contact Messages</a></li>
            <li class="breadcrumb-item active">Activity Logs</li>
        </ol>
        
        <div class="card shadow-sm mb-4 bg-white rounded-lg">
            <div class="card-header bg-white py-3 border-b">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-plaschema mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-semibold text-lg">Activity Logs</span>
                    </div>
                    <div>
                        <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-plaschema">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            Back to Messages
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-6">
                <div class="overflow-x-auto bg-white shadow-sm rounded-lg border">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performed By</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($log->action === 'create')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Created</span>
                                        @elseif($log->action === 'update')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Updated</span>
                                        @elseif($log->action === 'delete')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Deleted</span>
                                        @elseif($log->action === 'view')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Viewed</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($log->action) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        @if($log->description)
                                            {{ $log->description }}
                                        @elseif($log->old_values && isset($log->old_values['old_status']) && isset($log->new_values['new_status']))
                                            Changed status from <strong>{{ $log->old_values['old_status'] }}</strong> to <strong>{{ $log->new_values['new_status'] }}</strong>
                                        @elseif($log->old_values && is_array($log->old_values))
                                            <div class="text-xs text-gray-500">
                                                <p>Old values:</p>
                                                <pre class="mt-1 bg-gray-50 p-2 rounded overflow-auto max-h-20">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        @elseif($log->new_values && is_array($log->new_values))
                                            <div class="text-xs text-gray-500">
                                                <p>New values:</p>
                                                <pre class="mt-1 bg-gray-50 p-2 rounded overflow-auto max-h-20">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">No details available</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->created_at->format('M d, Y H:i:s') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">No activity logs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-6 px-2">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection 