@extends('layouts.admin')

@section('title', 'Activity Log Details')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Activity Log Details</h1>
            <a href="{{ route('admin.activities.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                Back to Logs
            </a>
        </div>
        
        <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
                
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Date & Time</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $activityLog->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">User</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $activityLog->user ? $activityLog->user->name : 'System' }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Action</h3>
                        <p class="mt-1 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($activityLog->action === 'created') bg-green-100 text-green-800
                                @elseif($activityLog->action === 'updated') bg-blue-100 text-blue-800
                                @elseif($activityLog->action === 'deleted') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst($activityLog->action) }}
                            </span>
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Entity</h3>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($activityLog->entity_type)
                                {{ class_basename($activityLog->entity_type) }}
                                @if($activityLog->entity_id)
                                    #{{ $activityLog->entity_id }}
                                @endif
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500">Description</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $activityLog->description }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">IP Address</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $activityLog->ip_address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            
            @if($activityLog->old_values || $activityLog->new_values)
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Data Changes</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Old Value</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Value</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @if($activityLog->old_values && $activityLog->new_values)
                                    @foreach(array_keys(array_merge($activityLog->old_values, $activityLog->new_values)) as $field)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $field }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                @if(isset($activityLog->old_values[$field]))
                                                    @if(is_array($activityLog->old_values[$field]))
                                                        <pre class="text-xs">{{ json_encode($activityLog->old_values[$field], JSON_PRETTY_PRINT) }}</pre>
                                                    @else
                                                        {{ $activityLog->old_values[$field] }}
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 italic">No value</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                @if(isset($activityLog->new_values[$field]))
                                                    @if(is_array($activityLog->new_values[$field]))
                                                        <pre class="text-xs">{{ json_encode($activityLog->new_values[$field], JSON_PRETTY_PRINT) }}</pre>
                                                    @else
                                                        {{ $activityLog->new_values[$field] }}
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 italic">No value</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @elseif($activityLog->old_values)
                                    @foreach($activityLog->old_values as $field => $value)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $field }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                @if(is_array($value))
                                                    <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <span class="text-gray-400 italic">No value</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @elseif($activityLog->new_values)
                                    @foreach($activityLog->new_values as $field => $value)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $field }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <span class="text-gray-400 italic">No value</span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                @if(is_array($value))
                                                    <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 