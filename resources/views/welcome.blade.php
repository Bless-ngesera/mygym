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
        <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
            <!-- Logo / Title -->
            <div class=" mb-8">
                <h1 class="text-6xl sm:text-7xl md:text-8xl font-extrabold tracking-tight text-purple-700 drop-shadow-sm">
                    My<span class="text-gray-900">Gym</span>
                </h1>
                <p class="mt-3 text-lg text-gray-600 max-w-xl mx-auto">
                    Your personalized fitness management platform — stay organized, motivated, and connected.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="space-x-4 mt-8">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}"
                        class="px-6 py-3 bg-purple-700 hover:bg-purple-800 text-white rounded-xl shadow-lg font-semibold text-sm uppercase tracking-wider transition duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-purple-300">
                        Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                        class="px-6 py-3 bg-purple-700 hover:bg-purple-800 text-white rounded-xl shadow-lg font-semibold text-sm uppercase tracking-wider transition duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-purple-300">
                        Log In
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                            class="px-6 py-3 border border-purple-700 text-purple-700 hover:bg-purple-50 rounded-xl font-semibold text-sm uppercase tracking-wider transition duration-200 ease-in-out">
                            Sign Up
                            </a>
                        @endif
                    @endauth
                @endif
            </div>

            <!-- Footer -->
            <footer class="mt-16 text-gray-500 text-sm pb-6">
                &copy; {{ date('Y') }} {{ config('app.name', 'MyGym') }}. All rights reserved.
            </footer>
        </div>
       
    </div>

</body>
</html>
