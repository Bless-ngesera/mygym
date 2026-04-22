<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MyGym') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-black from-gray-50 via-purple-50 to-white text-gray-800"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;">

    <div class="flex flex-col items-center justify-center min-h-screen text-center px-6">
        <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-8 max-w-2xl">

            <!-- Logo replacing the MyGym text -->
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/Project_Logo.png') }}"
                     alt="MyGym Logo"
                     class="h-20 w-auto object-contain">
            </div>

            <!-- Tagline -->
            <div class="mb-8">
                <p class="text-lg text-gray-600 max-w-xl mx-auto">
                    Your personalized fitness management platform — stay organized, motivated, and connected.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="space-x-4 mt-8">
                @if (Route::has('login'))
                    @auth
                        @php
                            $dashboardRoute = match(auth()->user()->role) {
                                'admin' => route('admin.dashboard'),
                                'instructor' => route('instructor.dashboard'),
                                default => route('member.dashboard')
                            };
                        @endphp
                        <a href="{{ $dashboardRoute }}"
                        class="px-8 py-3 bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-800 hover:to-indigo-800 text-white rounded-xl shadow-lg font-semibold text-sm uppercase tracking-wider transition duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-purple-300 inline-block">
                        Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                        class="px-8 py-3 bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-800 hover:to-indigo-800 text-white rounded-xl shadow-lg font-semibold text-sm uppercase tracking-wider transition duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-purple-300 inline-block">
                        Log In
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                            class="px-8 py-3 border-2 border-purple-700 text-purple-700 hover:bg-purple-50 rounded-xl font-semibold text-sm uppercase tracking-wider transition duration-200 ease-in-out inline-block">
                            Sign Up
                            </a>
                        @endif
                    @endauth
                @endif
            </div>

            <!-- Footer with Links -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex justify-center gap-6 mb-3">
                    <a href="#" class="text-gray-500 hover:text-purple-600 text-sm transition-colors duration-200">About</a>
                    <a href="#" class="text-gray-500 hover:text-purple-600 text-sm transition-colors duration-200">Terms</a>
                    <a href="#" class="text-gray-500 hover:text-purple-600 text-sm transition-colors duration-200">Privacy</a>
                    <a href="#" class="text-gray-500 hover:text-purple-600 text-sm transition-colors duration-200">Contact</a>
                </div>
                <p class="text-gray-500 text-sm">
                    &copy; {{ date('Y') }} {{ config('app.name', 'MyGym') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>

</body>
</html>
