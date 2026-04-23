<nav x-data="navigationComponent()"
     x-init="init()"
     class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl border-b border-gray-100/50 dark:border-gray-800/50 sticky top-0 z-50 shadow-sm transition-all duration-300"
     :class="{ 'shadow-lg bg-white/95 dark:bg-gray-900/95': scrolled }"
     x-cloak>

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="transition-all duration-300 hover:opacity-80 hover:scale-105">
                        <img src="{{ asset('images/Project_Logo.png') }}"
                             alt="MyGym Logo"
                             class="h-8 sm:h-9 w-auto">
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex space-x-6 lg:space-x-8 sm:-my-px sm:ms-8 lg:ms-10">
                    @auth
                        @if(auth()->user()->role === 'member')
                            <x-nav-link :href="route('member.dashboard')" :active="request()->routeIs('member.dashboard')">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('member.classes.index')" :active="request()->routeIs('member.classes.*')">
                                Classes
                            </x-nav-link>
                            <x-nav-link :href="route('member.bookings.index')" :active="request()->routeIs('member.bookings.*')">
                                Bookings
                            </x-nav-link>
                            <x-nav-link :href="route('member.receipts.index')" :active="request()->routeIs('member.receipts.*')">
                                Receipts
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->role === 'instructor')
                            <x-nav-link :href="route('instructor.dashboard')" :active="request()->routeIs('instructor.dashboard')">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('instructor.create')" :active="request()->routeIs('instructor.create')">
                                Schedule
                            </x-nav-link>
                            <x-nav-link :href="route('instructor.upcoming')" :active="request()->routeIs('instructor.upcoming')">
                                Classes
                            </x-nav-link>
                            <x-nav-link :href="route('instructor.calendar')" :active="request()->routeIs('instructor.calendar')">
                                Calendar
                            </x-nav-link>
                            <x-nav-link :href="route('instructor.earnings.index')" :active="request()->routeIs('instructor.earnings.*')">
                                Earnings
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->role === 'admin')
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('admin.instructors.index')" :active="request()->routeIs('admin.instructors.*')">
                                Instructors
                            </x-nav-link>
                            <x-nav-link :href="route('admin.members.index')" :active="request()->routeIs('admin.members.*')">
                                Members
                            </x-nav-link>
                            <x-nav-link :href="route('admin.earnings.index')" :active="request()->routeIs('admin.earnings.*')">
                                Earnings
                            </x-nav-link>
                            <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                                Reports
                            </x-nav-link>
                        @endif
                    @else
                        <x-nav-link :href="route('login')" :active="request()->routeIs('login')">
                            Login
                        </x-nav-link>
                        <x-nav-link :href="route('register')" :active="request()->routeIs('register')">
                            Register
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Right Side: AI Assistant, Theme Switcher, Notifications, User Dropdown -->
            <div class="flex items-center gap-1 sm:gap-2 md:gap-3">
                <!-- AI Assistant Button -->
                @auth
                <button @click="$dispatch('open-chat')"
                        class="relative group bg-gradient-to-r from-purple-600 to-blue-600 hover:shadow-lg hover:shadow-purple-500/25 transition-all duration-300 transform hover:scale-105 rounded-xl px-2 sm:px-3 py-2 flex items-center gap-1 sm:gap-2"
                        aria-label="Open AI Assistant">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <span class="text-white font-medium text-xs sm:text-sm hidden lg:inline-block">AI Assistant</span>
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 sm:w-3 sm:h-3 bg-green-500 rounded-full animate-pulse ring-2 ring-white dark:ring-gray-800"></span>
                </button>
                @endauth

                <!-- Theme Switcher -->
                <div x-data="{ themeOpen: false }" class="relative">
                    <button @click="themeOpen = !themeOpen"
                            class="p-2 text-gray-600 hover:text-purple-600 hover:bg-purple-50 dark:text-gray-400 dark:hover:text-purple-400 dark:hover:bg-purple-900/30 rounded-xl transition-all duration-200"
                            aria-label="Toggle theme">
                        <svg x-show="theme === 'light'" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg x-show="theme === 'dark'" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <svg x-show="theme === 'system'" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </button>

                    <div x-show="themeOpen"
                         @click.away="themeOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute right-0 mt-2 w-40 sm:w-48 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden"
                         style="display: none;">
                        <div class="py-2">
                            <button @click="setTheme('light'); themeOpen = false"
                                    class="w-full px-3 sm:px-4 py-2 text-left text-xs sm:text-sm hover:bg-purple-50 dark:hover:bg-purple-900/30 transition flex items-center gap-3">
                                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Light</span>
                                <span x-show="theme === 'light'" class="ml-auto text-purple-600 text-xs">✓</span>
                            </button>
                            <button @click="setTheme('dark'); themeOpen = false"
                                    class="w-full px-3 sm:px-4 py-2 text-left text-xs sm:text-sm hover:bg-purple-50 dark:hover:bg-purple-900/30 transition flex items-center gap-3">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Dark</span>
                                <span x-show="theme === 'dark'" class="ml-auto text-purple-600 text-xs">✓</span>
                            </button>
                            <button @click="setTheme('system'); themeOpen = false"
                                    class="w-full px-3 sm:px-4 py-2 text-left text-xs sm:text-sm hover:bg-purple-50 dark:hover:bg-purple-900/30 transition flex items-center gap-3">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">System</span>
                                <span x-show="theme === 'system'" class="ml-auto text-purple-600 text-xs">✓</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notification Bell -->
                @auth
                <div class="relative">
                    <button @click="toggleNotifications()"
                            class="relative p-2 text-gray-600 hover:text-purple-600 hover:bg-purple-50 dark:text-gray-400 dark:hover:text-purple-400 dark:hover:bg-purple-900/30 rounded-xl transition-all duration-200"
                            aria-label="Notifications">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span x-show="unreadCount > 0"
                              x-text="unreadCount > 99 ? '99+' : unreadCount"
                              class="absolute -top-1 -right-1 min-w-[18px] h-4 sm:min-w-[20px] sm:h-5 bg-red-500 text-white text-[10px] sm:text-xs font-bold rounded-full flex items-center justify-center px-1 animate-pulse">
                        </span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div x-show="notificationOpen"
                         @click.away="notificationOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute right-0 mt-2 w-72 sm:w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden"
                         style="display: none;">

                        <!-- Loading State -->
                        <div x-show="isLoading" class="p-8 text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div>
                            <p class="text-sm text-gray-500 mt-3">Loading...</p>
                        </div>

                        <!-- Content -->
                        <div x-show="!isLoading">
                            <div class="p-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                <h4 class="font-semibold text-sm text-gray-900 dark:text-white">Notifications</h4>
                                <div class="flex gap-2">
                                    <button x-show="unreadCount > 0"
                                            @click="markAllAsRead()"
                                            class="text-xs text-purple-600 hover:text-purple-700 dark:text-purple-400 transition">
                                        Mark all read
                                    </button>
                                    <button x-show="notifications.length > 0"
                                            @click="clearAllNotifications()"
                                            class="text-xs text-red-600 hover:text-red-700 dark:text-red-400 transition">
                                        Clear all
                                    </button>
                                </div>
                            </div>

                            <div class="max-h-80 sm:max-h-96 overflow-y-auto">
                                <template x-for="notification in notifications" :key="notification.id">
                                    <div class="p-3 border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition cursor-pointer"
                                         :class="{ 'bg-purple-50/30 dark:bg-purple-900/20': !notification.read }"
                                         @click="handleNotificationClick(notification)">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                                 :class="getIconClass(notification.type)">
                                                <span x-text="getIconEmoji(notification.type)" class="text-sm"></span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notification.title"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2" x-text="notification.message"></p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1" x-text="notification.time_ago || notification.created_at"></p>
                                            </div>
                                            <button x-show="!notification.read"
                                                    @click.stop="markSingleAsRead(notification.id)"
                                                    class="text-gray-400 hover:text-purple-600 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="notifications.length === 0" class="p-8 text-center">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No notifications</p>
                                </div>
                            </div>

                            <div class="p-3 border-t border-gray-100 dark:border-gray-700 text-center">
                                <a href="{{ route('notifications.index') }}" class="text-xs text-purple-600 hover:text-purple-700 dark:text-purple-400 transition">
                                    View all notifications →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endauth

                <!-- User Dropdown -->
                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 bg-gradient-to-r from-indigo-50 to-indigo-50/50 hover:from-indigo-100 hover:to-indigo-100 dark:from-gray-800 dark:to-gray-800/50 dark:hover:from-gray-700 dark:hover:to-gray-700 px-2 sm:px-3 py-1.5 rounded-xl border border-indigo-100/60 dark:border-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4F46E5&color=fff&bold=true&size=64"
                                 alt="Avatar" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg ring-2 ring-indigo-200 dark:ring-indigo-600">
                            <div class="hidden sm:block text-left">
                                <p class="text-xs sm:text-sm font-semibold text-gray-800 dark:text-gray-200 leading-none">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] sm:text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ ucfirst(Auth::user()->role) }}</p>
                            </div>
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        </div>

                        @if(auth()->user()->role === 'member')
                            <x-dropdown-link :href="route('member.dashboard')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Dashboard
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('member.bookings.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                My Bookings
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('member.receipts.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                My Receipts
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('member.classes.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Book a Class
                            </x-dropdown-link>
                        @endif

                        @if(auth()->user()->role === 'admin')
                            <x-dropdown-link :href="route('admin.dashboard')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 17h16"/></svg>
                                Admin Panel
                            </x-dropdown-link>
                        @endif

                        @if(auth()->user()->role === 'instructor')
                            <x-dropdown-link :href="route('instructor.dashboard')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                Instructor Panel
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('instructor.create')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Schedule Class
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('instructor.upcoming')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Upcoming Classes
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('instructor.calendar')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Calendar View
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('instructor.earnings.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                My Earnings
                            </x-dropdown-link>
                        @endif

                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="flex items-center gap-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @endauth
            </div>

            <!-- Guest Links (Mobile) -->
            @guest
            <div class="hidden sm:flex sm:items-center gap-2">
                <a href="{{ route('login') }}" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                    Login
                </a>
                <a href="{{ route('register') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-all duration-200">
                    Sign up
                </a>
            </div>
            @endguest

            <!-- Hamburger Menu Button -->
            <div class="flex items-center md:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition-colors">
                    <svg class="h-6 w-6 text-gray-600 dark:text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-t border-gray-100 dark:border-gray-800"
         style="display: none;">
        <div class="pt-2 pb-3 space-y-1 px-4">
            @auth
                <!-- Mobile AI Assistant Button -->
                <div class="px-3 py-2 mb-2">
                    <button @click="$dispatch('open-chat')" class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:shadow-lg transition-all duration-300 rounded-xl px-4 py-3 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <span class="text-white font-medium">AI Assistant</span>
                    </button>
                </div>

                <!-- Mobile Theme Switcher -->
                <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-800 mb-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Theme</p>
                    <div class="flex gap-2">
                        <button @click="setTheme('light')" class="flex-1 px-3 py-2 rounded-lg text-sm font-medium transition-all" :class="theme === 'light' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
                            Light
                        </button>
                        <button @click="setTheme('dark')" class="flex-1 px-3 py-2 rounded-lg text-sm font-medium transition-all" :class="theme === 'dark' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
                            Dark
                        </button>
                        <button @click="setTheme('system')" class="flex-1 px-3 py-2 rounded-lg text-sm font-medium transition-all" :class="theme === 'system' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
                            System
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation Links -->
                @if(auth()->user()->role === 'member')
                    <x-responsive-nav-link :href="route('member.dashboard')" :active="request()->routeIs('member.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('member.bookings.index')" :active="request()->routeIs('member.bookings.*')">My Bookings</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('member.receipts.index')" :active="request()->routeIs('member.receipts.*')">My Receipts</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('member.classes.index')" :active="request()->routeIs('member.classes.*')">Available Classes</x-responsive-nav-link>
                @endif

                @if(auth()->user()->role === 'instructor')
                    <x-responsive-nav-link :href="route('instructor.dashboard')" :active="request()->routeIs('instructor.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('instructor.upcoming')" :active="request()->routeIs('instructor.upcoming')">Upcoming Classes</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('instructor.create')" :active="request()->routeIs('instructor.create')">Schedule a Class</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('instructor.calendar')" :active="request()->routeIs('instructor.calendar')">Calendar</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('instructor.earnings.index')" :active="request()->routeIs('instructor.earnings.*')">My Earnings</x-responsive-nav-link>
                @endif

                @if(auth()->user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.earnings.index')" :active="request()->routeIs('admin.earnings.*')">Earnings</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.instructors.index')" :active="request()->routeIs('admin.instructors.*')">Instructors</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">Reports</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">Settings</x-responsive-nav-link>
                @endif

                <div class="border-t border-gray-100 dark:border-gray-800 my-3"></div>

                <!-- User Info -->
                <div class="px-3 py-2">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4F46E5&color=fff&bold=true&size=64" alt="Avatar" class="w-10 h-10 rounded-lg ring-2 ring-indigo-200 dark:ring-indigo-600">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>

                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            @else
                <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">Login</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">Register</x-responsive-nav-link>
            @endauth
        </div>
    </div>
</nav>

<script>
    function navigationComponent() {
        return {
            open: false,
            scrolled: false,
            theme: localStorage.getItem('theme') || 'system',
            notificationOpen: false,
            unreadCount: {{ $unreadCount ?? 0 }},
            notifications: [],
            isLoading: false,
            pollingInterval: null,

            init() {
                this.applyTheme(this.theme);
                this.setupScrollListener();
                this.setupSystemThemeListener();
                this.fetchUnreadCount();
                this.startPolling();
                this.setupEventListeners();
            },

            applyTheme(value) {
                if (value === 'system') {
                    const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    document.documentElement.classList.toggle('dark', isDark);
                } else {
                    document.documentElement.classList.toggle('dark', value === 'dark');
                }
            },

            setTheme(value) {
                this.theme = value;
                localStorage.setItem('theme', value);
                this.applyTheme(value);
            },

            setupScrollListener() {
                window.addEventListener('scroll', () => {
                    this.scrolled = window.scrollY > 10;
                });
            },

            setupSystemThemeListener() {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                    if (this.theme === 'system') {
                        document.documentElement.classList.toggle('dark', e.matches);
                    }
                });
            },

            setupEventListeners() {
                window.addEventListener('notification-received', () => {
                    this.fetchUnreadCount();
                    if (this.notificationOpen) {
                        this.fetchNotifications();
                    }
                });
            },

            startPolling() {
                if (this.pollingInterval) clearInterval(this.pollingInterval);

                const poll = () => {
                    this.fetchUnreadCount();
                };

                this.pollingInterval = setInterval(poll, 30000);

                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        if (this.pollingInterval) clearInterval(this.pollingInterval);
                    } else {
                        this.startPolling();
                        this.fetchUnreadCount();
                    }
                });
            },

            async fetchUnreadCount() {
                try {
                    const response = await fetch('/notifications/unread-count', {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.unreadCount = data.unread_count;
                    }
                } catch (error) {
                    console.error('Error fetching unread count:', error);
                }
            },

            async fetchNotifications() {
                this.isLoading = true;
                try {
                    const response = await fetch('/notifications/recent', {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.notifications = data.notifications;
                        this.unreadCount = data.unread_count;
                    }
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                } finally {
                    this.isLoading = false;
                }
            },

            toggleNotifications() {
                this.notificationOpen = !this.notificationOpen;
                if (this.notificationOpen && this.notifications.length === 0) {
                    this.fetchNotifications();
                }
            },

            async markSingleAsRead(notificationId) {
                try {
                    const response = await fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Content-Type': 'application/json'
                        }
                    });
                    if (response.ok) {
                        const notification = this.notifications.find(n => n.id === notificationId);
                        if (notification) notification.read = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                } catch (error) {
                    console.error('Error marking as read:', error);
                }
            },

            async markAllAsRead() {
                try {
                    const response = await fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Content-Type': 'application/json'
                        }
                    });
                    if (response.ok) {
                        this.notifications.forEach(n => n.read = true);
                        this.unreadCount = 0;
                        this.notificationOpen = false;
                    }
                } catch (error) {
                    console.error('Error marking all as read:', error);
                }
            },

            async clearAllNotifications() {
                if (!confirm('Are you sure you want to clear all notifications? This cannot be undone.')) return;

                try {
                    const response = await fetch('/notifications/clear-all', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Content-Type': 'application/json'
                        }
                    });
                    if (response.ok) {
                        this.notifications = [];
                        this.unreadCount = 0;
                        this.notificationOpen = false;
                    }
                } catch (error) {
                    console.error('Error clearing notifications:', error);
                }
            },

            handleNotificationClick(notification) {
                if (!notification.read) {
                    this.markSingleAsRead(notification.id);
                }
                if (notification.action_url && notification.action_url !== '#') {
                    window.location.href = notification.action_url;
                }
            },

            getIconClass(type) {
                const classes = {
                    'booking': 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                    'achievement': 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400',
                    'warning': 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                    'workout_reminder': 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400'
                };
                return classes[type] || 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400';
            },

            getIconEmoji(type) {
                const icons = {
                    'booking': '📅',
                    'achievement': '🏆',
                    'warning': '⚠️',
                    'workout_reminder': '💪',
                    'info': 'ℹ️'
                };
                return icons[type] || '🔔';
            }
        }
    }
</script>

<style>
    [x-cloak] { display: none !important; }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .animate-pulse {
        animation: pulse 2s infinite;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
