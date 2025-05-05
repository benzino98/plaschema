@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.analytics.reports') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                New Report
            </a>
            <a href="{{ route('admin.analytics.generate-report') }}?report_type={{ $type }}&start_date={{ $data['period']['start_date'] }}&end_date={{ $data['period']['end_date'] }}&format=pdf" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                Download PDF
            </a>
            <a href="{{ route('admin.analytics.generate-report') }}?report_type={{ $type }}&start_date={{ $data['period']['start_date'] }}&end_date={{ $data['period']['end_date'] }}&format=excel" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                Download Excel
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Report Period: {{ $period }}</h2>
        
        @if($type == 'summary')
            @include('admin.analytics.partials.summary_report')
        @elseif($type == 'providers')
            @include('admin.analytics.partials.providers_report')
        @elseif($type == 'messages')
            @include('admin.analytics.partials.messages_report')
        @elseif($type == 'activity')
            @include('admin.analytics.partials.activity_report')
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts based on report type
        @if($type == 'summary')
            initializeSummaryCharts();
        @elseif($type == 'providers')
            initializeProviderCharts();
        @elseif($type == 'messages')
            initializeMessageCharts();
        @elseif($type == 'activity')
            initializeActivityCharts();
        @endif
    });

    function initializeSummaryCharts() {
        // Content Chart
        const contentCtx = document.getElementById('contentChart').getContext('2d');
        const contentData = @json($data['content']);
        
        new Chart(contentCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(contentData).map(key => key.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
                datasets: [{
                    label: 'Count',
                    data: Object.values(contentData),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(139, 92, 246, 0.7)',
                    ],
                    borderWidth: 1
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

        // Messages by Status Chart
        const messagesCtx = document.getElementById('messagesStatusChart').getContext('2d');
        const messagesData = @json($data['messages']['by_status']);
        
        new Chart(messagesCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(messagesData).map(key => key.charAt(0).toUpperCase() + key.slice(1)),
                datasets: [{
                    data: Object.values(messagesData),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });

        // Activity by Action Chart
        const activityCtx = document.getElementById('activityActionChart').getContext('2d');
        const activityData = @json($data['activity']['by_action']);
        
        new Chart(activityCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(activityData).map(key => key.charAt(0).toUpperCase() + key.slice(1)),
                datasets: [{
                    data: Object.values(activityData),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
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
    }

    function initializeProviderCharts() {
        // Providers by Category Chart
        const categoryCtx = document.getElementById('providerCategoryChart').getContext('2d');
        const categoryData = @json($data['by_category']);
        
        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(categoryData),
                datasets: [{
                    data: Object.values(categoryData),
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

        // Providers by Type Chart
        const typeCtx = document.getElementById('providerTypeChart').getContext('2d');
        const typeData = @json($data['by_type']);
        
        new Chart(typeCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(typeData),
                datasets: [{
                    label: 'Count',
                    data: Object.values(typeData),
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderWidth: 1
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

        // Providers by City Chart
        const cityCtx = document.getElementById('providerCityChart').getContext('2d');
        const cityData = @json($data['by_city']);
        
        new Chart(cityCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(cityData),
                datasets: [{
                    data: Object.values(cityData),
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
    }

    function initializeMessageCharts() {
        // Messages by Category Chart
        const categoryCtx = document.getElementById('messageCategoryChart').getContext('2d');
        const categoryData = @json($data['by_category']);
        
        new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(categoryData),
                datasets: [{
                    data: Object.values(categoryData),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
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

        // Messages by Status Chart
        const statusCtx = document.getElementById('messageStatusChart').getContext('2d');
        const statusData = @json($data['by_status']);
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData).map(key => key.charAt(0).toUpperCase() + key.slice(1)),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });

        // Daily Message Count Chart
        const dailyCtx = document.getElementById('messageDailyChart').getContext('2d');
        const dailyData = @json($data['daily_counts']);
        
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: Object.keys(dailyData),
                datasets: [{
                    label: 'Messages',
                    data: Object.values(dailyData),
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
    }

    function initializeActivityCharts() {
        // Activity by User Chart
        const userCtx = document.getElementById('activityUserChart').getContext('2d');
        const userData = @json($data['by_user']);
        
        new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(userData),
                datasets: [{
                    label: 'Activity Count',
                    data: Object.values(userData),
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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

        // Activity by Action Chart
        const actionCtx = document.getElementById('activityActionChart').getContext('2d');
        const actionData = @json($data['by_action']);
        
        new Chart(actionCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(actionData).map(key => key.charAt(0).toUpperCase() + key.slice(1)),
                datasets: [{
                    data: Object.values(actionData),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
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

        // Activity by Entity Type Chart
        const entityCtx = document.getElementById('activityEntityChart').getContext('2d');
        const entityData = @json($data['by_entity_type']);
        
        new Chart(entityCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(entityData).map(key => key.charAt(0).toUpperCase() + key.slice(1)),
                datasets: [{
                    data: Object.values(entityData),
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
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

        // Daily Activity Chart
        const dailyCtx = document.getElementById('activityDailyChart').getContext('2d');
        const dailyData = @json($data['daily_counts']);
        
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: Object.keys(dailyData),
                datasets: [{
                    label: 'Activity Count',
                    data: Object.values(dailyData),
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
    }
</script>
@endsection 