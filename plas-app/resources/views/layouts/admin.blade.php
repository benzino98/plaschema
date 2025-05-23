@php
    use Illuminate\Support\Facades\Auth;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - PLASCHEMA Admin</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <a href="#main-content" class="skip-to-content">Skip to main content</a>
    <div class="min-h-screen bg-gray-100">
        <div class="flex">
            <!-- Sidebar -->
            <div id="sidebar" class="bg-gray-900 text-white w-64 flex-shrink-0 fixed h-screen overflow-y-auto">
                <div class="p-4 border-b border-gray-800">
                    <div class="flex items-center space-x-2">
                        <img src="{{ asset('images/PLASCHEMA-LOGO.png') }}" alt="PLASCHEMA Logo" class="h-8 w-auto">
                        <span class="text-xl font-bold">Admin</span>
                    </div>
                </div>
                <nav class="mt-4">
                    <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">Dashboard</div>
                    <ul>
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 flex items-center space-x-3 text-gray-300 hover:bg-gray-800 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="px-4 py-2 mt-4 text-xs text-gray-400 uppercase tracking-wider">Content</div>
                    <ul>
                        <li>
                            <a href="{{ route('admin.news.index') }}" class="px-4 py-2 flex items-center space-x-3 text-gray-300 hover:bg-gray-800 rounded {{ request()->routeIs('admin.news.*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                                <span>News</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.providers.index') }}" class="px-4 py-2 flex items-center space-x-3 text-gray-300 hover:bg-gray-800 rounded {{ request()->routeIs('admin.providers.*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                </svg>
                                <span>Healthcare Providers</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.faqs.index') }}" class="px-4 py-2 flex items-center space-x-3 text-gray-300 hover:bg-gray-800 rounded {{ request()->routeIs('admin.faqs.*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>FAQs</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.messages.index') }}" class="px-4 py-2 flex items-center space-x-3 text-gray-300 hover:bg-gray-800 rounded {{ request()->routeIs('admin.messages.*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>Messages</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.resources.index') }}" class="px-4 py-2 flex items-center space-x-3 text-gray-300 hover:bg-gray-800 rounded {{ request()->routeIs('admin.resources.*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span>Resources</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.resource-categories.index') }}" class="px-4 py-2 flex items-center space-x-3 text-gray-300 hover:bg-gray-800 rounded {{ request()->routeIs('admin.resource-categories.*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                <span>Resource Categories</span>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="px-4 py-2 mt-4 text-xs text-gray-400 uppercase tracking-wider">Administration</div>
                    <ul>
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 flex items-center space-x-3 text-gray-300 hover:bg-gray-800 rounded {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>Users</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 flex items-center space-x-3 text-gray-300 hover:bg-gray-800 rounded {{ request()->routeIs('admin.roles.*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Roles & Permissions</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.activity.index') }}" class="px-4 py-2 flex items-center space-x-3 text-gray-300 hover:bg-gray-800 rounded {{ request()->routeIs('admin.activity.*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Activity Log</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.translations.index') }}" class="px-4 py-2 flex items-center space-x-3 text-gray-300 hover:bg-gray-800 rounded {{ request()->routeIs('admin.translations.*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                </svg>
                                <span>Translations</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.analytics') }}" class="px-4 py-2 flex items-center space-x-3 text-gray-300 hover:bg-gray-800 rounded {{ request()->routeIs('admin.analytics*') ? 'bg-gray-800 text-white' : '' }}">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span>Analytics</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="ml-64 flex-1">
                <!-- Top Navbar -->
                <div class="bg-white shadow-sm border-b">
                    <div class="flex justify-between items-center px-6 py-3">
                        <div>
                            <button id="sidebar-toggle" class="text-gray-500 hover:text-gray-600 focus:outline-none">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:text-gray-900">
                                {{ Auth::user()->name }}
                            </a>
                            
                            <!-- Logout Form -->
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Page Content -->
                <main id="main-content" class="p-6">
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 mx-4 mt-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 mx-4 mt-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif
                    
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="{{ asset('js/alpine.js') }}"></script>
    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const mainContent = document.querySelector('.ml-64');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                mainContent.classList.remove('ml-0');
                mainContent.classList.add('ml-64');
            } else {
                sidebar.classList.add('hidden');
                mainContent.classList.remove('ml-64');
                mainContent.classList.add('ml-0');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html> 