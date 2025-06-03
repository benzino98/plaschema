<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PLASCHEMA') }} - Admin Login</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            /* Custom animations for the admin login page */
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .animate-fadeIn {
                animation: fadeIn 0.6s ease-out forwards;
            }
            
            .animate-delay-100 {
                animation-delay: 0.1s;
            }
            
            .animate-delay-200 {
                animation-delay: 0.2s;
            }
            
            .animate-delay-300 {
                animation-delay: 0.3s;
            }

            /* Form field focus animation */
            .form-input-focus-effect {
                transition: all 0.3s ease;
                border: 1px solid #e5e7eb;
            }
            
            .form-input-focus-effect:focus {
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
            }
            
            /* Password toggle animation */
            .password-toggle {
                transition: all 0.2s ease;
            }
            
            .password-toggle:hover {
                color: #3b82f6;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200">
            <div class="animate-fadeIn">
                <a href="/">
                    <img src="{{ asset('images/PLASCHEMA-LOGO.png') }}" alt="PLASCHEMA Logo" class="w-32 h-auto">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-6 bg-white shadow-xl rounded-lg overflow-hidden animate-fadeIn animate-delay-100">
                {{ $slot }}
            </div>
            
            <div class="mt-6 text-center text-sm text-gray-600 animate-fadeIn animate-delay-300">
                &copy; {{ date('Y') }} {{ config('app.name', 'PLASCHEMA') }}. All Rights Reserved.
            </div>
        </div>
        
        @stack('scripts')
    </body>
</html> 