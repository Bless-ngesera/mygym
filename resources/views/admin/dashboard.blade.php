<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                {{-- ✅ HAMBURGER — always visible, toggles sidebar on ALL screen sizes --}}
                <button id="sidebarToggle"
                    class="p-2.5 rounded-xl hover:bg-gray-100 focus:outline-none transition-all duration-200 active:scale-95 border border-transparent hover:border-gray-200 cursor-pointer"
                    aria-label="Toggle sidebar">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-lg flex items-center justify-center shadow-md">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-900 leading-tight text-base tracking-tight">MyGym Admin</h2>
                        <p class="text-xs text-gray-400 font-medium">Control Panel</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div class="hidden sm:block relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input id="admin-search" type="search" placeholder="Search instructors, members..."
                        class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm w-64 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 focus:bg-white transition-all duration-200 placeholder-gray-400"/>
                </div>

                <button class="relative p-2.5 rounded-xl hover:bg-gray-100 transition-all duration-200 border border-transparent hover:border-gray-200">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                </button>

                <div class="flex items-center space-x-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 px-3 py-1.5 rounded-xl transition-all duration-200 cursor-pointer">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=4F46E5&color=fff&bold=true"
                        alt="Avatar" class="w-8 h-8 rounded-lg ring-2 ring-indigo-200">
                    <div class="hidden sm:block">
                        <p class="text-sm font-semibold text-gray-800 leading-none">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Administrator</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    @if(!Auth::check() || (Auth::user()->role ?? '') !== 'admin')
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Unauthorized Access</h3>
                    <p class="text-gray-500 mt-2">You do not have permission to access the admin panel.</p>
                </div>
            </div>
        </div>
    @else
        @php
            $totalUsers       = $totalUsers ?? 0;
            $totalInstructors = $totalInstructors ?? 0;
            $totalMembers     = $totalMembers ?? 0;
            $totalEarnings    = $totalEarnings ?? 0;
            $recentInstructors = $recentInstructors ?? collect([]);
            $recentMembers    = $recentMembers ?? collect([]);
            $allInstructors   = $allInstructors ?? collect([]);
            $allMembers       = $allMembers ?? collect([]);
            $monthlyLabels    = $monthlyLabels ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            $monthlyEarnings  = $monthlyEarnings ?? array_fill(0, 12, 0);
            $instructorEarnings = $instructorEarnings ?? collect([]);
            $planEarnings     = $planEarnings ?? collect([]);
        @endphp

        {{-- ════════════════════════════════════════════════════════════
             MOBILE OVERLAY — sits behind sidebar, above content
             ════════════════════════════════════════════════════════════ --}}
        <div id="sidebarOverlay"
             class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[998] hidden"
             style="top: 0;"></div>

        {{-- ════════════════════════════════════════════════════════════
             FIXED SIDEBAR — uses fixed positioning so it NEVER scrolls
             The navbar on this app is typically 64px (h-16).
             Adjust --navbar-height if yours differs.
             ════════════════════════════════════════════════════════════ --}}
        <aside id="adminSidebar"
               class="fixed left-0 z-[999] w-72 bg-white border-r border-gray-100 shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-in-out"
               style="top: var(--navbar-height, 64px); height: calc(100vh - var(--navbar-height, 64px));">

            {{-- Sidebar Header --}}
            <div class="flex-shrink-0 px-5 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm tracking-tight">MyGym Admin</h3>
                            <p class="text-[11px] text-gray-400 font-medium">v2.0 • Control Panel</p>
                        </div>
                    </div>
                    <button id="closeSidebar" class="p-2 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- User Profile Card --}}
            <div class="flex-shrink-0 mx-4 mt-4 p-3.5 bg-gradient-to-br from-indigo-50 to-purple-50/40 rounded-2xl border border-indigo-100/60">
                <div class="flex items-center space-x-3">
                    <div class="relative flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=4F46E5&color=fff&bold=true&size=128"
                             alt="Avatar" class="w-12 h-12 rounded-xl ring-2 ring-indigo-200 shadow-md">
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email ?? 'admin@mygym.com' }}</p>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 mt-1 rounded-full text-[10px] font-semibold bg-indigo-600 text-white">
                            <span class="w-1.5 h-1.5 bg-white rounded-full opacity-80"></span>
                            Administrator
                        </span>
                    </div>
                </div>
            </div>

            {{-- Nav label --}}
            <div class="px-5 pt-5 pb-1">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Main Menu</span>
            </div>

            {{-- Nav links — only this part scrolls if nav items overflow --}}
            <nav class="flex-1 overflow-y-auto px-3 space-y-0.5 pb-2 custom-scrollbar">

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group
                   {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 flex-shrink-0
                        {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-indigo-100' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-500 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </span>
                    <span class="text-sm font-semibold">Dashboard</span>
                    @if(request()->routeIs('admin.dashboard'))
                        <span class="ml-auto w-1.5 h-1.5 bg-white rounded-full opacity-80"></span>
                    @endif
                </a>

                <a href="{{ route('admin.instructors.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group
                   {{ request()->routeIs('admin.instructors.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 flex-shrink-0
                        {{ request()->routeIs('admin.instructors.*') ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-indigo-100' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.instructors.*') ? 'text-white' : 'text-gray-500 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </span>
                    <span class="text-sm font-semibold">Instructors</span>
                    @if($totalInstructors > 0)
                        <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded-full
                            {{ request()->routeIs('admin.instructors.*') ? 'bg-white/25 text-white' : 'bg-indigo-100 text-indigo-700' }}">
                            {{ $totalInstructors }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.members.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group
                   {{ request()->routeIs('admin.members.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 flex-shrink-0
                        {{ request()->routeIs('admin.members.*') ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-indigo-100' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.members.*') ? 'text-white' : 'text-gray-500 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                    <span class="text-sm font-semibold">Members</span>
                    @if($totalMembers > 0)
                        <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded-full
                            {{ request()->routeIs('admin.members.*') ? 'bg-white/25 text-white' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $totalMembers }}
                        </span>
                    @endif
                </a>

                <div class="my-3 mx-2">
                    <div class="border-t border-gray-100"></div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-3 mb-1 px-1">Finance</span>
                </div>

                <a href="{{ route('admin.earnings.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group
                   {{ request()->routeIs('admin.earnings.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 flex-shrink-0
                        {{ request()->routeIs('admin.earnings.*') ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-emerald-100' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.earnings.*') ? 'text-white' : 'text-gray-500 group-hover:text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <span class="text-sm font-semibold">Earnings</span>
                </a>

                <a href="{{ route('admin.reports.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group
                   {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 flex-shrink-0
                        {{ request()->routeIs('admin.reports.*') ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-indigo-100' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-gray-500 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </span>
                    <span class="text-sm font-semibold">Reports</span>
                </a>

                <div class="my-3 mx-2">
                    <div class="border-t border-gray-100"></div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-3 mb-1 px-1">System</span>
                </div>

                <a href="{{ route('admin.settings.index') }}"
                   class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group
                   {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 flex-shrink-0
                        {{ request()->routeIs('admin.settings.*') ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-indigo-100' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-gray-500 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </span>
                    <span class="text-sm font-semibold">Settings</span>
                </a>
            </nav>

            {{-- Footer Quick Actions --}}
            <div class="flex-shrink-0 p-4 border-t border-gray-100">
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <a href="{{ route('admin.instructors.create') }}"
                       class="flex items-center justify-center gap-1.5 px-2.5 py-2.5 text-xs font-semibold text-indigo-700 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-all duration-200 active:scale-95 border border-indigo-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Instructor
                    </a>
                    <a href="{{ route('admin.members.create') }}"
                       class="flex items-center justify-center gap-1.5 px-2.5 py-2.5 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition-all duration-200 active:scale-95 border border-emerald-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Add Member
                    </a>
                </div>
                <div class="text-center">
                    <p class="text-[11px] text-gray-400 font-medium">&copy; {{ date('Y') }} MyGym Uganda &mdash; v2.0</p>
                </div>
            </div>
        </aside>

        {{-- ════════════════════════════════════════════════════════════
             MAIN CONTENT — has left margin that matches sidebar width
             when sidebar is open (on desktop). Scrolls independently.
             ════════════════════════════════════════════════════════════ --}}
        <div id="mainContent" class="transition-all duration-300 ease-in-out">
            <main class="min-h-screen overflow-y-auto"
                  style="background-image: url('{{ asset('images/background2.jpg') }}');
                         background-size: cover;
                         background-position: center;
                         background-attachment: fixed;">

                <div class="p-4 md:p-6 lg:p-8">

                    {{-- Summary Cards --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl p-5 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">All</span>
                            </div>
                            <div class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($totalUsers) }}</div>
                            <div class="text-xs font-medium text-gray-500 mt-0.5">Total Users</div>
                        </div>

                        <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl p-5 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-9 h-9 bg-purple-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Active</span>
                            </div>
                            <div class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($totalInstructors) }}</div>
                            <div class="text-xs font-medium text-gray-500 mt-0.5">Instructors</div>
                        </div>

                        <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl p-5 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">+8%</span>
                            </div>
                            <div class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($totalMembers) }}</div>
                            <div class="text-xs font-medium text-gray-500 mt-0.5">Members</div>
                        </div>

                        <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl p-5 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">UGX</span>
                            </div>
                            <div class="text-xl font-bold text-gray-900 tracking-tight">{{ number_format($totalEarnings, 0) }}</div>
                            <div class="text-xs font-medium text-gray-500 mt-0.5">Total Earnings</div>
                        </div>
                    </div>

                    {{-- Action Row --}}
                    <div class="flex flex-wrap items-center gap-3 mb-6">
                        <a href="{{ route('admin.instructors.create') }}"
                           class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl inline-flex items-center shadow-lg shadow-indigo-200 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 active:scale-95">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            Register Instructor
                        </a>
                        <a href="{{ route('admin.members.create') }}"
                           class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl inline-flex items-center shadow-lg shadow-emerald-200 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 active:scale-95">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Add Member
                        </a>
                    </div>

                    {{-- Flash messages --}}
                    @if(session('success'))
                        <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3">
                            <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-sm font-semibold">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-bold">Please fix the following errors:</span>
                            </div>
                            <ul class="list-disc pl-11 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Charts --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
                        <div id="earningsCard" class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl p-5 shadow-lg">
                            <div class="flex justify-between items-center mb-5">
                                <div>
                                    <h3 class="font-bold text-gray-900 text-sm">Monthly Earnings</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Revenue overview by month</p>
                                </div>
                                <div class="flex gap-1.5">
                                    <button id="exportEarningsPdf" class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium">PDF</button>
                                    <button id="exportEarningsCsv" class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium">CSV</button>
                                </div>
                            </div>
                            <canvas id="earningsChart" height="200"></canvas>
                        </div>

                        <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl p-5 shadow-lg">
                            <div class="flex justify-between items-center mb-5">
                                <div>
                                    <h3 class="font-bold text-gray-900 text-sm">Instructor Earnings</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Breakdown per instructor</p>
                                </div>
                                <button id="exportInstructorCsv" class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium">Export CSV</button>
                            </div>
                            <canvas id="instructorChart" height="200"></canvas>
                        </div>
                    </div>

                    {{-- Recent Instructors --}}
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg mb-5 overflow-hidden">
                        <div class="flex justify-between items-center px-5 py-4 border-b border-gray-100">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">Recent Instructors</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Latest registered instructors</p>
                            </div>
                            <a href="{{ route('admin.instructors.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">View All →</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50/80">
                                        <th class="px-3 py-3 sm:px-5 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="hidden sm:table-cell px-3 py-3 sm:px-5 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="hidden md:table-cell px-3 py-3 sm:px-5 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Joined</th>
                                        <th class="px-3 py-3 sm:px-5 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($recentInstructors as $instructor)
                                        <tr class="hover:bg-indigo-50/30 transition-colors duration-150">
                                            <td class="px-3 py-3 sm:px-5 sm:py-3.5">
                                                <div class="flex items-center gap-2 sm:gap-3">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($instructor->name) }}&background=4F46E5&color=fff&bold=true" alt="" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg shadow-sm flex-shrink-0">
                                                    <span class="text-sm font-semibold text-gray-800 truncate">{{ $instructor->name }}</span>
                                                </div>
                                            </td>
                                            <td class="hidden sm:table-cell px-3 py-3 sm:px-5 sm:py-3.5 text-sm text-gray-600">{{ $instructor->email }}</td>
                                            <td class="hidden md:table-cell px-3 py-3 sm:px-5 sm:py-3.5">
                                                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-lg">{{ $instructor->created_at->format('M d, Y') }}</span>
                                            </td>
                                            <td class="px-3 py-3 sm:px-5 sm:py-3.5">
                                                <a href="{{ route('admin.instructors.edit', $instructor->id) }}"
                                                   class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2 sm:px-2.5 py-1 rounded-lg transition-colors mr-1 sm:mr-2">Edit</a>
                                                <form action="{{ route('admin.instructors.destroy', $instructor->id) }}" method="POST" class="inline-block delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2 sm:px-2.5 py-1 rounded-lg transition-colors">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-5 py-8 text-center">
                                                <p class="text-sm text-gray-400 font-medium">No instructors found.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Recent Members --}}
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                        <div class="flex justify-between items-center px-5 py-4 border-b border-gray-100">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">Recent Members</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Latest gym members</p>
                            </div>
                            <a href="{{ route('admin.members.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">View All →</a>
                        </div>
                        <div class="divide-y divide-gray-50 px-2 py-2">
                            @forelse($recentMembers as $member)
                                <div class="flex items-center justify-between px-3 py-3 rounded-xl hover:bg-indigo-50/30 transition-colors duration-150">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=10b981&color=fff&bold=true" alt="" class="w-10 h-10 rounded-xl shadow-sm">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800">{{ $member->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $member->email }}</div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-lg">{{ $member->created_at->format('M d, Y') }}</span>
                                </div>
                            @empty
                                <div class="py-8 text-center">
                                    <p class="text-sm text-gray-400 font-medium">No members found.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>{{-- /padding --}}
            </main>
        </div>

        {{-- Modals --}}
        <div id="view-members-modal" class="hidden fixed inset-0 z-[1000] flex items-center justify-center p-4 modal">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-2xl w-full max-w-3xl shadow-2xl overflow-auto max-h-[90vh]">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10 rounded-t-2xl">
                    <h4 class="font-bold text-gray-900">All Members</h4>
                    <button data-modal-close class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">✕</button>
                </div>
                <div class="p-4 grid gap-2">
                    @forelse($allMembers as $member)
                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=10b981&color=fff&bold=true" alt="" class="w-11 h-11 rounded-xl">
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">{{ $member->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $member->email }}</div>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $member->created_at->format('M d, Y') }}</span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-sm text-gray-400">No members found.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div id="view-instructors-modal" class="hidden fixed inset-0 z-[1000] flex items-center justify-center p-4 modal">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-2xl w-full max-w-3xl shadow-2xl overflow-auto max-h-[90vh]">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10 rounded-t-2xl">
                    <h4 class="font-bold text-gray-900">All Instructors</h4>
                    <button data-modal-close class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">✕</button>
                </div>
                <div class="p-4 grid gap-2">
                    @forelse($allInstructors as $instructor)
                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($instructor->name) }}&background=4F46E5&color=fff&bold=true" alt="" class="w-11 h-11 rounded-xl">
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">{{ $instructor->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $instructor->email }}</div>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $instructor->created_at->format('M d, Y') }}</span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-sm text-gray-400">No instructors found.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <footer class="bg-gradient-to-r from-gray-900 to-gray-800 border-t border-purple-500/30">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
                    {{-- Column 1: Logo/Brand Info --}}
                    <div class="col-span-2 md:col-span-1 lg:col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex items-center">
                                <img src="{{ asset('images/Project_Logo.png') }}" alt="Gym Logo" class="h-7 w-auto object-contain ml-1">
                            </div>
                        </div>
                        <p class="text-sm text-gray-400 leading-relaxed">
                            Train smart, stay consistent, and celebrate your growth. We're a community rooted in African strength and unity.
                        </p>
                        <div class="flex space-x-4 mt-4">
                            <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.77l-.44 2.89h-2.33v6.987A10 10 0 0022 12z" clip-rule="evenodd" /></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.715.01 3.67.058 1.036.05 1.745.21 2.37.456.684.276 1.258.74 1.717 1.259.46.52.825 1.094 1.102 1.717.246.625.407 1.334.456 2.37.048.955.058 1.23.058 3.67s-.01 2.715-.058 3.67c-.05.97-.21 1.745-.456 2.37-.276.684-.74 1.258-1.259 1.717-.52.46-1.094.825-1.717 1.102-.625.246-1.334.407-2.37.456-.955.048-1.23.058-3.67.058s-2.715-.01-3.67-.058c-.97-.05-1.745-.21-2.37-.456-.684-.276-1.258-.74-1.717-1.259-.46-.52-.825-1.094-1.102-1.717-.246-.625-.407-1.334-.456-2.37-.048-.955-.058-1.23-.058-3.67s.01-2.715.058-3.67c.05-.97.21-1.745.456-2.37.276-.684.74-1.258 1.259-1.717.46-.52 1.094-.825 1.717-1.102.625-.246 1.334-.407 2.37-.456C9.59 2.01 9.875 2 12.315 2zm0 1.637c-2.35 0-2.6.01-3.535.056-.983.05-1.503.21-1.85.347-.417.164-.78.384-1.095.698-.315.315-.534.678-.698 1.095-.137.347-.297.867-.347 1.85-.046.935-.056 1.185-.056 3.535s.01 2.6.056 3.535c.05.983.21 1.503.347 1.85.164.417.384.78.698 1.095.315.315.678.534 1.095.698.347.137.867.297 1.85.347.935.046 1.185.056 3.535.056s2.6-.01 3.535-.056c.983-.05 1.503-.21 1.85-.347.417-.164.78-.384 1.095-.698.315-.315.534-.678.698-1.095.137-.347.297-.867.347-1.85.046-.935.056-1.185.056-3.535s-.01-2.6-.056-3.535c-.05-.983-.21-1.503-.347-1.85-.164-.417-.384-.78-.698-1.095-.315-.315-.678-.534-1.095-.698-.347-.137-.867-.297-1.85-.347-.935-.046-1.185-.056-3.535-.056zM12.315 5.564c-3.714 0-6.75 3.036-6.75 6.75s3.036 6.75 6.75 6.75 6.75-3.036 6.75-6.75-3.036-6.75-6.75-6.75zm0 11.235c-2.476 0-4.485-2.009-4.485-4.485S9.839 7.828 12.315 7.828s4.485 2.009 4.485 4.485-2.009 4.485-4.485 4.485zm4.991-9.982c-.52 0-.942-.423-.942-.942s.422-.942.942-.942.942.423.942.942-.422.942-.942.942z" clip-rule="evenodd" /></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Column 2: Admin Quick Links --}}
                    <div>
                        <h5 class="text-lg font-semibold text-white mb-4">Admin Panel</h5>
                        <ul class="space-y-3">
                            <li><a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📊 Dashboard</a></li>
                            <li><a href="{{ route('admin.members.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">👥 Manage Members</a></li>
                            <li><a href="{{ route('admin.instructors.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">👨‍🏫 Manage Instructors</a></li>
                            <li><a href="{{ route('admin.earnings.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💰 Earnings Overview</a></li>
                            <li><a href="{{ route('admin.reports.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📈 Reports</a></li>
                            <li><a href="{{ route('admin.settings.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">⚙️ Settings</a></li>
                        </ul>
                    </div>

                    {{-- Column 3: System Management --}}
                    <div>
                        <h5 class="text-lg font-semibold text-white mb-4">System</h5>
                        <ul class="space-y-3">
                            <li><a href="{{ route('admin.system.health') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🩺 System Health</a></li>
                            <li><a href="{{ route('admin.system.logs') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📋 System Logs</a></li>
                            <li><a href="{{ route('admin.database.backup') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💾 Database Backup</a></li>
                            <li>
                                <form method="POST" action="{{ route('admin.system.clear-cache') }}" class="inline" onsubmit="return confirm('Are you sure you want to clear the system cache?');">
                                    @csrf
                                    <button type="submit" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🗑️ Clear Cache</button>
                                </form>
                            </li>
                            <li><a href="{{ route('admin.system.queue-status') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">⏳ Queue Status</a></li>
                        </ul>
                    </div>

                    {{-- Column 4: Contact & Support --}}
                    <div class="col-span-2 md:col-span-1">
                        <h5 class="text-lg font-semibold text-white mb-4">Get In Touch</h5>
                        <ul class="space-y-3 text-sm text-gray-400">
                            <li class="flex items-start">
                                <span class="mr-2 text-purple-400">📍</span>
                                <span>Ggaba Road, Kampala, UGANDA</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2 text-purple-400">📞</span>
                                <span>+256 700 123 456</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2 text-purple-400">📧</span>
                                <span><a href="mailto:admin@mygym.com" class="hover:text-purple-400">admin@mygym.com</a></span>
                            </li>
                        </ul>
                        <div class="mt-6">
                            <h5 class="text-sm font-semibold text-white mb-2">Support Hours</h5>
                            <p class="text-xs text-gray-400">Monday - Friday: 9AM - 6PM</p>
                            <p class="text-xs text-gray-400">Saturday: 10AM - 4PM</p>
                            <p class="text-xs text-gray-400">Sunday: Closed</p>
                        </div>
                    </div>
                </div>

                {{-- Copyright Section with Links --}}
                <div class="mt-12 pt-8 border-t border-purple-500/30">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex gap-6">
                            <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">About Us</a>
                            <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">Terms of Service</a>
                            <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">Privacy Policy</a>
                            <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">Cookie Policy</a>
                        </div>
                        <div class="flex items-center gap-2">
                            <p class="text-sm text-gray-500">
                                &copy; {{ date('Y') }} MyGym. All rights reserved. Powered by Passion.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

    @endif {{-- end admin check --}}

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ─── Measure actual navbar height and store as CSS variable ─────────
        // This makes the sidebar top perfectly aligned regardless of Breeze/Jetstream navbar height
        const navbar = document.querySelector('nav') || document.querySelector('header');
        if (navbar) {
            const h = navbar.offsetHeight;
            document.documentElement.style.setProperty('--navbar-height', h + 'px');
        }

        // ─── Sidebar state ────────────────────────────────────────────────────
        const sidebar     = document.getElementById('adminSidebar');
        const overlay     = document.getElementById('sidebarOverlay');
        const toggleBtn   = document.getElementById('sidebarToggle');
        const closeBtn    = document.getElementById('closeSidebar');
        const mainContent = document.getElementById('mainContent');

        // Sidebar starts hidden (-translate-x-full). Open = show, Close = hide.
        let sidebarOpen = false;

        function openSidebar() {
            sidebarOpen = true;
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            // On large screens push the content over; on small just overlay
            if (window.innerWidth >= 1024) {
                mainContent.style.marginLeft = '18rem'; // 72 (w-72)
            }
        }

        function closeSidebar() {
            sidebarOpen = false;
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            mainContent.style.marginLeft = '0';
        }

        function toggleSidebar() {
            sidebarOpen ? closeSidebar() : openSidebar();
        }

        toggleBtn?.addEventListener('click', toggleSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);

        // Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (sidebarOpen) closeSidebar();
                const openModal = document.querySelector('.modal:not(.hidden)');
                if (openModal) closeModalEl(openModal);
            }
        });

        // Recalc on resize
        window.addEventListener('resize', function () {
            if (sidebarOpen && window.innerWidth >= 1024) {
                mainContent.style.marginLeft = '18rem';
            } else if (sidebarOpen && window.innerWidth < 1024) {
                mainContent.style.marginLeft = '0';
            }
        });

        // ─── Charts ──────────────────────────────────────────────────────────
        const monthlyLabels   = @json($monthlyLabels);
        const monthlyEarnings = @json($monthlyEarnings);
        const instructorData  = @json($instructorEarnings->map(fn($i) => ['name' => $i->name ?? 'Instructor', 'amount' => $i->amount ?? 0])->toArray());
        const instructorLabels  = instructorData.map(i => i.name);
        const instructorAmounts = instructorData.map(i => i.amount);

        const earningsCtx = document.getElementById('earningsChart')?.getContext('2d');
        if (earningsCtx) {
            new Chart(earningsCtx, {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Earnings (UGX)',
                        data: monthlyEarnings,
                        backgroundColor: 'rgba(79,70,229,0.07)',
                        borderColor: 'rgba(79,70,229,1)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4f46e5',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: 'rgba(17,24,39,0.9)', cornerRadius: 8, padding: 10 }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        const instructorCtx = document.getElementById('instructorChart')?.getContext('2d');
        if (instructorCtx && instructorLabels.length > 0) {
            new Chart(instructorCtx, {
                type: 'doughnut',
                data: {
                    labels: instructorLabels,
                    datasets: [{
                        data: instructorAmounts,
                        backgroundColor: ['#6366f1','#8b5cf6','#ec4899','#f97316','#10b981','#f59e0b','#06b6d4'],
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12, usePointStyle: true } },
                        tooltip: { backgroundColor: 'rgba(17,24,39,0.9)', cornerRadius: 8, padding: 10 }
                    }
                }
            });
        }

        // ─── CSV / PDF export ─────────────────────────────────────────────────
        window.downloadCSV = function (filename, rows) {
            const csv = rows.map(r => r.map(v => `"${String(v).replace(/"/g,'""')}"`).join(',')).join('\n');
            const a = Object.assign(document.createElement('a'), {
                href: URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' })),
                download: filename
            });
            document.body.appendChild(a); a.click(); a.remove();
        };

        window.exportElementToPDF = async function (el, filename) {
            if (!el) return;
            try {
                const canvas = await html2canvas(el, { scale: 2 });
                const img = canvas.toDataURL('image/png');
                const { jsPDF } = window.jspdf;
                if (jsPDF) {
                    const pdf = new jsPDF('landscape','pt','a4');
                    const pw = pdf.internal.pageSize.getWidth();
                    pdf.addImage(img,'PNG',0,0,pw,(canvas.height*pw)/canvas.width);
                    pdf.save(filename);
                } else {
                    Object.assign(document.createElement('a'),{ href: img, download: filename.replace('.pdf','.png') }).click();
                }
            } catch(e) { console.error(e); }
        };

        document.getElementById('exportEarningsPdf')?.addEventListener('click', () =>
            exportElementToPDF(document.getElementById('earningsCard'), 'monthly-earnings.pdf'));
        document.getElementById('exportEarningsCsv')?.addEventListener('click', () =>
            downloadCSV('monthly-earnings.csv', [['Month','Earnings (UGX)'], ...monthlyLabels.map((m,i) => [m, monthlyEarnings[i]])]));
        document.getElementById('exportInstructorCsv')?.addEventListener('click', () =>
            downloadCSV('instructor-earnings.csv', [['Instructor','Earnings (UGX)'], ...instructorData.map(i => [i.name, i.amount])]));

        // ─── Modals ───────────────────────────────────────────────────────────
        function openModalEl(id) {
            const m = document.getElementById(id);
            if (m) { m.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
        }
        function closeModalEl(m) {
            if (!m) return;
            m.classList.add('hidden'); document.body.style.overflow = '';
        }
        document.querySelectorAll('[data-modal-open]').forEach(btn =>
            btn.addEventListener('click', e => { e.preventDefault(); openModalEl(btn.getAttribute('data-modal-open')); }));
        document.querySelectorAll('[data-modal-close]').forEach(btn =>
            btn.addEventListener('click', () => closeModalEl(btn.closest('.modal'))));
        document.querySelectorAll('.modal').forEach(m =>
            m.addEventListener('click', e => { if (e.target === m) closeModalEl(m); }));

        // ─── Search ───────────────────────────────────────────────────────────
        document.getElementById('admin-search')?.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('table tbody tr').forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        // ─── Delete confirm ───────────────────────────────────────────────────
        document.querySelectorAll('.delete-form').forEach(form =>
            form.addEventListener('submit', e => { if (!confirm('Are you sure?')) e.preventDefault(); }));

        // ─── Photo preview ────────────────────────────────────────────────────
        const photoInput   = document.getElementById('photo-input');
        const photoPreview = document.getElementById('photo-preview');
        if (photoInput && photoPreview) {
            photoInput.addEventListener('change', function () {
                const f = this.files[0];
                if (f) { photoPreview.src = URL.createObjectURL(f); photoPreview.classList.remove('hidden'); }
                else photoPreview.classList.add('hidden');
            });
        }
    });
    </script>

    <style>
        /* ── Custom scrollbar ── */
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(79,70,229,0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(79,70,229,0.45); }

        /* ── Sidebar transition ── */
        #adminSidebar { will-change: transform; }

        /* ── Overlay fade ── */
        #sidebarOverlay { transition: opacity 0.3s ease; }

        /* ── Main content smooth push ── */
        #mainContent { transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1); }

        /* ── Prevent body scroll when sidebar overlay is visible (mobile) ── */
        body.sidebar-open { overflow: hidden; }
    </style>

</x-app-layout>
