<x-app-layout>
    <x-slot name="header">
        <div id="welcome-message" class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800">
                    {{ __('Welcome Back, ') . Auth::user()->name . '!' }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Track your fitness journey and stay motivated</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500 bg-white/50 backdrop-blur-sm px-4 py-2 rounded-xl shadow-sm">
                    Last login: {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('M d, Y') : now()->format('M d, Y') }}
                </span>
                <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <script>
            // Fade out then remove the welcome message after 5s
            setTimeout(() => {
                const welcomeMessage = document.getElementById('welcome-message');
                if (welcomeMessage) {
                    welcomeMessage.style.transition = 'opacity 0.8s ease';
                    welcomeMessage.style.opacity = '0';
                    setTimeout(() => welcomeMessage.remove(), 900);
                }
            }, 5000);
        </script>
    </x-slot>

    <div class="py-12 min-h-screen"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Success Message --}}
            @if(session('success'))
                <div id="successMessage" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl shadow-md flex items-center justify-between animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Hero Section --}}
            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                <div class="flex flex-col md:flex-row items-center justify-between p-8">
                    <div class="space-y-4">
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Welcome to <span class="text-purple-700">MyGym</span></h1>
                        <p class="text-gray-600 max-w-md leading-relaxed">
                            Stay motivated and keep track of your workouts, schedules, and progress — every rep brings you closer to your goals. Train smart, stay consistent, and celebrate your growth!
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('profile.edit') }}"
                               class="inline-block px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                                View Profile
                            </a>
                            <a href="{{ route('classes.index') }}"
                               class="inline-block px-5 py-2.5 bg-white/80 hover:bg-white text-gray-700 font-semibold rounded-lg transition-all duration-200 border border-gray-200 shadow-sm">
                                Browse Classes
                            </a>
                        </div>
                    </div>
                    <div class="mt-6 md:mt-0">
                        <div class="w-full md:w-80 h-56 rounded-xl overflow-hidden shadow-lg ring-4 ring-white/50">
                            <img src="https://images.pexels.com/photos/1552242/pexels-photo-1552242.jpeg?auto=compress&cs=tinysrgb&w=800"
                                 alt="African fitness community"
                                 loading="lazy"
                                 class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 overflow-hidden group">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">This Month</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $workoutsCompleted ?? 24 }}</p>
                        <p class="text-sm text-gray-500 mt-1">Workouts Completed</p>
                        <div class="mt-3 text-xs text-green-600 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            <span>+12% from last month</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 overflow-hidden group">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">This Week</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $upcomingClasses ?? 3 }}</p>
                        <p class="text-sm text-gray-500 mt-1">Upcoming Classes</p>
                        <a href="{{ route('classes.index') }}" class="mt-3 text-xs text-purple-600 hover:text-purple-700 inline-flex items-center gap-1">Book now →</a>
                    </div>
                </div>

                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 overflow-hidden group">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Next Session</span>
                        </div>
                        <p class="text-xl font-bold text-gray-900">{{ $trainerName ?? 'Nzabanita Caleb' }}</p>
                        <p class="text-sm text-gray-500 mt-1">Personal Trainer</p>
                        <p class="text-xs text-gray-400 mt-2">Next: Tomorrow, 6:00 PM</p>
                    </div>
                </div>

                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 overflow-hidden group">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">{{ $goalProgress ?? 70 }}%</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $goalProgress ?? 70 }}%</p>
                        <p class="text-sm text-gray-500 mt-1">Goal Progress</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 h-2 rounded-full" style="width: {{ $goalProgress ?? 70 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Classes Section --}}
            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Our Classes</h3>
                            <p class="text-sm text-gray-500 mt-1">Explore our fitness programs</p>
                        </div>
                        <a href="{{ route('classes.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-semibold flex items-center gap-1">View All →</a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @php
                            $classList = [
                                [
                                    'title' => 'Pilates',
                                    'description' => 'Strengthen your core and improve flexibility with our guided Pilates sessions.',
                                    'image' => 'https://images.pexels.com/photos/4804312/pexels-photo-4804312.jpeg?auto=compress&cs=tinysrgb&w=400',
                                    'schedule' => 'Mon & Wed, 6 PM',
                                    'color' => 'purple',
                                    'icon' => '🧘'
                                ],
                                [
                                    'title' => 'Yoga',
                                    'description' => 'Find balance and peace through our calming Yoga classes for all levels.',
                                    'image' => 'https://images.pexels.com/photos/8436605/pexels-photo-8436605.jpeg?auto=compress&cs=tinysrgb&w=400',
                                    'schedule' => 'Tue & Thu, 7 AM',
                                    'color' => 'emerald',
                                    'icon' => '🧘‍♀️'
                                ],
                                [
                                    'title' => 'Dance Fitness',
                                    'description' => 'Move to the rhythm with Afrobeat-inspired dance workouts.',
                                    'image' => 'https://images.pexels.com/photos/8957662/pexels-photo-8957662.jpeg?auto=compress&cs=tinysrgb&w=400',
                                    'schedule' => 'Fri & Sat, 5 PM',
                                    'color' => 'amber',
                                    'icon' => '💃'
                                ],
                                [
                                    'title' => 'Boxing',
                                    'description' => 'Unleash your strength with high-energy boxing classes.',
                                    'image' => 'https://images.pexels.com/photos/4804040/pexels-photo-4804040.jpeg?auto=compress&cs=tinysrgb&w=400',
                                    'schedule' => 'Mon & Fri, 7 PM',
                                    'color' => 'indigo',
                                    'icon' => '🥊'
                                ],
                            ];
                        @endphp

                        @foreach ($classList as $class)
                            <div class="bg-white/80 backdrop-blur-sm border border-white/40 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-200 hover:-translate-y-1 group">
                                <div class="h-40 overflow-hidden relative">
                                    <img src="{{ $class['image'] }}" alt="{{ $class['title'] }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <div class="absolute top-2 right-2 w-8 h-8 bg-white/90 rounded-lg flex items-center justify-center shadow-sm">
                                        <span class="text-lg">{{ $class['icon'] }}</span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h4 class="text-lg font-bold text-gray-800">{{ $class['title'] }}</h4>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $class['description'] }}</p>
                                    <div class="flex items-center justify-between mt-3">
                                        <p class="text-xs font-semibold text-{{ $class['color'] }}-600">{{ $class['schedule'] }}</p>
                                        <a href="{{ route('classes.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-semibold">Join →</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Why Choose MyGym Section --}}
            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                <div class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-2xl mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Why Choose MyGym?</h3>
                    <p class="text-gray-600 max-w-2xl mx-auto leading-relaxed">
                        At MyGym, we're more than a gym—we're a community rooted in African strength and unity. Our modern facilities, expert trainers, and vibrant classes like Afrobeat Dance Fitness and Boxing empower you to achieve your goals. With affordable memberships and a welcoming environment, we make fitness accessible and fun for everyone.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4 mt-6">
                        <a href="{{ route('member.classes') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Book a Class
                        </a>
                        <a href="{{ route('classes.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white/80 hover:bg-white text-gray-700 font-semibold rounded-xl transition-all duration-200 border border-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 17h16"></path></svg>
                            View All Classes
                        </a>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Recent Activity</h3>
                            <p class="text-sm text-gray-500 mt-1">Your latest fitness achievements</p>
                        </div>
                        <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">Last 7 days</span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-3 rounded-xl hover:bg-purple-50/30 transition-all duration-200 group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <span class="text-xl">🏋️</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Completed Boxing Session</p>
                                    <p class="text-xs text-gray-400">You crushed your boxing workout! 🔥</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 bg-white px-2 py-1 rounded-full">2 hours ago</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-xl hover:bg-purple-50/30 transition-all duration-200 group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <span class="text-xl">🕺</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Joined Dance Fitness Class</p>
                                    <p class="text-xs text-gray-400">Afrobeat vibes! Great energy! 💃</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 bg-white px-2 py-1 rounded-full">Yesterday</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-xl hover:bg-purple-50/30 transition-all duration-200 group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <span class="text-xl">🧘</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Achieved Yoga Milestone</p>
                                    <p class="text-xs text-gray-400">50 classes completed! Amazing progress! 🌟</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 bg-white px-2 py-1 rounded-full">3 days ago</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-xl hover:bg-purple-50/30 transition-all duration-200 group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <span class="text-xl">💪</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">Strength Training PR</p>
                                    <p class="text-xs text-gray-400">New personal record! +10kg 🏆</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 bg-white px-2 py-1 rounded-full">5 days ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-gradient-to-r from-gray-900 to-gray-800 border-t border-purple-500/30">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
                {{-- Column 1: Logo/Brand Info --}}
                <div class="col-span-2 md:col-span-1 lg:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h4 class="text-2xl font-bold text-white tracking-wider">My<span class="text-purple-400">Gym</span></h4>
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

                {{-- Column 2: Quick Links --}}
                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Quick Links</h5>
                    <ul class="space-y-3">
                        <li><a href="{{ route('member.dashboard') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Dashboard</a></li>
                        <li><a href="{{ route('classes.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Browse Classes</a></li>
                        <li><a href="{{ route('member.bookings') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">My Bookings</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Manage Profile</a></li>
                    </ul>
                </div>

                {{-- Column 3: Classes --}}
                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Popular Classes</h5>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Pilates</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Yoga</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Dance Fitness</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Boxing</a></li>
                    </ul>
                </div>

                {{-- Column 4: Contact Info --}}
                <div class="col-span-2 md:col-span-1">
                    <h5 class="text-lg font-semibold text-white mb-4">Get In Touch</h5>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-start">
                            <span class="mr-2 text-purple-400">📍</span>
                            <span>Ggaba road, Kampala, UGANDA</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-purple-400">📞</span>
                            <span>+256 700 123 456</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-purple-400">📧</span>
                            <span><a href="mailto:info@mygym.com" class="hover:text-purple-400">info@mygym.com</a></span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Copyright Section --}}
            <div class="mt-12 pt-8 border-t border-purple-500/30 text-center">
                <p class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} MyGym. All rights reserved. Powered by Passion.
                </p>
            </div>
        </div>
    </footer>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <script>
        // Auto-dismiss messages after 5 seconds
        setTimeout(function() {
            let successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s ease';
                successMessage.style.opacity = '0';
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 500);
            }
        }, 5000);
    </script>
</x-app-layout>
