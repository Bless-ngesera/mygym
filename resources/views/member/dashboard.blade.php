{{-- resources/views/member/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Welcome Back, {{ Auth::user()->name }}! 🎉
                </h2>
                <p class="text-sm text-gray-500 mt-1" id="dailyMotivation"></p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 bg-white/50 backdrop-blur-sm px-4 py-2 rounded-xl shadow-sm">
                    {{ now()->format('l, F j, Y') }}
                </span>
                <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Alert Messages --}}
            @if(session('success'))
                <div id="successMessage" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl shadow-md flex items-center justify-between animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900">×</button>
                </div>
            @endif

            @if(session('error'))
                <div id="errorMessage" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- Stats Cards Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 p-5 hover:shadow-xl transition-all group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Workouts</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalWorkouts ?? 0) }}</p>
                            <p class="text-xs text-green-600 mt-1">
                                <span class="inline-flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    +{{ $workoutIncrease ?? 0 }}% this month
                                </span>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 p-5 hover:shadow-xl transition-all group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Active Goals</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $activeGoalsCount ?? 0 }}</p>
                            <p class="text-xs text-blue-600 mt-1">{{ $completedGoalsCount ?? 0 }} completed</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 p-5 hover:shadow-xl transition-all group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Current Streak</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $currentStreak ?? 0 }} days</p>
                            <p class="text-xs text-orange-600 mt-1">Best: {{ $bestStreak ?? 0 }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-600 to-indigo-600 rounded-2xl shadow-lg p-5 text-white group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm">Membership</p>
                            <p class="text-2xl font-bold mt-1">{{ $subscription?->plan_name ?? 'No Active Plan' }}</p>
                            @if($subscription)
                                <p class="text-xs text-purple-200 mt-1">{{ $subscription->daysRemaining() }} days left</p>
                            @endif
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm group-hover:scale-110 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                    </div>
                    @if($subscription)
                        <div class="mt-3">
                            <div class="w-full bg-white/20 rounded-full h-1.5">
                                <div class="bg-white rounded-full h-1.5 transition-all" style="width: {{ $subscription->getProgressPercentage() }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left Column (2/3 width) --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Today's Workout Section --}}
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden hover:shadow-xl transition">
                        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-gray-800">💪 Today's Workout</h3>
                                <p class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</p>
                            </div>
                            <button onclick="openScheduleWorkoutModal()"
                                    class="px-4 py-2 text-sm bg-purple-50 text-purple-600 rounded-full hover:bg-purple-100 transition">
                                + Schedule Workout
                            </button>
                        </div>
                        <div class="p-5">
                            @if($todayWorkout && $todayWorkout->exercises->count() > 0)
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-lg">{{ $todayWorkout->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $todayWorkout->description ?? 'No description' }}</p>
                                        <div class="flex flex-wrap gap-3 mt-2">
                                            <span class="text-xs text-purple-600 bg-purple-50 px-2 py-1 rounded-full">⏱️ {{ $todayWorkout->duration ?? 45 }} min</span>
                                            <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">🔥 {{ $todayWorkout->calories_burn ?? rand(300, 600) }} cal</span>
                                        </div>
                                    </div>

                                    <div class="space-y-2 max-h-96 overflow-y-auto">
                                        @foreach($todayWorkout->exercises as $exercise)
                                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-purple-50 transition group">
                                            <input type="checkbox"
                                                   onchange="completeExercise({{ $exercise->pivot->id ?? $exercise->id }}, this)"
                                                   {{ ($exercise->pivot->completed ?? false) ? 'checked disabled' : '' }}
                                                   class="w-5 h-5 text-purple-600 rounded cursor-pointer">
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-800 text-sm">{{ $exercise->name }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $exercise->pivot->sets ?? 3 }} sets × {{ $exercise->pivot->reps ?? 12 }} reps
                                                    @if($exercise->pivot->weight_kg ?? false)
                                                        · {{ $exercise->pivot->weight_kg }}kg
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    @if(($todayWorkout->status ?? 'scheduled') !== 'completed')
                                        <button onclick="completeWorkout({{ $todayWorkout->id }})"
                                                class="w-full mt-3 px-4 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold hover:shadow-lg hover:scale-[1.02] transition transform">
                                            Complete Workout 🎉
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">No workout scheduled for today</p>
                                    <p class="text-sm text-gray-400 mt-1">Take a rest day or schedule a workout</p>
                                    <button onclick="openScheduleWorkoutModal()" class="mt-4 text-purple-600 text-sm font-semibold hover:underline inline-flex items-center gap-1">
                                        Schedule a workout →
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Progress Tracking Charts --}}
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden hover:shadow-xl transition">
                        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-gray-800">📊 Progress Tracking</h3>
                                <p class="text-sm text-gray-500">Your fitness journey over time</p>
                            </div>
                            <button onclick="openLogWeightModal()" class="text-sm text-purple-600 hover:underline">+ Log Weight</button>
                        </div>
                        <div class="p-5">
                            @if(count($progressLabels ?? []) > 0)
                                <canvas id="progressChart" height="200"></canvas>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-gray-500">No progress data yet</p>
                                    <button onclick="openLogWeightModal()" class="mt-2 text-purple-600 text-sm">Log your first weight →</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right Column (1/3 width) --}}
                <div class="space-y-6">

                    {{-- Quick Actions --}}
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 p-5">
                        <h3 class="font-bold text-gray-800 mb-3">⚡ Quick Actions</h3>
                        <div class="space-y-2">
                            <button onclick="checkIn()"
                                    class="w-full px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl font-semibold hover:shadow-lg hover:scale-[1.02] transition transform flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                Check In
                            </button>
                            <button onclick="openLogWeightModal()"
                                    class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-xl font-semibold hover:shadow-lg hover:scale-[1.02] transition transform flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                                Log Weight
                            </button>
                            <button onclick="openSetGoalModal()"
                                    class="w-full px-4 py-2.5 bg-green-600 text-white rounded-xl font-semibold hover:shadow-lg hover:scale-[1.02] transition transform flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                Set New Goal
                            </button>
                            <button onclick="openLogNutritionModal()"
                                    class="w-full px-4 py-2.5 bg-orange-600 text-white rounded-xl font-semibold hover:shadow-lg hover:scale-[1.02] transition transform flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Log Nutrition
                            </button>
                        </div>
                    </div>

                    {{-- Active Goals --}}
                    @if(($goals ?? collect())->count() > 0)
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 p-5">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-gray-800">🎯 Active Goals</h3>
                            <button onclick="openSetGoalModal()" class="text-xs text-purple-600 hover:underline">+ Add</button>
                        </div>
                        <div class="space-y-4">
                            @foreach($goals as $goal)
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-700 font-medium">{{ $goal->title }}</span>
                                        <span class="text-purple-600 font-bold">{{ $goal->progressPercentage() }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $goal->progressPercentage() }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1.5">{{ number_format($goal->current_value, 1) }} / {{ number_format($goal->target_value, 1) }} {{ $goal->unit }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Today's Nutrition --}}
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 p-5">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-gray-800">🥗 Today's Nutrition</h3>
                            <button onclick="openLogNutritionModal()" class="text-xs text-purple-600 hover:underline">Log Meal</button>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="text-center p-3 bg-orange-50 rounded-xl border border-orange-100">
                                <p class="text-2xl font-bold text-orange-600">{{ number_format($todayNutrition->calories ?? 0) }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">Calories</p>
                            </div>
                            <div class="text-center p-3 bg-blue-50 rounded-xl border border-blue-100">
                                <p class="text-2xl font-bold text-blue-600">{{ number_format($todayNutrition->protein_grams ?? 0) }}g</p>
                                <p class="text-xs text-gray-500 mt-0.5">Protein</p>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-xl border border-green-100">
                                <p class="text-2xl font-bold text-green-600">{{ number_format($todayNutrition->carbs_grams ?? 0) }}g</p>
                                <p class="text-xs text-gray-500 mt-0.5">Carbs</p>
                            </div>
                            <div class="text-center p-3 bg-yellow-50 rounded-xl border border-yellow-100">
                                <p class="text-2xl font-bold text-yellow-600">{{ number_format($todayNutrition->fat_grams ?? 0) }}g</p>
                                <p class="text-xs text-gray-500 mt-0.5">Fat</p>
                            </div>
                        </div>
                    </div>

                    {{-- AI Chat Assistant --}}
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                        <div class="p-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">🤖</span>
                                <div>
                                    <h3 class="font-bold">AI Fitness Coach</h3>
                                    <p class="text-xs text-purple-100">Available 24/7</p>
                                </div>
                            </div>
                        </div>
                        <div class="h-80 overflow-y-auto p-4 space-y-3 bg-gray-50" id="chatMessages">
                            <div class="flex justify-start animate-fade-in">
                                <div class="max-w-[85%] bg-white rounded-2xl rounded-tl-none px-4 py-2 shadow-sm border border-gray-100">
                                    <p class="text-sm text-gray-700">👋 Hi! I'm your AI fitness coach. Ask me about:</p>
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">💪 Workouts</span>
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">🥗 Nutrition</span>
                                        <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">🔥 Motivation</span>
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">📈 Progress</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 border-t border-gray-100 bg-white">
                            <div class="flex gap-2">
                                <input type="text" id="chatInput"
                                       placeholder="Ask me anything about fitness..."
                                       class="flex-1 px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm"
                                       onkeypress="if(event.key==='Enter') sendChatMessage()">
                                <button onclick="sendChatMessage()"
                                        class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition shadow-sm">
                                    Send
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Instructor Chat --}}
                    @if($instructor ?? false)
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                        <div class="p-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                    <span>👨‍🏫</span>
                                </div>
                                <div>
                                    <h3 class="font-bold">Chat with {{ $instructor->name }}</h3>
                                    <p class="text-xs text-green-100">Your personal coach</p>
                                </div>
                            </div>
                        </div>
                        <div class="h-64 overflow-y-auto p-4 space-y-3 bg-gray-50" id="instructorMessages">
                            @forelse($recentMessages ?? [] as $message)
                                <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }} animate-fade-in">
                                    <div class="max-w-[80%] {{ $message->sender_id === Auth::id() ? 'bg-purple-600 text-white rounded-2xl rounded-tr-none' : 'bg-white text-gray-700 rounded-2xl rounded-tl-none shadow-sm border border-gray-100' }} px-3 py-2">
                                        <p class="text-sm">{{ $message->message }}</p>
                                        <p class="text-xs mt-1 opacity-70">{{ $message->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 py-8">
                                    <p class="text-sm">No messages yet</p>
                                    <p class="text-xs mt-1">Start a conversation with your instructor!</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-3 border-t border-gray-100 bg-white">
                            <div class="flex gap-2">
                                <input type="text" id="instructorChatInput"
                                       placeholder="Message your instructor..."
                                       class="flex-1 px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                                       onkeypress="if(event.key==='Enter') sendToInstructor()">
                                <button onclick="sendToInstructor()"
                                        class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition shadow-sm">
                                    Send
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modals (Same as before) --}}
    <div id="scheduleWorkoutModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50" onclick="if(event.target===this) closeScheduleWorkoutModal()">
        <div class="bg-white rounded-2xl max-w-md w-full mx-4 shadow-2xl animate-fade-in">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Schedule Workout</h3>
                <button onclick="closeScheduleWorkoutModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 rounded-lg hover:bg-gray-100 transition">×</button>
            </div>
            <form id="scheduleWorkoutForm" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Workout Type</label>
                    <select name="workout_template_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Select workout...</option>
                        @foreach($workoutTemplates ?? [] as $template)
                            <option value="{{ $template->id }}">{{ $template->title }} ({{ $template->duration ?? 45 }} min)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date & Time</label>
                    <input type="datetime-local" name="scheduled_at" required
                           min="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <div class="flex gap-3 pt-3">
                    <button type="button" onclick="closeScheduleWorkoutModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-xl font-semibold hover:bg-purple-700 transition shadow">Schedule</button>
                </div>
            </form>
        </div>
    </div>

    <div id="logWeightModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50" onclick="if(event.target===this) closeLogWeightModal()">
        <div class="bg-white rounded-2xl max-w-md w-full mx-4 shadow-2xl animate-fade-in">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Log Weight</h3>
                <button onclick="closeLogWeightModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 rounded-lg hover:bg-gray-100 transition">×</button>
            </div>
            <form id="logWeightForm" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                    <input type="number" step="0.1" name="weight_kg" required placeholder="Enter your weight"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <div class="flex gap-3 pt-3">
                    <button type="button" onclick="closeLogWeightModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition shadow">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="setGoalModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50" onclick="if(event.target===this) closeSetGoalModal()">
        <div class="bg-white rounded-2xl max-w-md w-full mx-4 shadow-2xl animate-fade-in">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Set New Goal</h3>
                <button onclick="closeSetGoalModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 rounded-lg hover:bg-gray-100 transition">×</button>
            </div>
            <form id="setGoalForm" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Goal Title</label>
                    <input type="text" name="title" required placeholder="e.g., Lose 5kg, Build Muscle"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Goal Type</label>
                    <select name="type" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="weight_loss">Weight Loss</option>
                        <option value="muscle_gain">Muscle Gain</option>
                        <option value="endurance">Endurance</option>
                        <option value="strength">Strength</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Value</label>
                    <input type="number" step="0.1" name="target_value" required placeholder="Target amount"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <select name="unit" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="kg">kg</option>
                        <option value="lbs">lbs</option>
                        <option value="km">km</option>
                        <option value="minutes">minutes</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Date</label>
                    <input type="date" name="target_date" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <div class="flex gap-3 pt-3">
                    <button type="button" onclick="closeSetGoalModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition shadow">Set Goal</button>
                </div>
            </form>
        </div>
    </div>

    <div id="logNutritionModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50" onclick="if(event.target===this) closeLogNutritionModal()">
        <div class="bg-white rounded-2xl max-w-md w-full mx-4 shadow-2xl animate-fade-in">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Log Nutrition</h3>
                <button onclick="closeLogNutritionModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 rounded-lg hover:bg-gray-100 transition">×</button>
            </div>
            <form id="logNutritionForm" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meal Type</label>
                    <select name="meal_type" required class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="breakfast">🍳 Breakfast</option>
                        <option value="lunch">🥗 Lunch</option>
                        <option value="dinner">🍽️ Dinner</option>
                        <option value="snack">🍎 Snack</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Calories</label>
                    <input type="number" name="calories" required placeholder="Calories"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Protein (g)</label>
                        <input type="number" name="protein_grams" step="0.1" placeholder="Protein"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Carbs (g)</label>
                        <input type="number" name="carbs_grams" step="0.1" placeholder="Carbs"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fat (g)</label>
                        <input type="number" name="fat_grams" step="0.1" placeholder="Fat"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex gap-3 pt-3">
                    <button type="button" onclick="closeLogNutritionModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transition shadow">Log Meal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-gradient-to-r from-gray-900 to-gray-800 border-t border-purple-500/30 mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
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

                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Quick Links</h5>
                    <ul class="space-y-3">
                        <li><a href="{{ route('member.dashboard') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Dashboard</a></li>
                        <li><a href="{{ route('classes.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Browse Classes</a></li>
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

            <div class="mt-12 pt-8 border-t border-purple-500/30 text-center">
                <p class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} MyGym. All rights reserved. Powered by Passion.
                </p>
            </div>
        </div>
    </footer>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }
        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #c084fc;
            border-radius: 10px;
        }
        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #a855f7;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Daily motivation quotes
        const motivationQuotes = [
            "💪 \"The only bad workout is the one that didn't happen.\"",
            "🔥 \"Discipline is the bridge between goals and achievements.\"",
            "🏋️ \"Your body can stand almost anything. It's your mind you have to convince.\"",
            "⭐ \"Small daily improvements are the key to staggering long-term results.\"",
            "🎯 \"Don't stop when you're tired. Stop when you're done.\""
        ];

        document.getElementById('dailyMotivation').textContent = motivationQuotes[Math.floor(Math.random() * motivationQuotes.length)];

        // Progress Chart
        @if(count($progressLabels ?? []) > 0)
        const ctx = document.getElementById('progressChart')?.getContext('2d');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($progressLabels ?? []),
                    datasets: [{
                        label: 'Weight (kg)',
                        data: @json($progressValues ?? []),
                        borderColor: '#9333ea',
                        backgroundColor: 'rgba(147, 51, 234, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#9333ea',
                        pointBorderColor: '#fff',
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: { beginAtZero: false, title: { display: true, text: 'Weight (kg)' } },
                        x: { title: { display: true, text: 'Date' } }
                    }
                }
            });
        }
        @endif

        // AI Chat Function
        async function sendChatMessage() {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();
            if (!message) return;

            const chatContainer = document.getElementById('chatMessages');

            // Add user message
            const userDiv = document.createElement('div');
            userDiv.className = 'flex justify-end animate-fade-in';
            userDiv.innerHTML = `<div class="max-w-[85%] bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-2xl rounded-tr-none px-4 py-2 shadow-sm"><p class="text-sm">${escapeHtml(message)}</p><p class="text-xs mt-1 opacity-70">Just now</p></div>`;
            chatContainer.appendChild(userDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;

            input.value = '';
            input.disabled = true;

            // Add typing indicator
            const typingDiv = document.createElement('div');
            typingDiv.className = 'flex justify-start animate-fade-in';
            typingDiv.id = 'typingIndicator';
            typingDiv.innerHTML = `<div class="bg-gray-200 rounded-2xl rounded-tl-none px-4 py-2"><div class="flex gap-1"><span class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span><span class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span><span class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span></div></div>`;
            chatContainer.appendChild(typingDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;

            try {
                const response = await fetch('/member/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                document.getElementById('typingIndicator')?.remove();

                const aiDiv = document.createElement('div');
                aiDiv.className = 'flex justify-start animate-fade-in';
                aiDiv.innerHTML = `<div class="max-w-[85%] bg-white text-gray-700 rounded-2xl rounded-tl-none px-4 py-2 shadow-sm border border-gray-100"><p class="text-sm">${escapeHtml(data.response)}</p><p class="text-xs mt-1 text-gray-400">Just now</p></div>`;
                chatContainer.appendChild(aiDiv);
                chatContainer.scrollTop = chatContainer.scrollHeight;
            } catch (error) {
                document.getElementById('typingIndicator')?.remove();
                const aiDiv = document.createElement('div');
                aiDiv.className = 'flex justify-start animate-fade-in';
                aiDiv.innerHTML = `<div class="max-w-[85%] bg-white text-gray-700 rounded-2xl rounded-tl-none px-4 py-2 shadow-sm border border-gray-100"><p class="text-sm">Sorry, I'm having trouble connecting. Please try again.</p><p class="text-xs mt-1 text-gray-400">Just now</p></div>`;
                chatContainer.appendChild(aiDiv);
                chatContainer.scrollTop = chatContainer.scrollHeight;
            } finally {
                input.disabled = false;
                input.focus();
            }
        }

        async function sendToInstructor() {
            const input = document.getElementById('instructorChatInput');
            const message = input.value.trim();
            if (!message) return;

            try {
                const response = await fetch('/member/messages/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        receiver_id: {{ $instructor->id ?? 0 }},
                        message: message
                    })
                });
                const data = await response.json();
                if (data.success) location.reload();
                else alert('Failed to send message');
            } catch (error) {
                alert('Failed to send message');
            }
        }

        async function completeWorkout(id) {
            if (!confirm('Complete this workout?')) return;
            try {
                const response = await fetch(`/member/workouts/${id}/complete`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await response.json();
                if (data.success) location.reload();
                else alert('Failed to complete workout');
            } catch (error) {
                alert('Failed to complete workout');
            }
        }

        async function completeExercise(exerciseId, checkbox) {
            try {
                const response = await fetch(`/member/workout-exercises/${exerciseId}/complete`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await response.json();
                if (data.success) {
                    checkbox.disabled = true;
                    checkbox.checked = true;
                }
            } catch (error) {
                checkbox.checked = false;
            }
        }

        async function checkIn() {
            try {
                const response = await fetch('/member/attendance/check-in', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await response.json();
                if (data.success) location.reload();
                else alert(data.error || 'Check-in failed');
            } catch (error) {
                alert('Failed to check in');
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Modal Functions
        function openScheduleWorkoutModal() {
            document.getElementById('scheduleWorkoutModal').classList.remove('hidden');
            document.getElementById('scheduleWorkoutModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeScheduleWorkoutModal() {
            document.getElementById('scheduleWorkoutModal').classList.add('hidden');
            document.getElementById('scheduleWorkoutModal').classList.remove('flex');
            document.body.style.overflow = '';
        }
        function openLogWeightModal() {
            document.getElementById('logWeightModal').classList.remove('hidden');
            document.getElementById('logWeightModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeLogWeightModal() {
            document.getElementById('logWeightModal').classList.add('hidden');
            document.getElementById('logWeightModal').classList.remove('flex');
            document.body.style.overflow = '';
        }
        function openSetGoalModal() {
            document.getElementById('setGoalModal').classList.remove('hidden');
            document.getElementById('setGoalModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeSetGoalModal() {
            document.getElementById('setGoalModal').classList.add('hidden');
            document.getElementById('setGoalModal').classList.remove('flex');
            document.body.style.overflow = '';
        }
        function openLogNutritionModal() {
            document.getElementById('logNutritionModal').classList.remove('hidden');
            document.getElementById('logNutritionModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeLogNutritionModal() {
            document.getElementById('logNutritionModal').classList.add('hidden');
            document.getElementById('logNutritionModal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Form Submissions
        document.getElementById('scheduleWorkoutForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('{{ route("member.workouts.schedule") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData
                });
                const data = await response.json();
                if (data.success) location.reload();
                else alert('Failed to schedule workout');
            } catch (error) {
                alert('Failed to schedule workout');
            }
        });

        document.getElementById('logWeightForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('{{ route("member.progress.weight") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData
                });
                const data = await response.json();
                if (data.success) location.reload();
                else alert('Failed to log weight');
            } catch (error) {
                alert('Failed to log weight');
            }
        });

        document.getElementById('setGoalForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('{{ route("member.goals.store") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData
                });
                const data = await response.json();
                if (data.success) location.reload();
                else alert('Failed to set goal');
            } catch (error) {
                alert('Failed to set goal');
            }
        });

        document.getElementById('logNutritionForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                const response = await fetch('{{ route("member.nutrition.store") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData
                });
                const data = await response.json();
                if (data.success) location.reload();
                else alert('Failed to log nutrition');
            } catch (error) {
                alert('Failed to log nutrition');
            }
        });

        // Auto-scroll chats
        const chatContainer = document.getElementById('chatMessages');
        if (chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;
        const instructorContainer = document.getElementById('instructorMessages');
        if (instructorContainer) instructorContainer.scrollTop = instructorContainer.scrollHeight;
    </script>
</x-app-layout>
