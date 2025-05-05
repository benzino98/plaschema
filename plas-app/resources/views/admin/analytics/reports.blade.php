@extends('layouts.admin')

@section('title', 'Generate Reports')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Generate Reports</h1>
        <a href="{{ route('admin.analytics.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Back to Dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.analytics.generate-report') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Report Type -->
                <div>
                    <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                    <select id="report_type" name="report_type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="summary">Summary Report</option>
                        <option value="providers">Healthcare Providers Report</option>
                        <option value="messages">Contact Messages Report</option>
                        <option value="activity">Activity Report</option>
                    </select>
                    @error('report_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Report Format -->
                <div>
                    <label for="format" class="block text-sm font-medium text-gray-700 mb-1">Report Format</label>
                    <select id="format" name="format" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="html">Web (HTML)</option>
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                    </select>
                    @error('format')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Date Range -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ date('Y-m-d', strtotime('-30 days')) }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('start_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('end_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- User Filter (for activity reports only) -->
            <div id="user_filter_container" class="mb-6 hidden">
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Filter by User (Optional)</label>
                <select id="user_id" name="user_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Users</option>
                    @foreach (\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    <!-- Report Preview -->
    <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Report Types</h2>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-2">Summary Report</h3>
                <p class="text-gray-600">A comprehensive overview of all system activity, including content creation, message statistics, and user activities.</p>
            </div>
            
            <div class="p-6 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-2">Healthcare Providers Report</h3>
                <p class="text-gray-600">Detailed analysis of healthcare providers categorized by type, location, and other relevant metrics.</p>
            </div>
            
            <div class="p-6 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-2">Contact Messages Report</h3>
                <p class="text-gray-600">Insights into contact message trends, including volume by category, status distribution, and response times.</p>
            </div>
            
            <div class="p-6">
                <h3 class="font-semibold text-gray-800 mb-2">Activity Report</h3>
                <p class="text-gray-600">Comprehensive audit of system activity by user, action type, and affected content, with daily trend analysis.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportTypeSelect = document.getElementById('report_type');
        const userFilterContainer = document.getElementById('user_filter_container');
        
        // Show user filter only for activity reports
        reportTypeSelect.addEventListener('change', function() {
            if (this.value === 'activity') {
                userFilterContainer.classList.remove('hidden');
            } else {
                userFilterContainer.classList.add('hidden');
            }
        });
    });
</script>
@endsection 