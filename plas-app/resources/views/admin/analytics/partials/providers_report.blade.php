<div class="bg-gray-50 rounded-lg p-6 border border-gray-200 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg border border-gray-200 p-6 flex flex-col items-center">
            <h4 class="text-sm font-medium text-gray-500 mb-1">Total Providers</h4>
            <p class="text-3xl font-bold text-blue-600">{{ $data['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6 flex flex-col items-center">
            <h4 class="text-sm font-medium text-gray-500 mb-1">Categories</h4>
            <p class="text-3xl font-bold text-green-600">{{ count($data['by_category']) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6 flex flex-col items-center">
            <h4 class="text-sm font-medium text-gray-500 mb-1">Provider Types</h4>
            <p class="text-3xl font-bold text-purple-600">{{ count($data['by_type']) }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div>
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Providers by Category</h3>
        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data['by_category'] as $category => $count)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $category }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ round(($count / $data['total']) * 100, 1) }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 h-64">
            <canvas id="providerCategoryChart"></canvas>
        </div>
    </div>
    
    <div>
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Providers by Type</h3>
        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data['by_type'] as $type => $count)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ round(($count / $data['total']) * 100, 1) }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 h-64">
            <canvas id="providerTypeChart"></canvas>
        </div>
    </div>
</div>

<div class="mb-8">
    <h3 class="text-lg font-semibold text-gray-700 mb-4">Providers by City</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data['by_city'] as $city => $count)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $city }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ round(($count / $data['total']) * 100, 1) }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="h-80">
            <canvas id="providerCityChart"></canvas>
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
        <p class="mb-2">Report Type: Healthcare Providers</p>
        <p>This report provides detailed analysis of healthcare providers categorized by type, location, and other relevant metrics during the selected period.</p>
    </div>
</div>