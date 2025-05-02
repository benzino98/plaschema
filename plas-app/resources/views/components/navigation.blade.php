<!-- Main Navigation -->
<nav class="bg-white shadow-sm">
    <div class="container mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="block h-12">
                        <img class="h-full w-auto" src="{{ asset('images/plaschema-logo.png') }}" alt="PLASCHEMA">
                    </a>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:ml-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        Home
                    </x-nav-link>
                    
                    <x-nav-link :href="route('about')" :active="request()->routeIs('about')">
                        About
                    </x-nav-link>
                    
                    <x-nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                        Contact
                    </x-nav-link>
                </div>
            </div>
            
            <!-- Buttons -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-button href="{{ route('login') }}" variant="outline" class="mr-2">
                    Login
                </x-button>
                <x-button href="{{ route('register') }}">
                    Get Started
                </x-button>
            </div>
            
            <!-- Mobile menu button -->
            <div class="flex items-center sm:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-plaschema hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-plaschema transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile menu -->
    <div :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen}" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                Home
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('about')" :active="request()->routeIs('about')">
                About
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                Contact
            </x-responsive-nav-link>
        </div>
        
        <div class="pt-4 pb-3 border-t border-gray-200">
            <div class="flex items-center px-4 space-x-3">
                <x-button href="{{ route('login') }}" variant="outline" class="w-full justify-center mb-2">
                    Login
                </x-button>
                <x-button href="{{ route('register') }}" class="w-full justify-center">
                    Get Started
                </x-button>
            </div>
        </div>
    </div>
</nav> 