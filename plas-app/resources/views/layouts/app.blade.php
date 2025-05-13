<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PLASCHEMA') }}</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Custom Colors */
            :root {
                --plaschema-green: #74BA03;
                --plaschema-dark: #558A02;  /* Darker green for better contrast with white text */
                --primary: #74BA03; /* Updated to match plaschema green for consistency */
                --secondary: #2a628f;
                --accent: #f13c20;
                --light: #f8f9fa;
            }
            
            .bg-plaschema {
                background-color: var(--plaschema-green);
            }
            
            .bg-plaschema-dark {
                background-color: var(--plaschema-dark);
            }
            
            .text-plaschema {
                color: var(--plaschema-green);
            }
            
            .container-custom {
                width: 100%;
                padding-right: 1rem;
                padding-left: 1rem;
                margin-right: auto;
                margin-left: auto;
            }
            
            @media (min-width: 640px) {
                .container-custom {
                    max-width: 640px;
                }
            }
            
            @media (min-width: 768px) {
                .container-custom {
                    max-width: 768px;
                }
            }
            
            @media (min-width: 1024px) {
                .container-custom {
                    max-width: 1024px;
                }
            }
            
            @media (min-width: 1280px) {
                .container-custom {
                    max-width: 1280px;
                }
            }
            
            /* Hero section with better text contrast */
            .hero-section {
                background-color: var(--plaschema-dark);
                color: white;
                padding: 4rem 0;
            }
            
            .hero-section h1 {
                font-size: 2.5rem;
                font-weight: bold;
                margin-bottom: 1rem;
            }
            
            .hero-section p {
                font-size: 1.25rem;
                margin-bottom: 2rem;
            }
            
            /* Button styles */
            .btn-white {
                background-color: white;
                color: var(--plaschema-dark);
                padding: 0.75rem 1.5rem;
                border-radius: 0.375rem;
                font-weight: 500;
                display: inline-block;
                margin-right: 1rem;
                transition: all 0.2s;
            }
            
            .btn-white:hover {
                background-color: #f8f9fa;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            
            .btn-outline {
                background-color: transparent;
                color: white;
                border: 2px solid white;
                padding: 0.75rem 1.5rem;
                border-radius: 0.375rem;
                font-weight: 500;
                display: inline-block;
                transition: all 0.2s;
            }
            
            .btn-outline:hover {
                background-color: rgba(255, 255, 255, 0.1);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Navigation -->
            <nav class="bg-white shadow-sm navbar transition-shadow" id="main-navbar">
                <div class="container-custom mx-auto px-4">
                    <div class="flex justify-between h-20">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="flex items-center">
                                <img src="{{ asset('images/PLASCHEMA-LOGO.png') }}" alt="PLASCHEMA Logo" class="h-12 w-auto">
                                <span class="ml-3 font-bold text-2xl text-plaschema sm:block">PLASCHEMA</span>
                            </a>
                        </div>
                        
                        <!-- Primary Navigation - Right Aligned -->
                        <div class="hidden sm:flex sm:items-center">
                            <div class="flex space-x-8 md:space-x-12">
                                <a href="{{ route('home') }}" class="inline-flex items-center px-3 pt-1 border-b-2 {{ request()->routeIs('home') ? 'border-plaschema' : 'border-transparent' }} text-base font-medium leading-5 text-gray-900 hover:text-plaschema hover:border-plaschema transition hover-lift nav-hover-glow">Home</a>
                                <a href="{{ route('about') }}" class="inline-flex items-center px-3 pt-1 border-b-2 {{ request()->routeIs('about') ? 'border-plaschema' : 'border-transparent' }} text-base font-medium leading-5 text-gray-900 hover:text-plaschema hover:border-plaschema transition hover-lift nav-hover-glow">About</a>
                                <a href="{{ route('plans') }}" class="inline-flex items-center px-3 pt-1 border-b-2 {{ request()->routeIs('plans') ? 'border-plaschema' : 'border-transparent' }} text-base font-medium leading-5 text-gray-900 hover:text-plaschema hover:border-plaschema transition hover-lift nav-hover-glow">Health Plans</a>
                                <a href="{{ route('providers.index') }}" class="inline-flex items-center px-3 pt-1 border-b-2 {{ request()->routeIs('providers.*') ? 'border-plaschema' : 'border-transparent' }} text-base font-medium leading-5 text-gray-900 hover:text-plaschema hover:border-plaschema transition hover-lift nav-hover-glow">Providers</a>
                                <a href="{{ route('news') }}" class="inline-flex items-center px-3 pt-1 border-b-2 {{ request()->routeIs('news*') ? 'border-plaschema' : 'border-transparent' }} text-base font-medium leading-5 text-gray-900 hover:text-plaschema hover:border-plaschema transition hover-lift nav-hover-glow">News</a>
                                <a href="{{ route('resources.index') }}" class="inline-flex items-center px-3 pt-1 border-b-2 {{ request()->routeIs('resources.*') ? 'border-plaschema' : 'border-transparent' }} text-base font-medium leading-5 text-gray-900 hover:text-plaschema hover:border-plaschema transition hover-lift nav-hover-glow">Resources</a>
                                <a href="{{ route('faq') }}" class="inline-flex items-center px-3 pt-1 border-b-2 {{ request()->routeIs('faq') ? 'border-plaschema' : 'border-transparent' }} text-base font-medium leading-5 text-gray-900 hover:text-plaschema hover:border-plaschema transition hover-lift nav-hover-glow">FAQs</a>
                                <a href="{{ route('contact') }}" class="inline-flex items-center px-3 pt-1 border-b-2 {{ request()->routeIs('contact') ? 'border-plaschema' : 'border-transparent' }} text-base font-medium leading-5 text-gray-900 hover:text-plaschema hover:border-plaschema transition hover-lift nav-hover-glow">Contact</a>
                                <x-language-switcher />
                            </div>
                        </div>
                        
                        <!-- Mobile menu button -->
                        <div class="flex items-center sm:hidden">
                            <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out button-push">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile menu, show/hide based on menu state. -->
                <div class="mobile-menu hidden sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="{{ route('home') }}" class="block pl-3 pr-4 py-3 border-l-4 {{ request()->routeIs('home') ? 'border-plaschema bg-green-50 text-green-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">Home</a>
                        <a href="{{ route('about') }}" class="block pl-3 pr-4 py-3 border-l-4 {{ request()->routeIs('about') ? 'border-plaschema bg-green-50 text-green-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">About</a>
                        <a href="{{ route('plans') }}" class="block pl-3 pr-4 py-3 border-l-4 {{ request()->routeIs('plans') ? 'border-plaschema bg-green-50 text-green-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">Health Plans</a>
                        <a href="{{ route('providers.index') }}" class="block pl-3 pr-4 py-3 border-l-4 {{ request()->routeIs('providers.*') ? 'border-plaschema bg-green-50 text-green-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">Providers</a>
                        <a href="{{ route('news') }}" class="block pl-3 pr-4 py-3 border-l-4 {{ request()->routeIs('news*') ? 'border-plaschema bg-green-50 text-green-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">News</a>
                        <a href="{{ route('resources.index') }}" class="block pl-3 pr-4 py-3 border-l-4 {{ request()->routeIs('resources.*') ? 'border-plaschema bg-green-50 text-green-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">Resources</a>
                        <a href="{{ route('faq') }}" class="block pl-3 pr-4 py-3 border-l-4 {{ request()->routeIs('faq') ? 'border-plaschema bg-green-50 text-green-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">FAQs</a>
                        <a href="{{ route('contact') }}" class="block pl-3 pr-4 py-3 border-l-4 {{ request()->routeIs('contact') ? 'border-plaschema bg-green-50 text-green-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">Contact</a>
                        <div class="pl-3 pr-4 py-3">
                            <x-language-switcher style="inline" />
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>

            <!-- Footer -->
            <x-footer />
        </div>
        
        @stack('scripts')
        
        <script>
            // Mobile menu toggle
            document.addEventListener('DOMContentLoaded', function() {
                const mobileMenuButton = document.querySelector('.mobile-menu-button');
                const mobileMenu = document.querySelector('.mobile-menu');
                
                if (mobileMenuButton && mobileMenu) {
                    mobileMenuButton.addEventListener('click', function() {
                        mobileMenu.classList.toggle('hidden');
                    });
                }
                
                // Initialize scroll animations
                if (typeof setupScrollAnimations === 'function') {
                    setupScrollAnimations();
                    
                    // Additional component-specific animations
                    setupScrollAnimations('.hero-animate', 'fade-in', {
                        threshold: 0,
                        rootMargin: '0px'
                    });
                    
                    setupScrollAnimations('.card-animate', 'scale-in', {
                        threshold: 0.1,
                        rootMargin: '50px'
                    });
                }
                
                // Apply shadow to navbar on scroll
                if (typeof applyShadowOnScroll === 'function') {
                    applyShadowOnScroll('#main-navbar', 'shadow-md', 10);
                }
            });
        </script>
    </body>
</html>
