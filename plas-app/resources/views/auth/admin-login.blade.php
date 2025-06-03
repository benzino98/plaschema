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

        /* Button styles */
        .admin-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
            font-weight: 600;
            color: white;
            background-color: #3b82f6;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .admin-button:hover {
            background-color: #2563eb;
            transform: scale(1.02);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .admin-button:active {
            background-color: #1d4ed8;
            transform: scale(0.98);
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-green-50 via-green-100 to-green-200">
        <div class="animate-fadeIn">
            <a href="/">
                <img src="{{ asset('images/PLASCHEMA-LOGO.png') }}" alt="PLASCHEMA Logo" class="w-24 h-auto">
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-8 py-6 bg-white shadow-xl rounded-lg overflow-hidden animate-fadeIn animate-delay-100">
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-6 animate-fadeIn">Admin Login</h1>
            
            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 animate-fadeIn">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-4 animate-fadeIn">
                    <div class="font-medium text-red-600">
                        {{ __('Oops! Please check your credentials.') }}
                    </div>

                    <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6 animate-fadeIn animate-delay-100">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Enter your email" 
                            class="form-input-focus-effect rounded-md shadow-sm border-gray-300 focus:border-green-500 focus:ring-green-500 block w-full pl-10 transition duration-300 ease-in-out">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password') }}</label>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Enter your password"
                            class="form-input-focus-effect rounded-md shadow-sm block w-full pr-10 border-gray-300 focus:border-green-500 focus:ring-green-500"
                        />
                        <button
                            type="button"
                            class="password-toggle absolute inset-y-0 right-0 flex items-center pr-3 text-gray-600 cursor-pointer"
                            onclick="togglePasswordVisibility('password')"
                            tabindex="-1"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 password-eye-icon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500 focus:border-green-500" name="remember">
                        <span class="ms-2 text-sm text-gray-700">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="mt-6">
                    <button type="submit" class="admin-button w-full justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>
        </div>
        
        <div class="mt-6 text-center text-sm text-gray-600 animate-fadeIn animate-delay-300">
            &copy; {{ date('Y') }} {{ config('app.name', 'PLASCHEMA') }}. All Rights Reserved.
        </div>
    </div>
    
    <script>
        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = passwordInput.parentElement.querySelector('.password-eye-icon');
            
            // Toggle password visibility
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Change to eye-slash icon
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                `;
            } else {
                passwordInput.type = 'password';
                // Change back to eye icon
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                `;
            }

            // Add a little animation effect
            eyeIcon.classList.add('scale-110');
            setTimeout(() => {
                eyeIcon.classList.remove('scale-110');
            }, 200);
        }
    </script>
</body>
</html> 