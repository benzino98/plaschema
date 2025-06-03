@extends('layouts.admin')

@section('title', 'Import Healthcare Providers')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Import Healthcare Providers</h1>
            <a href="{{ route('admin.providers.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Providers
            </a>
        </div>

        @if(session('warning'))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                <p>{{ session('warning') }}</p>
                @if(session('import_errors_count'))
                    <div class="mt-2">
                        <a href="{{ route('admin.providers.error-report') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded inline-flex items-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Error Report
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Import Instructions</h2>
                <div class="prose prose-sm text-gray-500 mb-6">
                    <p>Please follow these steps to import healthcare providers:</p>
                    <ol class="list-decimal pl-5 space-y-2">
                        <li>Download the template file using the button below.</li>
                        <li>Fill in the data according to the template format.</li>
                        <li>Save the file as Excel (.xlsx, .xls) or CSV format.</li>
                        <li>Upload the file using the form below.</li>
                        <li>Fix any errors if indicated after upload.</li>
                    </ol>
                    <div class="mt-4">
                        <p><strong>Important Notes:</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Required fields: name, type, description, address, city, phone, email</li>
                            <li>Duplicate entries (matching name and email) will be skipped</li>
                            <li>Images cannot be imported through this template</li>
                            <li>For services, provide a comma-separated list</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mb-8">
                    <a href="{{ route('admin.providers.template') }}" class="bg-[#74BA03] hover:bg-[#65a203] text-white px-4 py-2 rounded inline-flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Template
                    </a>
                </div>

                <h2 class="text-lg font-medium text-gray-900 mb-4">Upload File</h2>
                <form action="{{ route('admin.providers.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Select Excel or CSV File</label>
                        <input type="file" name="file" id="file" required 
                            class="mt-1 block w-full border border-gray-300 shadow-sm py-2 px-3 focus:outline-none focus:ring-[#74BA03] focus:border-[#74BA03] sm:text-sm rounded-md" 
                            accept=".xlsx,.xls,.csv">
                        @error('file')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Import Providers
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 