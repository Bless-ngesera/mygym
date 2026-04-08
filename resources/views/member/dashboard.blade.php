<x-app-layout>
    <x-slot name="header">
        <div id="welcome-message" class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                    <span>🏋️</span> {{ __('Welcome Back, ') . Auth::user()->name . '! 🎉' }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Track your fitness journey and stay motivated</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap justify-end">
                <span class="text-sm text-gray-500 bg-white/50 backdrop-blur-sm px-4 py-2 rounded-xl shadow-sm hidden sm:inline-block">
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
            setTimeout(() => {
                const welcomeMessage = document.getElementById('welcome-message');
                if (welcomeMessage) {
                    welcomeMessage.style.transition = 'opacity 0.8s ease';
                    welcomeMessage.style.opacity = '0';
                    setTimeout(() => welcomeMessage.remove(), 900);
                }
            }, 10000);
        </script>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen relative"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
               background-size: cover;
               background-position: center;
               background-attachment: fixed;">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/30 to-white/70 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10 relative z-10">

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div id="successMessage" class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl shadow-md flex items-center justify-between animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900 ml-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div id="errorMessage" class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl shadow-md flex items-center justify-between animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.style.display='none'" class="text-red-700 hover:text-red-900 ml-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            {{-- Hero / Profile Header --}}
            <div class="bg-white/80 backdrop-blur-sm shadow-lg sm:rounded-2xl p-6 border border-gray-100 flex flex-col md:flex-row items-center gap-6">
                <div class="flex items-center gap-4 flex-1">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg ring-4 ring-white/60 flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-gray-900">Welcome back, {{ Auth::user()->name }} 👋🏾</h1>
                        <p class="text-sm text-gray-600 mt-1">Keep going — every rep brings you closer to your goals.</p>
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-2 bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path></svg>
                                Active Member
                            </span>
                            <span class="text-sm text-gray-500">Member since {{ Auth::user()->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 w-full md:w-auto mt-4 md:mt-0">
                    <div class="text-center p-3 bg-white rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Workouts</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_workouts'] ?? 0) }}</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Upcoming</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($upcomingWorkouts->count() ?? 0) }}</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Goal</p>
                        <p class="text-lg font-bold text-gray-900">{{ $goals->first()?->progressPercentage() ?? 0 }}%</p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('member.classes.index') }}" class="px-5 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transform transition">
                    📅 Book a Class
                </a>
                <a href="{{ route('member.bookings.index') }}" class="px-5 py-3 bg-white text-gray-800 font-semibold rounded-full shadow border border-gray-100 hover:shadow-md transition">
                    📋 My Bookings
                </a>
                <button onclick="checkIn()" class="px-5 py-3 bg-green-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transition">
                    ✅ Check In
                </button>
                <button onclick="openNutritionModal()" class="px-5 py-3 bg-blue-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transition">
                    🥗 Log Nutrition
                </button>
                <a href="{{ route('profile.edit') }}" class="px-5 py-3 bg-yellow-500 text-white font-semibold rounded-full shadow hover:scale-[1.02] transition">
                    👤 My Profile
                </a>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-purple-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-purple-700">Total Workouts</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($stats['total_workouts'] ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['completed_workouts'] ?? 0 }} completed</p>
                        </div>
                        <div class="bg-purple-50 text-purple-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-green-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-green-700">Upcoming Classes</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($upcomingWorkouts->count() ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Scheduled sessions</p>
                        </div>
                        <div class="bg-green-50 text-green-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-yellow-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0 flex-1 pr-2">
                            <h3 class="text-sm font-semibold text-yellow-700">Your Coach</h3>
                            <p class="text-xl font-extrabold text-gray-900 mt-2 truncate">{{ $instructor->name ?? 'AI Coach' }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $instructor ? 'Personal Instructor' : '24/7 AI Support' }}</p>
                        </div>
                        <div class="bg-yellow-50 text-yellow-700 p-3 rounded-lg flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-indigo-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-indigo-700">Goal Progress</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ $goals->first()?->progressPercentage() ?? 0 }}%</p>
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 h-1.5 rounded-full" style="width: {{ $goals->first()?->progressPercentage() ?? 0 }}%"></div>
                            </div>
                        </div>
                        <div class="bg-indigo-50 text-indigo-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left Column --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Today's Workout --}}
                    <div class="bg-white/90 backdrop-blur-sm shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex justify-between items-center gap-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">💪 Today's Workout</h3>
                                    <p class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</p>
                                </div>
                                @if($todayWorkout && $todayWorkout->status !== 'completed')
                                    <button onclick="startWorkout({{ $todayWorkout->id }})"
                                        class="px-5 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transform transition text-sm whitespace-nowrap">
                                        Start Workout
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="p-6">
                            @if($todayWorkout)
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="font-bold text-gray-900">{{ $todayWorkout->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $todayWorkout->description }}</p>
                                    </div>
                                    <div class="space-y-3">
                                        @foreach($todayWorkout->exercises as $exercise)
                                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl hover:bg-purple-50 transition">
                                            <input type="checkbox"
                                                onchange="completeExercise({{ $exercise->pivot->id }}, this)"
                                                {{ $exercise->pivot->completed ? 'checked disabled' : '' }}
                                                class="w-5 h-5 text-purple-600 rounded flex-shrink-0 cursor-pointer">
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-gray-900 text-sm">{{ $exercise->name }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $exercise->pivot->sets }} sets × {{ $exercise->pivot->reps }} reps
                                                    @if($exercise->pivot->weight_kg) · {{ $exercise->pivot->weight_kg }}kg @endif
                                                    · Rest {{ $exercise->pivot->rest_seconds }}s
                                                </p>
                                            </div>
                                            <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @if($todayWorkout->status === 'in_progress')
                                        <button onclick="completeWorkout({{ $todayWorkout->id }})"
                                            class="w-full mt-4 px-4 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-full text-sm font-semibold shadow hover:scale-[1.02] transform transition">
                                            Complete Workout 🎉
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">No workout scheduled for today</p>
                                    <p class="text-sm text-gray-400 mt-1">Take a rest day or check your upcoming workouts</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Progress Charts --}}
                    <div class="bg-white/90 backdrop-blur-sm shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800">📊 Progress Tracking</h3>
                            <p class="text-sm text-gray-500">Your fitness journey over time</p>
                        </div>
                        <div class="p-6 space-y-8">
                            <div id="weightChartSkeleton" class="space-y-2">
                                <div class="h-4 bg-gray-100 rounded w-32 animate-pulse mb-4"></div>
                                <div class="h-64 bg-gray-50 rounded-xl animate-pulse flex items-end gap-1 p-4">
                                    @for($i = 0; $i < 8; $i++)
                                        <div class="flex-1 bg-gray-200 rounded-t" style="height: {{ rand(30, 90) }}%"></div>
                                    @endfor
                                </div>
                            </div>
                            <canvas id="weightChart" class="w-full hidden"></canvas>

                            <div id="workoutChartSkeleton" class="space-y-2">
                                <div class="h-4 bg-gray-100 rounded w-40 animate-pulse mb-4"></div>
                                <div class="h-64 bg-gray-50 rounded-xl animate-pulse flex items-end gap-1 p-4">
                                    @for($i = 0; $i < 8; $i++)
                                        <div class="flex-1 bg-gray-200 rounded-t" style="height: {{ rand(20, 80) }}%"></div>
                                    @endfor
                                </div>
                            </div>
                            <canvas id="workoutFrequencyChart" class="w-full hidden"></canvas>
                        </div>
                    </div>

                    {{-- Workout History with Pagination --}}
                    <div class="bg-white/90 backdrop-blur-sm shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-gray-800">📆 Workout History</h3>
                                <span class="text-xs text-gray-500">Showing {{ $workoutHistory->firstItem() ?? 0 }} - {{ $workoutHistory->lastItem() ?? 0 }} of {{ $workoutHistory->total() ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="space-y-0">
                            @forelse($workoutHistory as $workout)
                                <div class="flex items-center gap-4 p-4 border-b border-gray-50 hover:bg-gray-50 transition">
                                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-700 flex-shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($workout->date)->format('l, M d, Y') }}</p>
                                                <p class="text-xs text-gray-500">{{ $workout->count }} workout(s) completed</p>
                                            </div>
                                            <span class="text-xs font-semibold text-green-600">Completed</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-500">No workout history yet. Start working out!</div>
                            @endforelse
                        </div>
                        @if($workoutHistory->hasPages())
                            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                                {{ $workoutHistory->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="space-y-6">

                    {{-- Quick Actions --}}
                    <div class="bg-white/90 backdrop-blur-sm shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800">Quick Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            @if($currentAttendance && $currentAttendance->status === 'checked_in')
                                <button onclick="checkOut()"
                                    class="w-full px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-full text-sm font-semibold shadow hover:scale-[1.02] transform transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Check Out
                                </button>
                            @else
                                <button onclick="checkIn()"
                                    class="w-full px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-full text-sm font-semibold shadow hover:scale-[1.02] transform transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                    Check In
                                </button>
                            @endif
                            <button onclick="openNutritionModal()"
                                class="w-full px-4 py-2.5 bg-white text-gray-800 rounded-full text-sm font-semibold shadow border border-gray-100 hover:shadow-md transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Log Nutrition
                            </button>
                            <a href="{{ route('member.classes.index') }}"
                                class="w-full px-4 py-2.5 bg-green-600 text-white rounded-full text-sm font-semibold shadow hover:scale-[1.02] transform transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Book a Class
                            </button>
                        </div>
                    </div>

                    {{-- Nutrition --}}
                    <div class="bg-white/90 backdrop-blur-sm shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800">🥗 Today's Nutrition</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Daily intake overview</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="text-center p-3 bg-orange-50 rounded-xl border border-orange-100">
                                    <p class="text-2xl font-bold text-orange-600">{{ number_format($nutrition->calories ?? 0) }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Calories</p>
                                </div>
                                <div class="text-center p-3 bg-blue-50 rounded-xl border border-blue-100">
                                    <p class="text-2xl font-bold text-blue-600">{{ $nutrition->protein_grams ?? 0 }}g</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Protein</p>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-xl border border-green-100">
                                    <p class="text-2xl font-bold text-green-600">{{ $nutrition->carbs_grams ?? 0 }}g</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Carbs</p>
                                </div>
                                <div class="text-center p-3 bg-yellow-50 rounded-xl border border-yellow-100">
                                    <p class="text-2xl font-bold text-yellow-600">{{ $nutrition->fat_grams ?? 0 }}g</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Fat</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Goals --}}
                    @if($goals->count() > 0)
                    <div class="bg-white/90 backdrop-blur-sm shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-gray-800">🎯 Your Goals</h3>
                                <span class="text-xs font-semibold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full">{{ $goals->count() }} active</span>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach($goals as $goal)
                                <div>
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="text-gray-700 font-medium">{{ $goal->title }}</span>
                                        <span class="text-purple-700 font-bold">{{ $goal->progressPercentage() }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 h-2 rounded-full transition-all duration-1000" style="width: {{ $goal->progressPercentage() }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1.5">{{ $goal->current_value }} / {{ $goal->target_value }} {{ $goal->unit ?? $goal->type }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Attendance History with Pagination --}}
                    <div class="bg-white/90 backdrop-blur-sm shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-gray-800">📅 Attendance History</h3>
                                <span class="text-xs text-gray-500">Showing {{ $attendanceHistory->firstItem() ?? 0 }} - {{ $attendanceHistory->lastItem() ?? 0 }} of {{ $attendanceHistory->total() ?? 0 }}</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-0.5">Recent gym visits</p>
                        </div>
                        <div class="space-y-0">
                            @forelse($attendanceHistory as $attendance)
                                <div class="p-4 flex justify-between items-center border-b border-gray-50 hover:bg-gray-50 transition">
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $attendance->check_in->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $attendance->check_in->format('h:i A') }} - {{ $attendance->check_out?->format('h:i A') ?? 'Still here' }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-emerald-600">{{ $attendance->duration_minutes ?? 'In progress' }} min</span>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-500">No attendance records yet. Check in to start!</div>
                            @endforelse
                        </div>
                        @if($attendanceHistory->hasPages())
                            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                                {{ $attendanceHistory->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- Membership --}}
                    <div class="bg-gradient-to-r from-purple-600/80 to-indigo-700/80 rounded-2xl shadow-lg p-6 text-white">
                        <div class="flex justify-between items-start">
                            <div class="flex-1 min-w-0">
                                <p class="text-purple-100 text-sm">Current Plan</p>
                                <p class="text-2xl font-bold mt-1 truncate">{{ $subscription->plan_name ?? 'No Active Plan' }}</p>
                                @if($subscription)
                                    <p class="text-sm text-purple-100 mt-2">Expires: {{ $subscription->end_date->format('M d, Y') }}</p>
                                    <p class="text-xs text-purple-200 mt-1">{{ $subscription->daysRemaining() }} days remaining</p>
                                @endif
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm flex-shrink-0 ml-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                        </div>
                        @if($subscription)
                            <div class="mt-4 w-full bg-white/20 rounded-full h-2">
                                <div class="bg-white rounded-full h-2 transition-all duration-1000" style="width: {{ $subscription->getProgressPercentage() }}%"></div>
                            </div>
                        @endif
                        <button class="mt-4 w-full px-4 py-2.5 bg-white text-purple-700 rounded-full font-semibold hover:shadow-lg transition hover:scale-[1.02] transform">
                            Renew Membership →
                        </button>
                    </div>

                    {{-- Payment History with Pagination --}}
                    <div class="bg-white/90 backdrop-blur-sm shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-gray-800">💳 Recent Payments</h3>
                                <span class="text-xs text-gray-500">Showing {{ $paymentHistory->firstItem() ?? 0 }} - {{ $paymentHistory->lastItem() ?? 0 }} of {{ $paymentHistory->total() ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="space-y-0">
                            @forelse($paymentHistory as $payment)
                                <div class="p-4 flex justify-between items-center border-b border-gray-50 hover:bg-gray-50 transition">
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">UGX {{ number_format($payment->amount, 0) }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $payment->paid_at->format('M d, Y') }}</p>
                                    </div>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">{{ $payment->status }}</span>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-500">No payment history</div>
                            @endforelse
                        </div>
                        @if($paymentHistory->hasPages())
                            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                                {{ $paymentHistory->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Premium Chat Section - Always Visible --}}
            <div class="bg-white/90 backdrop-blur-sm shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-md">
                                💬
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Fitness Assistant</h3>
                                <p class="text-sm text-gray-500">Get instant help with workouts, nutrition, and motivation</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="switchChatTab('instructor')" id="instructorTabBtn" class="px-4 py-2 text-sm font-semibold rounded-full transition-all duration-200 {{ $instructor ? 'bg-purple-600 text-white shadow-md' : 'hidden' }}">
                                💪 My Coach
                            </button>
                            <button onclick="switchChatTab('ai')" id="aiTabBtn" class="px-4 py-2 text-sm font-semibold rounded-full transition-all duration-200 {{ $instructor ? 'bg-gray-200 text-gray-700' : 'bg-purple-600 text-white shadow-md' }}">
                                🤖 AI Coach
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Chat Container -->
                <div class="relative">
                    <!-- Instructor Chat -->
                    @if($instructor)
                    <div id="instructorChatPanel" class="p-6 {{ $instructor ? '' : 'hidden' }}">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($instructor->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $instructor->name }}</p>
                                <p class="text-xs text-green-600 flex items-center gap-1">
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                    Online
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3 mb-4 h-80 overflow-y-auto custom-scrollbar" id="instructorMessagesContainer">
                            @forelse($recentMessages as $message)
                                <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[75%] {{ $message->sender_id === Auth::id() ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-gray-100 text-gray-700 rounded-r-2xl rounded-tl-2xl' }} px-4 py-2.5 shadow-sm">
                                        <p class="text-sm">{{ $message->message }}</p>
                                        <p class="text-xs mt-1 opacity-70">{{ $message->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 py-8">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium">No messages yet</p>
                                    <p class="text-xs mt-1">Start a conversation with your instructor!</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="flex gap-2 mt-4">
                            <input type="text" id="instructorMessageInput" placeholder="Type your message..."
                                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm transition-all"
                                onkeypress="if(event.key==='Enter') sendMessageToInstructor()">
                            <button onclick="sendMessageToInstructor()"
                                class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl text-sm font-semibold shadow-md hover:shadow-lg transition-all hover:scale-105">
                                Send
                                <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- AI Chat Panel -->
                    <div id="aiChatPanel" class="p-6 {{ $instructor ? 'hidden' : '' }}">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-full flex items-center justify-center text-white text-lg">
                                🤖
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">AI Fitness Coach</p>
                                <p class="text-xs text-blue-600">Available 24/7</p>
                            </div>
                        </div>

                        <div class="space-y-3 mb-4 h-80 overflow-y-auto custom-scrollbar" id="aiMessagesContainer">
                            <div class="flex justify-start">
                                <div class="max-w-[75%] bg-gradient-to-r from-blue-500 to-cyan-600 text-white rounded-r-2xl rounded-tl-2xl px-4 py-2.5 shadow-sm">
                                    <p class="text-sm">🤖 Hi! I'm your AI Fitness Coach. I can help you with:</p>
                                    <ul class="text-sm mt-2 space-y-1 ml-4">
                                        <li>• 💪 Workout plans & exercises</li>
                                        <li>• 🥗 Nutrition advice & meal ideas</li>
                                        <li>• 🔥 Motivation & goal setting</li>
                                        <li>• ⚖️ Weight loss or muscle building tips</li>
                                        <li>• 🧘 Recovery & injury prevention</li>
                                    </ul>
                                    <p class="text-sm mt-2">What would you like to know today?</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4">
                            <input type="text" id="aiMessageInput" placeholder="Ask me anything about fitness, nutrition, or workouts..."
                                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm transition-all"
                                onkeypress="if(event.key==='Enter') sendMessageToAI()">
                            <button onclick="sendMessageToAI()"
                                class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl text-sm font-semibold shadow-md hover:shadow-lg transition-all hover:scale-105">
                                Send
                                <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Motivation Banner --}}
            <div class="bg-gradient-to-r from-purple-600/80 to-indigo-700/80 text-white rounded-2xl shadow-lg p-8 text-center">
                <h2 class="text-2xl font-bold mb-2">🔥 Daily Motivation</h2>
                <p class="text-lg italic mb-4">"Discipline is the bridge between goals and achievements."</p>
                <p class="text-sm text-purple-100">– African Fitness Wisdom</p>
            </div>

            {{-- Classes & Community --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white/90 backdrop-blur-sm shadow-md rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Our Classes</h3>
                        <a href="{{ route('member.classes.index') }}" class="text-sm text-indigo-600 font-semibold hover:underline">See all</a>
                    </div>
                    <div class="space-y-4">
                        @php
                            $classList = [
                                ['title' => 'Pilates', 'schedule' => 'Mon & Wed, 6 PM', 'icon' => '🧘'],
                                ['title' => 'Yoga', 'schedule' => 'Tue & Thu, 7 AM', 'icon' => '🧘‍♀️'],
                                ['title' => 'Dance Fitness', 'schedule' => 'Fri & Sat, 5 PM', 'icon' => '💃'],
                                ['title' => 'Boxing', 'schedule' => 'Mon & Fri, 7 PM', 'icon' => '🥊'],
                            ];
                        @endphp
                        @foreach($classList as $class)
                            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl hover:bg-purple-50 transition">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm text-xl flex-shrink-0">{{ $class['icon'] }}</div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $class['title'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $class['schedule'] }}</p>
                                </div>
                                <a href="{{ route('member.classes.index') }}" class="text-sm text-indigo-600 font-semibold hover:underline">Join →</a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-sm shadow-md rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">📆 Recent Activity</h3>
                        <span class="text-xs text-gray-500">Last 7 days</span>
                    </div>
                    <ul class="space-y-4">
                        @forelse($attendanceHistory->take(4) as $attendance)
                            <li class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-lg flex-shrink-0">
                                    🏋️
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-gray-900 text-sm">Gym Visit</p>
                                            <p class="text-xs text-gray-500">Checked in at {{ $attendance->check_in->format('h:i A') }} · {{ $attendance->duration_minutes }} min</p>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $attendance->check_in->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-center text-gray-500 py-4">No recent activity. Start working out!</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>

        {{-- Floating Action Button --}}
        <a href="{{ route('member.classes.index') }}" class="fixed bottom-8 right-8 bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-full shadow-xl ring-4 ring-white/50 transform hover:scale-105 transition z-50" title="Book a Class">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </a>
    </div>

    {{-- Footer --}}
    <footer class="bg-gradient-to-r from-gray-900 to-gray-800 border-t border-purple-500/30">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
                <div class="col-span-2 md:col-span-1 lg:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h4 class="text-2xl font-bold text-white tracking-wider">My<span class="text-purple-400">Gym</span></h4>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">Train smart, stay consistent, and celebrate your growth. We're a community rooted in African strength and unity.</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.77l-.44 2.89h-2.33v6.987A10 10 0 0022 12z"/></svg></a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.315 2c2.43 0 2.715.01 3.67.058 1.036.05 1.745.21 2.37.456.684.276 1.258.74 1.717 1.259.46.52.825 1.094 1.102 1.717.246.625.407 1.334.456 2.37.048.955.058 1.23.058 3.67s-.01 2.715-.058 3.67c-.05.97-.21 1.745-.456 2.37-.276.684-.74 1.258-1.259 1.717-.52.46-1.094.825-1.717 1.102-.625.246-1.334.407-2.37.456-.955.048-1.23.058-3.67.058s-2.715-.01-3.67-.058c-.97-.05-1.745-.21-2.37-.456-.684-.276-1.258-.74-1.717-1.259-.46-.52-.825-1.094-1.102-1.717-.246-.625-.407-1.334-.456-2.37-.048-.955-.058-1.23-.058-3.67s.01-2.715.058-3.67c.05-.97.21-1.745.456-2.37.276-.684.74-1.258 1.259-1.717.46-.52 1.094-.825 1.717-1.102.625-.246 1.334-.407 2.37-.456C9.59 2.01 9.875 2 12.315 2z"/></svg></a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg></a>
                    </div>
                </div>
                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Quick Links</h5>
                    <ul class="space-y-3">
                        <li><a href="{{ route('member.dashboard') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Dashboard</a></li>
                        <li><a href="{{ route('member.classes.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Browse Classes</a></li>
                        <li><a href="{{ route('member.bookings.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">My Bookings</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Manage Profile</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Popular Classes</h5>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Pilates</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Yoga</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Dance Fitness</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Boxing</a></li>
                    </ul>
                </div>
                <div class="col-span-2 md:col-span-1">
                    <h5 class="text-lg font-semibold text-white mb-4">Get In Touch</h5>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-start"><span class="mr-2 text-purple-400">📍</span><span>Ggaba road, Kampala, UGANDA</span></li>
                        <li class="flex items-start"><span class="mr-2 text-purple-400">📞</span><span>+256 700 123 456</span></li>
                        <li class="flex items-start"><span class="mr-2 text-purple-400">📧</span><span><a href="mailto:info@mygym.com" class="hover:text-purple-400 transition">info@mygym.com</a></span></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-purple-500/30 text-center">
                <p class="text-sm text-gray-500">&copy; {{ date('Y') }} MyGym. All rights reserved. Powered by Passion.</p>
            </div>
        </div>
    </footer>

    {{-- Nutrition Modal --}}
    <div id="nutritionModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
        <form onsubmit="submitNutrition(event)" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl animate-fade-in">
            @csrf
            <div class="flex justify-between items-center mb-5">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Log Today's Nutrition</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Track your daily macros</p>
                </div>
                <button type="button" onclick="closeNutritionModal()" class="text-gray-400 hover:text-gray-600 transition w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Calories</label>
                    <input type="number" name="calories" required value="{{ $nutrition->calories ?? 0 }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Protein (g)</label>
                    <input type="number" name="protein_grams" required value="{{ $nutrition->protein_grams ?? 0 }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Carbs (g)</label>
                    <input type="number" name="carbs_grams" required value="{{ $nutrition->carbs_grams ?? 0 }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fat (g)</label>
                    <input type="number" name="fat_grams" required value="{{ $nutrition->fat_grams ?? 0 }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeNutritionModal()"
                    class="px-4 py-2 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 rounded-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold shadow hover:scale-[1.02] transform transition">Save Changes</button>
            </div>
        </form>
    </div>

    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        #weightChart, #workoutFrequencyChart { max-height: 260px; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }

        /* Custom scrollbar for chat */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c084fc;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a855f7;
        }

        /* Chat message animations */
        .flex {
            animation: fadeInUp 0.3s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let currentChatType = '{{ $instructor ? "instructor" : "ai" }}';

        function switchChatTab(type) {
            currentChatType = type;
            const instructorPanel = document.getElementById('instructorChatPanel');
            const aiPanel = document.getElementById('aiChatPanel');
            const instructorBtn = document.getElementById('instructorTabBtn');
            const aiBtn = document.getElementById('aiTabBtn');

            if (type === 'instructor' && instructorPanel) {
                instructorPanel.classList.remove('hidden');
                aiPanel.classList.add('hidden');
                if (instructorBtn) {
                    instructorBtn.classList.remove('bg-gray-200', 'text-gray-700');
                    instructorBtn.classList.add('bg-purple-600', 'text-white', 'shadow-md');
                }
                if (aiBtn) {
                    aiBtn.classList.remove('bg-purple-600', 'text-white', 'shadow-md');
                    aiBtn.classList.add('bg-gray-200', 'text-gray-700');
                }
            } else if (aiPanel) {
                if (instructorPanel) instructorPanel.classList.add('hidden');
                aiPanel.classList.remove('hidden');
                if (aiBtn) {
                    aiBtn.classList.remove('bg-gray-200', 'text-gray-700');
                    aiBtn.classList.add('bg-purple-600', 'text-white', 'shadow-md');
                }
                if (instructorBtn && instructorBtn.classList) {
                    instructorBtn.classList.remove('bg-purple-600', 'text-white', 'shadow-md');
                    instructorBtn.classList.add('bg-gray-200', 'text-gray-700');
                }
            }
        }

        const progressData = @json($progressData);
        const workoutData = @json($workoutFrequency);

        window.addEventListener('DOMContentLoaded', () => {
            const colors = { grid: 'rgba(0,0,0,0.06)', text: '#6b7280' };

            const weightSkeleton = document.getElementById('weightChartSkeleton');
            const weightCanvas = document.getElementById('weightChart');
            if (weightCanvas) {
                if (progressData && progressData.length > 0) {
                    weightSkeleton.remove();
                    weightCanvas.classList.remove('hidden');
                    new Chart(weightCanvas, {
                        type: 'line',
                        data: {
                            labels: progressData.map(p => new Date(p.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
                            datasets: [{ label: 'Weight (kg)', data: progressData.map(p => p.weight_kg), borderColor: 'rgb(124,58,237)', backgroundColor: 'rgba(124,58,237,0.1)', tension: 0.4, fill: true, pointBackgroundColor: 'rgb(124,58,237)', pointRadius: 4, pointHoverRadius: 6 }]
                        },
                        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top', labels: { color: colors.text, font: { size: 12 } } } }, scales: { x: { grid: { color: colors.grid }, ticks: { color: colors.text } }, y: { beginAtZero: false, grid: { color: colors.grid }, ticks: { color: colors.text }, title: { display: true, text: 'Weight (kg)', color: colors.text } } } }
                    });
                } else {
                    weightSkeleton.innerHTML = '<p class="text-center text-gray-400 text-sm py-8">No weight data yet. Log your progress to see the chart.</p>';
                }
            }

            const workoutSkeleton = document.getElementById('workoutChartSkeleton');
            const workoutCanvas = document.getElementById('workoutFrequencyChart');
            if (workoutCanvas) {
                if (workoutData && workoutData.length > 0) {
                    workoutSkeleton.remove();
                    workoutCanvas.classList.remove('hidden');
                    new Chart(workoutCanvas, {
                        type: 'bar',
                        data: {
                            labels: workoutData.map(w => new Date(w.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
                            datasets: [{ label: 'Workouts Completed', data: workoutData.map(w => w.count), backgroundColor: 'rgba(124,58,237,0.7)', borderRadius: 8, borderSkipped: false }]
                        },
                        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top', labels: { color: colors.text, font: { size: 12 } } } }, scales: { x: { grid: { color: colors.grid }, ticks: { color: colors.text } }, y: { beginAtZero: true, grid: { color: colors.grid }, ticks: { color: colors.text, stepSize: 1 }, title: { display: true, text: 'Number of Workouts', color: colors.text } } } }
                    });
                } else {
                    workoutSkeleton.innerHTML = '<p class="text-center text-gray-400 text-sm py-8">No workout frequency data yet. Complete some workouts!</p>';
                }
            }
        });

        function startWorkout(id) {
            fetch(`/member/workouts/${id}/start`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(r => r.json()).then(d => { if (d.success) location.reload(); else alert('Failed to start workout'); });
        }

        function completeWorkout(id) {
            fetch(`/member/workouts/${id}/complete`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(r => r.json()).then(d => { if (d.success) location.reload(); else alert('Failed to complete workout'); });
        }

        function completeExercise(id, cb) {
            fetch(`/member/workout-exercises/${id}/complete`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(r => r.json()).then(d => { if (d.success) { cb.disabled = true; cb.checked = true; } });
        }

        function checkIn() {
            fetch('/member/check-in', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(r => r.json()).then(d => { if (d.success) location.reload(); else alert(d.error || 'Check-in failed'); });
        }

        function checkOut() {
            fetch('/member/check-out', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(r => r.json()).then(d => { if (d.success) location.reload(); else alert(d.error || 'Check-out failed'); });
        }

        function openNutritionModal() {
            document.getElementById('nutritionModal').classList.remove('hidden');
            document.getElementById('nutritionModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeNutritionModal() {
            document.getElementById('nutritionModal').classList.add('hidden');
            document.getElementById('nutritionModal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.getElementById('nutritionModal').addEventListener('click', function(e) { if (e.target === this) closeNutritionModal(); });

        function submitNutrition(e) {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(e.target));
            fetch('/member/nutrition', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }, body: JSON.stringify(data) })
                .then(r => r.json()).then(d => { if (d.success) location.reload(); else alert('Failed to save nutrition'); });
        }

        function sendMessageToInstructor() {
            const msg = document.getElementById('instructorMessageInput')?.value;
            if (!msg?.trim()) return;
            fetch('/member/messages', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }, body: JSON.stringify({ receiver_id: {{ $instructor->id ?? 0 }}, message: msg }) })
                .then(r => r.json()).then(d => { if (d.success) { location.reload(); } else { alert('Failed to send message'); } });
        }

        function sendMessageToAI() {
            const msg = document.getElementById('aiMessageInput')?.value;
            if (!msg?.trim()) return;

            const aiContainer = document.getElementById('aiMessagesContainer');
            const userMessageDiv = document.createElement('div');
            userMessageDiv.className = 'flex justify-end';
            userMessageDiv.innerHTML = `<div class="max-w-[75%] bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-l-2xl rounded-tr-2xl px-4 py-2.5 shadow-sm"><p class="text-sm">${escapeHtml(msg)}</p><p class="text-xs mt-1 opacity-70">Just now</p></div>`;
            aiContainer.appendChild(userMessageDiv);
            aiContainer.scrollTop = aiContainer.scrollHeight;

            document.getElementById('aiMessageInput').value = '';

            setTimeout(() => {
                const aiResponseDiv = document.createElement('div');
                aiResponseDiv.className = 'flex justify-start';
                aiResponseDiv.innerHTML = `<div class="max-w-[75%] bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-r-2xl rounded-tl-2xl px-4 py-2.5 shadow-sm"><p class="text-sm">${getAIResponse(msg)}</p><p class="text-xs mt-1 opacity-70">Just now</p></div>`;
                aiContainer.appendChild(aiResponseDiv);
                aiContainer.scrollTop = aiContainer.scrollHeight;
            }, 500);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function getAIResponse(message) {
            const msg = message.toLowerCase();
            if (msg.includes('workout') || msg.includes('exercise')) {
                return "💪 Great question! For optimal results, I recommend a mix of cardio and strength training. Try 3-4 strength sessions and 2-3 cardio sessions per week. Would you like a personalized workout plan?";
            } else if (msg.includes('nutrition') || msg.includes('diet') || msg.includes('food')) {
                return "🥗 Nutrition is key! Focus on whole foods: lean proteins, complex carbs, healthy fats, and plenty of vegetables. Aim for 1.6-2.2g of protein per kg of body weight. What specific nutrition advice are you looking for?";
            } else if (msg.includes('motivation') || msg.includes('motivate')) {
                return "🔥 Remember why you started! Every workout brings you one step closer to your goals. Set small achievable targets and celebrate every win. You've got this! 💪";
            } else if (msg.includes('protein')) {
                return "💪 Excellent sources of protein include chicken, fish, eggs, Greek yogurt, lentils, beans, and tofu. Aim for 20-40g per meal distributed throughout the day.";
            } else if (msg.includes('weight loss')) {
                return "🏋️ For weight loss, combine a calorie deficit with strength training to preserve muscle mass. Aim for 500-700 calorie deficit per day for sustainable loss of 0.5-1kg per week.";
            } else if (msg.includes('muscle')) {
                return "💪 To build muscle, focus on progressive overload - gradually increase weight or reps over time. Eat in a slight calorie surplus with adequate protein (1.6-2.2g/kg body weight). Rest and recovery are crucial!";
            } else {
                return "🤖 I'm here to help! Ask me about workouts, nutrition, motivation, weight loss, muscle building, or recovery. What would you like to know?";
            }
        }

        setTimeout(() => {
            ['successMessage', 'errorMessage'].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.style.transition = 'opacity 0.5s ease'; el.style.opacity = '0'; setTimeout(() => el.remove(), 500); }
            });
        }, 5000);
    </script>
</x-app-layout>
