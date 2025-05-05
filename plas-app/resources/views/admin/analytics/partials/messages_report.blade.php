<!-- Messages Report Partial -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Messages Overview -->
        <div class="bg-gray-50 p-4 rounded-md shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Messages Overview</h2>
            
            <!-- Message Statistics -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-3 rounded shadow-sm text-center">
                    <p class="text-sm text-gray-500">Total Messages</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $data['total'] }}</p>
                </div>
                <div class="bg-white p-3 rounded shadow-sm text-center">
                    <p class="text-sm text-gray-500">Categories</p>
                    <p class="text-2xl font-bold text-gray-800">{{ count($data['by_category']) }}</p>
                </div>
                <div class="bg-white p-3 rounded shadow-sm text-center">
                    <p class="text-sm text-gray-500">Avg. Response Time</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $data['avg_response_time'] ?? 'N/A' }}</p>
                </div>
            </div>
            
            <!-- Messages by Status Table -->
            <h3 class="text-md font-semibold mb-2">Messages by Status</h3>
            <div class="overflow-x-auto mb-4">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="py-2 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                            <th class="py-2 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($data['by_status'] as $status => $count)
                        <tr>
                            <td class="py-2 px-4 text-sm text-gray-800">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($status == 'new') bg-blue-100 text-blue-800
                                    @elseif($status == 'in_progress') bg-yellow-100 text-yellow-800
                                    @elseif($status == 'resolved') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
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
            
            <!-- Messages by Status Chart -->
            <div class="mt-6">
                <canvas id="messagesStatusChart" width="400" height="200"></canvas>
            </div>
        </div>
        
        <!-- Categories and Response Time -->
        <div class="bg-gray-50 p-4 rounded-md shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Categories and Response</h2>
            
            <!-- Messages by Category Table -->
            <h3 class="text-md font-semibold mb-2">Messages by Category</h3>
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="py-2 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                            <th class="py-2 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($data['by_category'] as $category => $count)
                        <tr>
                            <td class="py-2 px-4 text-sm text-gray-800">{{ ucfirst(str_replace('_', ' ', $category)) }}</td>
                            <td class="py-2 px-4 text-sm text-gray-800 text-right">{{ $count }}</td>
                            <td class="py-2 px-4 text-sm text-gray-800 text-right">
                                {{ round(($count / $data['total']) * 100, 1) }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Messages by Category Chart -->
            <div class="mb-6">
                <canvas id="messagesCategoryChart" width="400" height="200"></canvas>
            </div>
            
            <!-- Response Time Chart -->
            @if(isset($data['response_time_trend']))
            <h3 class="text-md font-semibold mb-2">Response Time Trend</h3>
            <div class="mt-4">
                <canvas id="responseTimeChart" width="400" height="200"></canvas>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Report Details -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-lg font-semibold mb-4">Report Details</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-600 mb-1"><span class="font-medium">Report Period:</span> {{ $period }}</p>
            <p class="text-sm text-gray-600 mb-1"><span class="font-medium">Total Messages:</span> {{ $data['total'] }}</p>
            <p class="text-sm text-gray-600 mb-1"><span class="font-medium">Generated On:</span> {{ now()->format('M d, Y H:i') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">
                This report provides a comprehensive analysis of contact messages received during the selected period, 
                including message volumes by category and status, as well as response time metrics. Use this information 
                to identify trends, evaluate customer service performance, and optimize response strategies.
            </p>
        </div>
    </div>
</div> 