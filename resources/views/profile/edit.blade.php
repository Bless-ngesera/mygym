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
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 bg-gray-50 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-100 transition-all duration-200 border border-gray-200">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </span>
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
                        {{ ucfirst(Auth::user()->role ?? 'user') }}
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
            <a href="{{ route('dashboard') }}"
               class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group
               {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 flex-shrink-0
                    {{ request()->routeIs('dashboard') ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-indigo-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-500 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </span>
                <span class="text-sm font-semibold">Dashboard</span>
            </a>

            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group
               {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 flex-shrink-0
                    {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-indigo-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-500 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </span>
                <span class="text-sm font-semibold">Admin Panel</span>
            </a>
            @endif

            <a href="{{ route('profile.edit') }}"
               class="flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 group
               {{ request()->routeIs('profile.edit') ? 'bg-indigo-600 text-white shadow-md' : 'bg-indigo-50 text-indigo-700' }}">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 flex-shrink-0
                    {{ request()->routeIs('profile.edit') ? 'bg-white/20' : 'bg-indigo-100' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                <span class="text-sm font-semibold">Profile</span>
            </a>
        </nav>

        {{-- Footer Quick Actions --}}
        <div class="flex-shrink-0 p-4 border-t border-gray-100">
            <div class="grid grid-cols-2 gap-2 mb-3">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center justify-center gap-1.5 px-2.5 py-2.5 text-xs font-semibold text-indigo-700 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-all duration-200 active:scale-95 border border-indigo-100">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-1.5 px-2.5 py-2.5 text-xs font-semibold text-red-700 bg-red-50 rounded-xl hover:bg-red-100 transition-all duration-200 active:scale-95 border border-red-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
            <div class="text-center">
                <p class="text-[11px] text-gray-400 font-medium">&copy; {{ date('Y') }} MyGym Uganda &mdash; v2.0</p>
            </div>
        </div>
    </aside>

    {{-- Main Content with proper margin adjustment and background image --}}
    <div id="mainContent" class="transition-all duration-300 ease-in-out">
        <main class="min-h-screen overflow-y-auto"
              style="background-image: url('{{ asset('images/background2.jpg') }}');
                     background-size: cover;
                     background-position: center;
                     background-attachment: fixed;">
            <div class="p-4 md:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">

                    {{-- Header Section --}}
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Profile Settings</h1>
                            <p class="text-sm text-gray-500 mt-1">Manage your account information and preferences</p>
                        </div>
                    </div>

                    {{-- Profile Sections --}}
                    <div class="space-y-6">
                        {{-- Update Profile Information --}}
                        <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900">Profile Information</h2>
                                        <p class="text-sm text-gray-500 mt-0.5">Update your account's profile information and email address</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>

                        {{-- Update Password --}}
                        <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900">Update Password</h2>
                                        <p class="text-sm text-gray-500 mt-0.5">Ensure your account is using a long, random password to stay secure</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>

                        {{-- Delete Account --}}
                        <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900">Delete Account</h2>
                                        <p class="text-sm text-gray-500 mt-0.5">Permanently delete your account and all associated data</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
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

        /* Form styling to match glass-morphism theme */
        input, select, textarea {
            @apply bg-white/80 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all;
        }
        button[type="submit"] {
            @apply bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-all duration-200 shadow-md hover:shadow-lg;
        }
    </style>
</x-app-layout>
