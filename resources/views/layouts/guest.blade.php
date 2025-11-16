<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- small local styles for animation (keeps Tailwind usage primary) -->
        <style>
            @keyframes float {
                0% { transform: translateY(0); }
                50% { transform: translateY(-6px); }
                100% { transform: translateY(0); }
            }
            .logo-float { animation: float 4s ease-in-out infinite; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased"
        style="background-image: url('{{ asset('images/background2.jpg') }}'); 
        background-size: cover; 
        background-position: center; 
        background-attachment: fixed;"
    >
        <!-- full-screen gradient overlay to improve contrast -->
        <div class="fixed inset-0 pointer-events-none">
            <div class="absolute inset-0 bg-black/35"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 via-transparent to-pink-500/15 mix-blend-multiply"></div>
        </div>

        <div class="min-h-screen flex items-center justify-center px-4 py-12">
            <div class="w-full sm:max-w-md">
                <!-- card -->
                <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                    <div class="px-8 py-6">
                        <!-- header: logo + app name -->
                        <div class="flex items-center space-x-4 mb-4">
                            <a href="/" class="flex items-center space-x-3">
                                <x-application-logo class="w-16 h-16 text-gray-700 logo-float" />
                                <div>
                                    <h1 class="text-2xl font-semibold text-gray-800">{{ config('app.name', 'Laravel') }}</h1>
                                    <p class="text-sm text-gray-600 -mt-1">Welcome — please sign in</p>
                                </div>
                            </a>
                        </div>

                        <!-- slot (forms / content) -->
                        <div class="mt-2">
                            {{ $slot }}
                        </div>

                        <!-- divider -->
                        <div class="mt-6 border-t border-white/30"></div>

                        <!-- footer -->
                        <div class="mt-4 flex items-center justify-between text-xs text-gray-600">
                            <div>
                                <a href="#" class="hover:underline">Privacy</a>
                                <span class="mx-2">·</span>
                                <a href="#" class="hover:underline">Support</a>
                            </div>
                            <div>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
