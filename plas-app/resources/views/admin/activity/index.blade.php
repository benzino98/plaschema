@extends('layouts.admin')

@section('title', 'Activity Logs')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Activity Logs</h1>
        </div>
        
        <div class="mt-6 bg-white shadow rounded-lg">
            <!-- Filters -->
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-sm font-medium text-gray-700 mb-3">Filter Logs</h2>
                <form action="{{ route('admin.activities.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="action" class="block text-xs font-medium text-gray-700 mb-1">Action</label>
                        <select name="action" id="action" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50 text-sm">
                            <option value="">All Actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="entity_type" class="block text-xs font-medium text-gray-700 mb-1">Entity Type</label>
                        <select name="entity_type" id="entity_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50 text-sm">
                            <option value="">All Entities</option>
                            @foreach($entityTypes as $type)
                                @if($type)
                                    <option value="{{ $type }}" {{ request('entity_type') == $type ? 'selected' : '' }}>{{ class_basename($type) }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="from_date" class="block text-xs font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50 text-sm">
                    </div>
                    
                    <div>
                        <label for="to_date" class="block text-xs font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#74BA03] focus:ring focus:ring-[#74BA03] focus:ring-opacity-50 text-sm">
                    </div>
                    
                    <div class="md:col-span-4 flex justify-end">
                        <button type="submit" class="bg-[#74BA03] hover:bg-[#65a203] text-white font-bold py-2 px-4 rounded text-sm">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Logs Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entity</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->user ? $log->user->name : 'System' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($log->action === 'created') bg-green-100 text-green-800
                                        @elseif($log->action === 'updated') bg-blue-100 text-blue-800
                                        @elseif($log->action === 'deleted') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($log->entity_type)
                                        {{ class_basename($log->entity_type) }}
                                        @if($log->entity_id)
                                            #{{ $log->entity_id }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-sm truncate">
                                    {{ $log->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.activities.show', $log) }}" class="text-indigo-600 hover:text-indigo-900">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No activity logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 