<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div>
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Content Overview</h3>
        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data['content'] as $key => $value)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $value }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 h-64">
            <canvas id="contentChart"></canvas>
        </div>
    </div>
    
    <div>
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Messages by Status</h3>
        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data['messages']['by_status'] as $status => $count)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($status) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 h-64">
            <canvas id="messagesStatusChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Activity Summary</h3>
        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
            <p class="mb-4">Total Activities: <span class="font-semibold">{{ $data['activity']['total'] }}</span></p>
            <div class="bg-white rounded border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data['activity']['by_action'] as $action => $count)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($action) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div>
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Activity by Action</h3>
        <div class="h-80">
            <canvas id="activityActionChart"></canvas>
        </div>
    </div>
</div>

<div class="mt-8">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-700">Report Details</h3>
        <div class="text-sm text-gray-500">
            Period: {{ $data['period']['start_date'] }} to {{ $data['period']['end_date'] }}
        </div>
    </div>
    
    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
        <p class="mb-2">Report Generated: {{ date('F d, Y H:i:s') }}</p>
        <p class="mb-2">Report Type: Summary</p>
        <p>This report provides an overview of system content, message statistics, and user activities during the selected period.</p>
    </div>
</div> 