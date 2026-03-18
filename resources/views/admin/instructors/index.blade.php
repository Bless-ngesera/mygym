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

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-4 py-2 bg-gray-50 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-100 transition-all duration-200 border border-gray-200">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </span>
                </a>
                <a href="{{ route('admin.instructors.create') }}"
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Register Instructor
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Sidebar Overlay --}}
    <div id="sidebarOverlay"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[998] hidden"
         style="top: 0;"></div>

    {{-- Fixed Sidebar --}}
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

        {{-- Nav links --}}
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
            </a>

            <a href="{{ route('admin.instructors.index') }}"
               class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group
               {{ request()->routeIs('admin.instructors.*') ? 'bg-indigo-600 text-white shadow-md' : 'bg-indigo-50 text-indigo-700' }}">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 flex-shrink-0
                    {{ request()->routeIs('admin.instructors.*') ? 'bg-white/20' : 'bg-indigo-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.instructors.*') ? 'text-white' : 'text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </span>
                <span class="text-sm font-semibold">Instructors</span>
                @if($instructors->total() > 0)
                    <span class="ml-auto text-xs font-bold px-2 py-0.5 rounded-full bg-white/25 text-white">
                        {{ $instructors->total() }}
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

    {{-- Main Content with proper margin adjustment --}}
    <div id="mainContent" class="transition-all duration-300 ease-in-out">
        <div class="py-6">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div class="relative w-full sm:w-1/3">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input id="instructor-search" type="search" placeholder="Search instructors by name, email, or specialty..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all" />
                    </div>
                    <div class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg font-medium">
                        Total: <span class="font-bold">{{ $instructors->total() ?? 0 }}</span> instructors
                    </div>
                </div>

                <div class="grid gap-3">
                    @forelse($instructors as $ins)
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-white/90 backdrop-blur-sm border border-white/20 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200">
                            <div class="flex items-center gap-4 w-full sm:w-auto mb-3 sm:mb-0">
                                <!-- Avatar + Name block -->
                                <img src="{{ $ins->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($ins->name).'&background=4F46E5&color=fff&bold=true&size=128' }}"
                                    class="w-14 h-14 rounded-xl object-cover ring-2 ring-indigo-200 shadow-md" alt="Instructor photo">

                                <div>
                                    <div class="font-semibold text-gray-900 text-lg">{{ $ins->name }}</div>
                                    <div class="text-sm text-gray-500 flex items-center gap-2 mt-1">
                                        <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ $ins->specialty ?? 'General' }}</span>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Joined {{ $ins->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col items-start sm:items-end w-full sm:w-auto">
                                <div class="text-sm text-gray-600 flex items-center gap-1 mb-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $ins->email }}
                                </div>
                                <div class="mt-2 flex gap-2">
                                    <a href="{{ route('admin.instructors.edit', $ins->id) }}"
                                       class="px-3 py-1.5 bg-amber-50 text-amber-700 rounded-xl text-xs font-semibold hover:bg-amber-100 transition-all duration-200 flex items-center gap-1 border border-amber-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.instructors.destroy', $ins->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this instructor?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1.5 bg-red-50 text-red-700 rounded-xl text-xs font-semibold hover:bg-red-100 transition-all duration-200 flex items-center gap-1 border border-red-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center bg-white/90 backdrop-blur-sm border border-white/20 rounded-2xl shadow-lg">
                            <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">No instructors found.</p>
                            <a href="{{ route('admin.instructors.create') }}" class="inline-flex items-center gap-2 mt-3 text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Register your first instructor
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($instructors->hasPages())
                    <div class="mt-6">
                        {{ $instructors->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==================== SIDEBAR FUNCTIONALITY ====================
            const navbar = document.querySelector('nav') || document.querySelector('header');
            if (navbar) {
                const h = navbar.offsetHeight;
                document.documentElement.style.setProperty('--navbar-height', h + 'px');
            }

            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = document.getElementById('sidebarToggle');
            const closeBtn = document.getElementById('closeSidebar');
            const mainContent = document.getElementById('mainContent');

            let sidebarOpen = false;

            function openSidebar() {
                sidebarOpen = true;
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                if (window.innerWidth >= 1024) {
                    mainContent.style.marginLeft = '18rem';
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

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    if (sidebarOpen) closeSidebar();
                }
            });

            window.addEventListener('resize', function() {
                if (sidebarOpen && window.innerWidth >= 1024) {
                    mainContent.style.marginLeft = '18rem';
                } else if (sidebarOpen && window.innerWidth < 1024) {
                    mainContent.style.marginLeft = '0';
                }
            });

            // ==================== SEARCH FUNCTIONALITY ====================
            document.getElementById('instructor-search')?.addEventListener('input', function(){
                const q = this.value.toLowerCase();
                document.querySelectorAll('.grid > div').forEach(card => {
                    const txt = card.innerText.toLowerCase();
                    card.style.display = txt.includes(q) ? '' : 'none';
                });
            });
        });
    </script>

    <style>
        /* Custom scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(79,70,229,0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(79,70,229,0.45); }

        /* Sidebar transitions */
        #adminSidebar { will-change: transform; }
        #sidebarOverlay { transition: opacity 0.3s ease; }
        #mainContent { transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1); }
    </style>
</x-app-layout>
