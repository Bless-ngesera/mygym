<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
          darkMode: localStorage.getItem('theme') === 'dark' ||
                   (localStorage.getItem('theme') === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)
      }"
      x-init="() => {
          // Initialize theme from localStorage or system preference
          const savedTheme = localStorage.getItem('theme') || 'system';

          if (savedTheme === 'system') {
              const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
              document.documentElement.classList.toggle('dark', systemDark);
              darkMode = systemDark;
          } else {
              document.documentElement.classList.toggle('dark', savedTheme === 'dark');
              darkMode = savedTheme === 'dark';
          }

          // Watch for system theme changes
          window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
              const currentTheme = localStorage.getItem('theme') || 'system';
              if (currentTheme === 'system') {
                  document.documentElement.classList.toggle('dark', e.matches);
                  darkMode = e.matches;
              }
          });

          // Watch for darkMode changes
          $watch('darkMode', (value) => {
              const currentTheme = localStorage.getItem('theme') || 'system';
              if (currentTheme === 'system') {
                  // Don't store darkMode separately when in system mode
                  return;
              }
              document.documentElement.classList.toggle('dark', value);
          });
      }"
      :class="{ 'dark': darkMode }">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MyGym') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine.js for theme switching -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            [x-cloak] { display: none !important; }

            /* Smooth theme transitions */
            * {
                transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
            }
        </style>
    </head>

    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow transition-colors duration-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
