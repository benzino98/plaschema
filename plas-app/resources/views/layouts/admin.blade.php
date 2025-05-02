<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - PLASCHEMA Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#74BA03] text-white hidden md:block">
            <div class="p-4">
                <h1 class="text-xl font-bold">PLASCHEMA Admin</h1>
            </div>
            <nav class="mt-8">
                <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-white/20 {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : '' }}">
                    Dashboard
                </a>
                
                <div class="mt-4 px-4">
                    <h2 class="text-xs uppercase tracking-wider text-white/70">Content Management</h2>
                </div>
                <a href="{{ route('admin.news.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-white/20 {{ request()->routeIs('admin.news.*') ? 'bg-white/20' : '' }}">
                    News Management
                </a>
                <a href="{{ route('admin.providers.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-white/20 {{ request()->routeIs('admin.providers.*') ? 'bg-white/20' : '' }}">
                    Healthcare Providers
                </a>
                <a href="{{ route('admin.faqs.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-white/20 {{ request()->routeIs('admin.faqs.*') ? 'bg-white/20' : '' }}">
                    FAQ Management
                </a>
                
                @if(auth()->user()->hasRole('super-admin'))
                <a href="{{ route('admin.messages.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-white/20 {{ request()->routeIs('admin.messages.*') ? 'bg-white/20' : '' }}">
                    Contact Messages
                </a>
                @endif
                
                <div class="mt-4 px-4">
                    <h2 class="text-xs uppercase tracking-wider text-white/70">User Management</h2>
                </div>
                <a href="{{ route('admin.users.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-white/20 {{ request()->routeIs('admin.users.*') ? 'bg-white/20' : '' }}">
                    User Management
                </a>
                <a href="{{ route('admin.roles.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-white/20 {{ request()->routeIs('admin.roles.*') ? 'bg-white/20' : '' }}">
                    Role Management
                </a>
                
                <div class="mt-4 px-4">
                    <h2 class="text-xs uppercase tracking-wider text-white/70">System</h2>
                </div>
                <a href="{{ route('admin.activities.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-white/20 {{ request()->routeIs('admin.activities.*') ? 'bg-white/20' : '' }}">
                    Activity Logs
                </a>
                
                <div class="mt-8 px-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left py-2.5 px-4 rounded transition duration-200 hover:bg-white/20">
                            Logout
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Content -->
        <div class="flex-1">
            <!-- Top navbar -->
            <header class="bg-white shadow">
                <div class="flex justify-between items-center px-4 py-4 sm:px-6 lg:px-8">
                    <button class="md:hidden block text-gray-500" id="sidebarToggle">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div>
                        <span class="text-sm text-gray-500">Welcome, {{ Auth::user()->name }}</span>
                    </div>
                </div>
            </header>

            <!-- Main content -->
            <main>
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
    
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            const sidebar = document.querySelector('aside');
            sidebar.classList.toggle('hidden');
        });
    </script>
</body>
</html> 