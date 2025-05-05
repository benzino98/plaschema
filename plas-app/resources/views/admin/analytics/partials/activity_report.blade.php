<!-- Activity Report Partial -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Activity Overview -->
        <div class="bg-gray-50 p-4 rounded-md shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Activity Overview</h2>
            
            <!-- Activity Statistics -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-3 rounded shadow-sm text-center">
                    <p class="text-sm text-gray-500">Total Activities</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $data['total'] }}</p>
                </div>
                <div class="bg-white p-3 rounded shadow-sm text-center">
                    <p class="text-sm text-gray-500">Unique Users</p>
                    <p class="text-2xl font-bold text-gray-800">{{ count($data['by_user']) }}</p>
                </div>
                <div class="bg-white p-3 rounded shadow-sm text-center">
                    <p class="text-sm text-gray-500">Actions</p>
                    <p class="text-2xl font-bold text-gray-800">{{ count($data['by_action']) }}</p>
                </div>
            </div>
            
            <!-- Activities by Action Table -->
            <h3 class="text-md font-semibold mb-2">Activities by Action</h3>
            <div class="overflow-x-auto mb-4">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="py-2 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                            <th class="py-2 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($data['by_action'] as $action => $count)
                        <tr>
                            <td class="py-2 px-4 text-sm text-gray-800">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($action == 'created') bg-green-100 text-green-800
                                    @elseif($action == 'updated') bg-blue-100 text-blue-800
                                    @elseif($action == 'deleted') bg-red-100 text-red-800
                                    @elseif($action == 'logged_in') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $action)) }}
                                </span>
                            </td>
                            <td class="py-2 px-4 text-sm text-gray-800 text-right">{{ $count }}</td>
                            <td class="py-2 px-4 text-sm text-gray-800 text-right">
                                {{ round(($count / $data['total']) * 100, 1) }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Activity by Action Chart -->
            <div class="mt-4">
                <canvas id="activityActionChart" width="400" height="200"></canvas>
            </div>
        </div>
        
        <!-- Entity Types and User Activity -->
        <div class="bg-gray-50 p-4 rounded-md shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Entity Types and User Activity</h2>
            
            <!-- Activities by Entity Type Table -->
            <h3 class="text-md font-semibold mb-2">Activities by Entity Type</h3>
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entity Type</th>
                            <th class="py-2 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                            <th class="py-2 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($data['by_entity_type'] as $type => $count)
                        <tr>
                            <td class="py-2 px-4 text-sm text-gray-800">{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                            <td class="py-2 px-4 text-sm text-gray-800 text-right">{{ $count }}</td>
                            <td class="py-2 px-4 text-sm text-gray-800 text-right">
                                {{ round(($count / $data['total']) * 100, 1) }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Entity Type Chart -->
            <div class="mb-6">
                <canvas id="entityTypeChart" width="400" height="200"></canvas>
            </div>
            
            <!-- Top Users -->
            <h3 class="text-md font-semibold mb-2">Top Users by Activity</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="py-2 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Activities</th>
                            <th class="py-2 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            // Sort users by activity count in descending order
                            arsort($data['by_user']);
                            // Take top 5 users
                            $topUsers = array_slice($data['by_user'], 0, 5, true);
                        @endphp
                        
                        @foreach($topUsers as $user => $count)
                        <tr>
                            <td class="py-2 px-4 text-sm text-gray-800">{{ $user }}</td>
                            <td class="py-2 px-4 text-sm text-gray-800 text-right">{{ $count }}</td>
                            <td class="py-2 px-4 text-sm text-gray-800 text-right">
                                {{ round(($count / $data['total']) * 100, 1) }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Activity Over Time -->
    @if(isset($data['activity_over_time']))
    <div class="mt-6 bg-gray-50 p-4 rounded-md shadow-sm">
        <h2 class="text-lg font-semibold mb-4">Activity Trend</h2>
        <div>
            <canvas id="activityTrendChart" width="800" height="200"></canvas>
        </div>
    </div>
    @endif
</div>

<!-- Report Details -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-lg font-semibold mb-4">Report Details</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-600 mb-1"><span class="font-medium">Report Period:</span> {{ $period }}</p>
            <p class="text-sm text-gray-600 mb-1"><span class="font-medium">Total Activities:</span> {{ $data['total'] }}</p>
            <p class="text-sm text-gray-600 mb-1"><span class="font-medium">Generated On:</span> {{ now()->format('M d, Y H:i') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">
                This report provides a comprehensive analysis of user activities during the selected period, 
                including actions performed, entity types affected, and user-specific metrics. Use this information 
                to monitor system usage, identify active users, and understand patterns of interaction with the platform.
            </p>
        </div>
    </div>
</div> 