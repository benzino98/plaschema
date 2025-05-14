@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Dashboard</h1>
    
    <!-- Statistics Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">News</p>
                    <p class="text-2xl font-semibold">{{ $dashboardData['content']['news']['total'] }}</p>
                    <p class="text-xs text-gray-500">{{ $dashboardData['content']['news']['published'] }} published</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Providers</p>
                    <p class="text-2xl font-semibold">{{ $dashboardData['content']['providers']['total'] }}</p>
                    <p class="text-xs text-gray-500">{{ $dashboardData['content']['providers']['active'] }} active</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">FAQs</p>
                    <p class="text-2xl font-semibold">{{ $dashboardData['content']['faqs']['total'] }}</p>
                    <p class="text-xs text-gray-500">{{ $dashboardData['content']['faqs']['active'] }} active</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Resources</p>
                    <p class="text-2xl font-semibold">{{ $dashboardData['content']['resources']['total'] }}</p>
                    <p class="text-xs text-gray-500">{{ number_format($dashboardData['content']['resources']['download_count']) }} downloads</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Messages</p>
                    <p class="text-2xl font-semibold">{{ $dashboardData['messages']['total'] }}</p>
                    <p class="text-xs text-gray-500">{{ $dashboardData['messages']['new'] }} new</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Action Buttons -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-8">
        <h2 class="text-lg font-medium text-gray-700 mb-4">Quick Actions</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.news.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create News
            </a>
            
            <a href="{{ route('admin.providers.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Provider
            </a>
            
            <a href="{{ route('admin.faqs.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add FAQ
            </a>
            
            <a href="{{ route('admin.resources.create') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Upload Resource
            </a>
            
            <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                View Messages
            </a>
        </div>
    </div>
    
    <!-- Main Dashboard Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Charts Section -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-4 mb-8">
                <h2 class="text-lg font-medium text-gray-700 mb-4">Content Growth</h2>
                <div class="h-72">
                    <canvas id="contentGrowthChart"></canvas>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h2 class="text-lg font-medium text-gray-700 mb-4">Provider Distribution</h2>
                    <div class="h-64">
                        <canvas id="providerDistributionChart"></canvas>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h2 class="text-lg font-medium text-gray-700 mb-4">Download Statistics</h2>
                    <div class="h-64">
                        <canvas id="downloadStatisticsChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h2 class="text-lg font-medium text-gray-700 mb-4">Activity Timeline</h2>
                <div class="h-64">
                    <canvas id="activityTimelineChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Right Sidebar -->
        <div class="lg:col-span-1">
            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-8">
                <h2 class="text-lg font-medium text-gray-700 mb-4">Recent Activity</h2>
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($dashboardData['activity']['recent'] as $activity)
                    <div class="flex items-start pb-4 border-b border-gray-100">
                        <div class="bg-blue-100 text-blue-500 p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-800">
                                <span class="font-medium">{{ $activity->user ? $activity->user->name : 'System' }}</span> 
                                {{ $activity->action }} 
                                <span class="font-medium">{{ $activity->entity_type }}</span>
                            </p>
                            <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm">No recent activity found.</p>
                    @endforelse
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.activity.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all activity →
                    </a>
                </div>
            </div>
            
            <!-- Recent Content -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-8">
                <h2 class="text-lg font-medium text-gray-700 mb-4">Recent News</h2>
                <div class="space-y-4 max-h-80 overflow-y-auto">
                    @forelse($dashboardData['content']['news']['recent'] as $news)
                    <div class="pb-3 border-b border-gray-100">
                        <a href="{{ route('admin.news.edit', $news->id) }}" class="text-gray-800 hover:text-blue-600 font-medium text-sm">
                            {{ $news->title }}
                        </a>
                        <p class="text-xs text-gray-500">{{ $news->published_at->format('M d, Y') }}</p>
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm">No recent news found.</p>
                    @endforelse
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.news.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all news →
                    </a>
                </div>
            </div>
            
            <!-- Recent Messages -->
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h2 class="text-lg font-medium text-gray-700 mb-4">Recent Messages</h2>
                <div class="space-y-4 max-h-80 overflow-y-auto">
                    @forelse($dashboardData['messages']['recent'] as $message)
                    <div class="pb-3 border-b border-gray-100">
                        <a href="{{ route('admin.messages.show', $message->id) }}" class="text-gray-800 hover:text-blue-600 font-medium text-sm">
                            {{ Str::limit($message->subject, 50) }}
                        </a>
                        <p class="text-xs text-gray-500">
                            From: {{ $message->name }} • {{ $message->created_at->diffForHumans() }}
                            @if($message->status == 'new')
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">New</span>
                            @endif
                        </p>
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm">No recent messages found.</p>
                    @endforelse
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.messages.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all messages →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart colors
        const colors = {
            blue: 'rgba(59, 130, 246, 0.7)',
            green: 'rgba(16, 185, 129, 0.7)',
            purple: 'rgba(139, 92, 246, 0.7)',
            yellow: 'rgba(245, 158, 11, 0.7)',
            blueLight: 'rgba(59, 130, 246, 0.3)',
            greenLight: 'rgba(16, 185, 129, 0.3)',
            purpleLight: 'rgba(139, 92, 246, 0.3)',
            yellowLight: 'rgba(245, 158, 11, 0.3)',
        };
        
        // Content Growth Chart
        const contentGrowthData = @json($dashboardData['charts']['content_growth']);
        const ctxContentGrowth = document.getElementById('contentGrowthChart').getContext('2d');
        new Chart(ctxContentGrowth, {
            type: 'line',
            data: {
                labels: contentGrowthData.labels,
                datasets: [
                    {
                        label: 'News',
                        data: contentGrowthData.datasets[0].data,
                        borderColor: colors.blue,
                        backgroundColor: colors.blueLight,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Providers',
                        data: contentGrowthData.datasets[1].data,
                        borderColor: colors.green,
                        backgroundColor: colors.greenLight,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'FAQs',
                        data: contentGrowthData.datasets[2].data,
                        borderColor: colors.purple,
                        backgroundColor: colors.purpleLight,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Resources',
                        data: contentGrowthData.datasets[3].data,
                        borderColor: colors.yellow,
                        backgroundColor: colors.yellowLight,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Provider Distribution Chart
        const providerData = @json($dashboardData['charts']['provider_distribution']);
        const ctxProviders = document.getElementById('providerDistributionChart').getContext('2d');
        new Chart(ctxProviders, {
            type: 'doughnut',
            data: {
                labels: Object.keys(providerData),
                datasets: [{
                    data: Object.values(providerData),
                    backgroundColor: [
                        colors.blue,
                        colors.green,
                        colors.purple,
                        colors.yellow,
                        'rgba(239, 68, 68, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
        
        // Download Statistics Chart
        const downloadData = @json($dashboardData['charts']['download_statistics']);
        const ctxDownloads = document.getElementById('downloadStatisticsChart').getContext('2d');
        new Chart(ctxDownloads, {
            type: 'bar',
            data: {
                labels: downloadData.labels,
                datasets: [{
                    label: 'Downloads',
                    data: downloadData.datasets[0].data,
                    backgroundColor: colors.yellow
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Activity Timeline Chart
        const activityData = @json($dashboardData['charts']['activity_timeline']);
        const ctxActivity = document.getElementById('activityTimelineChart').getContext('2d');
        new Chart(ctxActivity, {
            type: 'line',
            data: {
                labels: activityData.labels,
                datasets: [{
                    label: 'Activities',
                    data: activityData.datasets[0].data,
                    borderColor: 'rgba(99, 102, 241, 0.7)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection 