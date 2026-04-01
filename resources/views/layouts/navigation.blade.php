<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/Project_Logo.png') }}"
                             alt="MyGym Logo"
                             class="h-9 w-auto">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        <!-- Dashboard Link (always visible for authenticated users) -->
                        @if(auth()->user()->role === 'admin')
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                Dashboard
                            </x-nav-link>
                        @else
                            {{-- <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                Dashboard
                            </x-nav-link> --}}
                        @endif

                        <!-- Role-Based Navigation Links -->
                        @if(auth()->user()->role === 'member')
                            <x-nav-link :href="route('member.dashboard')" :active="request()->routeIs('member.dashboard')">
                                Dashboard
                            </x-nav-link>

                            <x-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')">
                                Available Classes
                            </x-nav-link>

                            <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.*')">
                                My Bookings
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

                            <x-nav-link :href="route('instructor.earnings')" :active="request()->routeIs('instructor.earnings')">
                                My Earnings
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->role === 'admin')
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

            <!-- Settings Dropdown (Authenticated Only) -->
            @auth
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2.5 bg-gradient-to-r from-indigo-50 to-indigo-50/50 hover:from-indigo-100 hover:to-indigo-100 px-3 py-1.5 rounded-xl border border-indigo-100/60 transition-all duration-200">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4F46E5&color=fff&bold=true&size=64"
                                 alt="Avatar" class="w-8 h-8 rounded-lg ring-2 ring-indigo-200">
                            <div class="hidden sm:block text-left">
                                <p class="text-sm font-semibold text-gray-800 leading-none">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ ucfirst(Auth::user()->role) }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- User Info Header -->
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>

                        <!-- Role-based quick links -->
                        @if(auth()->user()->role === 'member')
                            <x-dropdown-link :href="route('bookings.index')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                My Bookings
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('bookings.create')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Book a Class
                            </x-dropdown-link>
                        @endif

                        @if(auth()->user()->role === 'admin')
                            <x-dropdown-link :href="route('admin.dashboard')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 17h16"/>
                                </svg>
                                Admin Panel
                            </x-dropdown-link>
                        @endif

                        @if(auth()->user()->role === 'instructor')
                            <x-dropdown-link :href="route('instructor.dashboard')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Instructor Panel
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('instructor.create')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Schedule Class
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('instructor.upcoming')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Upcoming Classes
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('instructor.calendar')" class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Calendar View
                            </x-dropdown-link>

                        @endif

                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profile
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="flex items-center gap-2 text-red-600 hover:text-red-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @endauth

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
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-gray-100 focus:outline-none transition-colors">
                    <svg class="h-6 w-6 text-gray-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/95 backdrop-blur-md border-t border-gray-100">
        <div class="pt-2 pb-3 space-y-1 px-4">
            @auth
                <!-- Dashboard (Mobile) -->
                @if(auth()->user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        Dashboard
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-responsive-nav-link>
                @endif

                <!-- Mobile Role-Based Links -->
                @if(auth()->user()->role === 'member')
                    <x-responsive-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.*')">
                        My Bookings
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')">
                        Classes
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('bookings.create')" :active="request()->routeIs('bookings.create')">
                        Book a Class
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

                    <x-responsive-nav-link :href="route('instructor.earnings')" :active="request()->routeIs('instructor.earnings')">
                        My Earnings
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->role === 'admin')
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
                <div class="border-t border-gray-100 my-3"></div>

                <!-- User Info -->
                <div class="px-3 py-2">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4F46E5&color=fff&bold=true&size=64"
                             alt="Avatar" class="w-10 h-10 rounded-lg ring-2 ring-indigo-200">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
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
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="text-red-600 hover:text-red-700">
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
