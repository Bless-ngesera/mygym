<nav x-data="{
    open: false,
    theme: localStorage.getItem('theme') || 'system',
    systemDark: window.matchMedia('(prefers-color-scheme: dark)').matches,
    localeOpen: false,
    currentLocale: '{{ app()->getLocale() }}',
    supportedLocales: {
        en: 'English',
        es: 'Español',
        fr: 'Français',
        de: 'Deutsch',
        it: 'Italiano',
        pt: 'Português',
        sw: 'Kiswahili',
        ar: 'العربية'
    },
    flagIcons: {
        en: '🇬🇧',
        es: '🇪🇸',
        fr: '🇫🇷',
        de: '🇩🇪',
        it: '🇮🇹',
        pt: '🇵🇹',
        sw: '🇹🇿',
        ar: '🇸🇦'
    }
}" x-init="() => {
    // Watch for theme changes
    $watch('theme', value => {
        localStorage.setItem('theme', value);
        applyTheme(value);
    });

    // Watch for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        systemDark = e.matches;
        if (theme === 'system') {
            applyTheme('system');
        }
    });

    // Initial theme application
    applyTheme(theme);

    function applyTheme(value) {
        if (value === 'system') {
            const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', isDark);
        } else {
            document.documentElement.classList.toggle('dark', value === 'dark');
        }
    }
}" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 sticky top-0 z-50">

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="transition-opacity hover:opacity-80">
                        <img src="{{ asset('images/Project_Logo.png') }}"
                             alt="MyGym Logo"
                             class="h-9 w-auto">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        <!-- Role-Based Navigation Links -->
                        @if(auth()->user()->role === 'member')
                            <x-nav-link :href="route('member.dashboard')" :active="request()->routeIs('member.dashboard')">
                                Dashboard
                            </x-nav-link>

                            <x-nav-link :href="route('member.classes.index')" :active="request()->routeIs('member.classes.*')">
                                Available Classes
                            </x-nav-link>

                            <x-nav-link :href="route('member.bookings.index')" :active="request()->routeIs('member.bookings.*')">
                                My Bookings
                            </x-nav-link>

                            <x-nav-link :href="route('member.receipts.index')" :active="request()->routeIs('member.receipts.*')">
                                My Receipts
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->role === 'instructor')
                            <x-nav-link :href="route('instructor.dashboard')" :active="request()->routeIs('instructor.dashboard')">
                                Dashboard
                            </x-nav-link>

                            <x-nav-link :href="route('instructor.create')" :active="request()->routeIs('instructor.create')">
                                Schedule a Class
                            </x-nav-link>

                            <x-nav-link :href="route('instructor.upcoming')" :active="request()->routeIs('instructor.upcoming')">
                                Upcoming Classes
                            </x-nav-link>

                            <x-nav-link :href="route('instructor.calendar')" :active="request()->routeIs('instructor.calendar')">
                                Calendar
                            </x-nav-link>

                            <x-nav-link :href="route('instructor.earnings.index')" :active="request()->routeIs('instructor.earnings.*')">
                                My Earnings
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->role === 'admin')
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                Dashboard
                            </x-nav-link>

                            <x-nav-link :href="route('admin.earnings.index')" :active="request()->routeIs('admin.earnings.*')">
                                Earnings
                            </x-nav-link>

                            <x-nav-link :href="route('admin.instructors.index')" :active="request()->routeIs('admin.instructors.*')">
                                Instructors
                            </x-nav-link>

                            <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                                Reports
                            </x-nav-link>

                            <x-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">
                                Settings
                            </x-nav-link>
                        @endif
                    @else
                        <!-- Guest Navigation Links -->
                        <x-nav-link :href="route('login')" :active="request()->routeIs('login')">
                            Login
                        </x-nav-link>
                        <x-nav-link :href="route('register')" :active="request()->routeIs('register')">
                            Register
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Right Side: Language Switcher, Theme Switcher & User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <!-- Language Switcher Dropdown -->
                <div x-data="{ localeOpen: false }" class="relative">
                    <button @click="localeOpen = !localeOpen"
                            class="relative p-2 text-gray-600 hover:text-purple-600 hover:bg-purple-50 dark:text-gray-400 dark:hover:text-purple-400 dark:hover:bg-purple-900/30 rounded-xl transition-all duration-200"
                            aria-label="Toggle language">
                        <span class="text-xl" x-text="flagIcons[currentLocale] || '🌐'"></span>
                    </button>

                    <!-- Language Dropdown Menu -->
                    <div x-show="localeOpen"
                         @click.away="localeOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden"
                         style="display: none;">
                        <div class="py-2">
                            <template x-for="(name, code) in supportedLocales" :key="code">
                                <a :href="'{{ url('locale') }}/' + code"
                                   @click="localeOpen = false; currentLocale = code"
                                   class="w-full px-4 py-2.5 text-left text-sm hover:bg-purple-50 dark:hover:bg-purple-900/30 transition flex items-center gap-3">
                                    <span class="text-lg" x-text="flagIcons[code]"></span>
                                    <span class="text-gray-700 dark:text-gray-300" x-text="name"></span>
                                    <span x-show="currentLocale === code" class="ml-auto text-purple-600">✓</span>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Theme Switcher Dropdown -->
                <div x-data="{ themeOpen: false }" class="relative">
                    <button @click="themeOpen = !themeOpen"
                            class="relative p-2 text-gray-600 hover:text-purple-600 hover:bg-purple-50 dark:text-gray-400 dark:hover:text-purple-400 dark:hover:bg-purple-900/30 rounded-xl transition-all duration-200"
                            aria-label="Toggle theme">
                        <!-- Sun Icon (Light) -->
                        <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <!-- Moon Icon (Dark) -->
                        <svg x-show="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <!-- Computer Icon (System) -->
                        <svg x-show="theme === 'system'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </button>

                    <!-- Theme Dropdown Menu -->
                    <div x-show="themeOpen"
                         @click.away="themeOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden"
                         style="display: none;">
                        <div class="py-2">
                            <button @click="theme = 'light'; themeOpen = false"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-purple-50 dark:hover:bg-purple-900/30 transition flex items-center gap-3">
                                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Light Mode</span>
                                <span x-show="theme === 'light'" class="ml-auto text-purple-600">✓</span>
                            </button>

                            <button @click="theme = 'dark'; themeOpen = false"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-purple-50 dark:hover:bg-purple-900/30 transition flex items-center gap-3">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Dark Mode</span>
                                <span x-show="theme === 'dark'" class="ml-auto text-purple-600">✓</span>
                            </button>

                            <button @click="theme = 'system'; themeOpen = false"
                                    class="w-full px-4 py-2.5 text-left text-sm hover:bg-purple-50 dark:hover:bg-purple-900/30 transition flex items-center gap-3">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">System Mode</span>
                                <span x-show="theme === 'system'" class="ml-auto text-purple-600">✓</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notification Bell (Members Only) -->
                @if(auth()->check() && auth()->user()->role === 'member')
                    @php
                        $unreadCount = App\Models\Notification::where('user_id', auth()->id())->where('read', false)->count();
                        $recentNotifications = App\Models\Notification::where('user_id', auth()->id())->latest()->limit(5)->get();
                    @endphp
                    <x-notification-bell :count="$unreadCount" :notifications="$recentNotifications" />
                @endif

                <!-- User Dropdown (Authenticated Only) -->
                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2.5 bg-gradient-to-r from-indigo-50 to-indigo-50/50 hover:from-indigo-100 hover:to-indigo-100 dark:from-gray-700 dark:to-gray-700/50 dark:hover:from-gray-600 dark:hover:to-gray-600 px-3 py-1.5 rounded-xl border border-indigo-100/60 dark:border-gray-600 transition-all duration-200">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4F46E5&color=fff&bold=true&size=64"
                                 alt="Avatar" class="w-8 h-8 rounded-lg ring-2 ring-indigo-200 dark:ring-indigo-600">
                            <div class="hidden sm:block text-left">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 leading-none">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ ucfirst(Auth::user()->role) }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- User Info Header -->
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        </div>

                        <!-- Role-based quick links -->
                        @if(auth()->user()->role === 'member')
                            <x-dropdown-link :href="route('member.dashboard')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Dashboard
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('member.bookings.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                My Bookings
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('member.receipts.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                My Receipts
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('member.classes.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Book a Class
                            </x-dropdown-link>
                        @endif

                        @if(auth()->user()->role === 'admin')
                            <x-dropdown-link :href="route('admin.dashboard')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 17h16"/>
                                </svg>
                                Admin Panel
                            </x-dropdown-link>
                        @endif

                        @if(auth()->user()->role === 'instructor')
                            <x-dropdown-link :href="route('instructor.dashboard')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Instructor Panel
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('instructor.create')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Schedule Class
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('instructor.upcoming')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Upcoming Classes
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('instructor.calendar')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Calendar View
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('instructor.earnings.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                My Earnings
                            </x-dropdown-link>
                        @endif

                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profile
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="flex items-center gap-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @endauth
            </div>

            <!-- Guest Links (Mobile/Tablet) -->
            @guest
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-2">
                <a href="{{ route('login') }}"
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                    Log in
                </a>
                <a href="{{ route('register') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-all duration-200">
                    Sign up
                </a>
            </div>
            @endguest

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors">
                    <svg class="h-6 w-6 text-gray-600 dark:text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border-t border-gray-100 dark:border-gray-700">
        <div class="pt-2 pb-3 space-y-1 px-4">
            @auth
                <!-- Mobile Theme Switcher -->
                <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-700 mb-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Theme</p>
                    <div class="flex gap-2">
                        <button @click="theme = 'light'"
                                class="flex-1 px-3 py-2 rounded-lg text-sm font-medium transition-all"
                                :class="theme === 'light' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'">
                            Light
                        </button>
                        <button @click="theme = 'dark'"
                                class="flex-1 px-3 py-2 rounded-lg text-sm font-medium transition-all"
                                :class="theme === 'dark' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'">
                            Dark
                        </button>
                        <button @click="theme = 'system'"
                                class="flex-1 px-3 py-2 rounded-lg text-sm font-medium transition-all"
                                :class="theme === 'system' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'">
                            System
                        </button>
                    </div>
                </div>

                <!-- Mobile Language Switcher -->
                <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-700 mb-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Language</p>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="(name, code) in supportedLocales" :key="code">
                            <a :href="'{{ url('locale') }}/' + code"
                               @click="open = false; currentLocale = code"
                               class="px-3 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2"
                               :class="currentLocale === code ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'">
                                <span class="text-lg" x-text="flagIcons[code]"></span>
                                <span x-text="name"></span>
                            </a>
                        </template>
                    </div>
                </div>

                <!-- Mobile Role-Based Links -->
                @if(auth()->user()->role === 'member')
                    <x-responsive-nav-link :href="route('member.dashboard')" :active="request()->routeIs('member.dashboard')">
                        Dashboard
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('member.bookings.index')" :active="request()->routeIs('member.bookings.*')">
                        My Bookings
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('member.receipts.index')" :active="request()->routeIs('member.receipts.*')">
                        My Receipts
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('member.classes.index')" :active="request()->routeIs('member.classes.*')">
                        Available Classes
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->role === 'instructor')
                    <x-responsive-nav-link :href="route('instructor.dashboard')" :active="request()->routeIs('instructor.dashboard')">
                        Dashboard
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('instructor.upcoming')" :active="request()->routeIs('instructor.upcoming')">
                        Upcoming Classes
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('instructor.create')" :active="request()->routeIs('instructor.create')">
                        Schedule a Class
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('instructor.calendar')" :active="request()->routeIs('instructor.calendar')">
                        Calendar
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('instructor.earnings.index')" :active="request()->routeIs('instructor.earnings.*')">
                        My Earnings
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        Dashboard
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('admin.earnings.index')" :active="request()->routeIs('admin.earnings.*')">
                        Earnings
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('admin.instructors.index')" :active="request()->routeIs('admin.instructors.*')">
                        Instructors
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                        Reports
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">
                        Settings
                    </x-responsive-nav-link>
                @endif

                <!-- Divider -->
                <div class="border-t border-gray-100 dark:border-gray-700 my-3"></div>

                <!-- User Info -->
                <div class="px-3 py-2">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4F46E5&color=fff&bold=true&size=64"
                             alt="Avatar" class="w-10 h-10 rounded-lg ring-2 ring-indigo-200 dark:ring-indigo-600">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>

                <x-responsive-nav-link :href="route('profile.edit')">
                    Profile
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            @else
                <!-- Guest Mobile Links -->
                <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">
                    Login
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">
                    Register
                </x-responsive-nav-link>
            @endauth
        </div>
    </div>
</nav>
