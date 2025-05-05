@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Analytics Dashboard</h1>
        <a href="{{ route('admin.analytics.reports') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Generate Reports
        </a>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Healthcare Providers</p>
                    <p class="text-2xl font-bold text-gray-700">{{ $data['totalProviders'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">News Articles</p>
                    <p class="text-2xl font-bold text-gray-700">{{ $data['totalNews'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">FAQs</p>
                    <p class="text-2xl font-bold text-gray-700">{{ $data['totalFaqs'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Contact Messages</p>
                    <p class="text-2xl font-bold text-gray-700">{{ $data['totalMessages'] }}</p>
                    <p class="text-sm text-yellow-500">{{ $data['newMessages'] }} new</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Activity Timeline Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Activity Timeline (Last 30 Days)</h2>
            <div class="h-80">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <!-- Content Growth Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Content Growth (12 Months)</h2>
            <div class="h-80">
                <canvas id="contentGrowthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detail Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Providers by Category -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Providers by Category</h2>
            <div class="h-80">
                <canvas id="providersByCategoryChart"></canvas>
            </div>
        </div>

        <!-- Messages by Category -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Messages by Category</h2>
            <div class="h-80">
                <canvas id="messagesByCategoryChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activity Timeline Chart
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const activityData = @json($data['activityTimeline']);
        
        const activityLabels = Object.keys(activityData);
        const activityCounts = Object.values(activityData);
        
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: activityLabels,
                datasets: [{
                    label: 'Activity Count',
                    data: activityCounts,
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    tension: 0.4,
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

        // Content Growth Chart
        const contentCtx = document.getElementById('contentGrowthChart').getContext('2d');
        const contentData = @json($data['contentGrowth']);
        
        const contentLabels = Object.keys(contentData);
        const newsData = contentLabels.map(month => contentData[month].news);
        const providerData = contentLabels.map(month => contentData[month].providers);
        const faqData = contentLabels.map(month => contentData[month].faqs);
        
        new Chart(contentCtx, {
            type: 'bar',
            data: {
                labels: contentLabels,
                datasets: [
                    {
                        label: 'News',
                        data: newsData,
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    },
                    {
                        label: 'Providers',
                        data: providerData,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    },
                    {
                        label: 'FAQs',
                        data: faqData,
                        backgroundColor: 'rgba(139, 92, 246, 0.7)',
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

        // Providers by Category Chart
        const providersCtx = document.getElementById('providersByCategoryChart').getContext('2d');
        const providersData = @json($data['providersByCategory']);
        
        const providerCategories = Object.keys(providersData);
        const providerCounts = Object.values(providersData);
        
        new Chart(providersCtx, {
            type: 'pie',
            data: {
                labels: providerCategories,
                datasets: [{
                    data: providerCounts,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(139, 92, 246, 0.7)',
                        'rgba(6, 182, 212, 0.7)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });

        // Messages by Category Chart
        const messagesCtx = document.getElementById('messagesByCategoryChart').getContext('2d');
        const messagesData = @json($data['messagesByCategory']);
        
        const messageCategories = Object.keys(messagesData);
        const messageCounts = Object.values(messagesData);
        
        new Chart(messagesCtx, {
            type: 'doughnut',
            data: {
                labels: messageCategories,
                datasets: [{
                    data: messageCounts,
                    backgroundColor: [
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(139, 92, 246, 0.7)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    });
</script>
@endsection 