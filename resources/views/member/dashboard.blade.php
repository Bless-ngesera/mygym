{{-- resources/views/member/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Member Dashboard
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Welcome back, {{ Auth::user()->name }}
                </p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('member.workouts.history') }}"
                   class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Workout History
                </a>
                <a href="{{ route('plans.index') }}"
                   class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                    </svg>
                    Plans
                </a>
                <a href="{{ route('profile.edit') }}"
                   class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    My Profile
                </a>
            </div>
        </div>
    </x-slot>

    <main class="min-h-screen"
          style="background-image: url('{{ asset('images/background2.jpg') }}');
                 background-size: cover;
                 background-position: center;
                 background-attachment: fixed;">
        <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8 md:py-8">

            {{-- Toast Notification Container --}}
            <div id="toastContainer" class="fixed top-20 right-4 z-50 space-y-2 pointer-events-none"></div>

            {{-- Same Summary Cards as original --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- ... keep original summary cards ... --}}
                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl p-5 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 tracking-tight">{{ number_format($upcomingWorkoutsCount ?? 0) }}</div>
                    <div class="text-xs font-medium text-gray-500 mt-1">Upcoming Workouts</div>
                </div>

                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl p-5 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-cyan-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 tracking-tight">{{ number_format($upcomingClassesCount ?? 0) }}</div>
                    <div class="text-xs font-medium text-gray-500 mt-1">Upcoming Classes</div>
                </div>

                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl p-5 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{{ $completedGoalsCount ?? 0 }} done</span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 tracking-tight">{{ number_format($activeGoalsCount ?? 0) }}</div>
                    <div class="text-xs font-medium text-gray-500 mt-1">Active Goals</div>
                </div>

                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl p-5 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2c-.5 0-1 .4-1.1.9L9.3 8.5C8 8 6.8 6.6 6.2 5c-.3-.6-1-.9-1.6-.6-.5.3-.8.9-.6 1.4.9 2.6 2.9 4.6 5.4 5.4C8.5 12.6 8 14.2 8 16c0 2.2 1.8 4 4 4s4-1.8 4-4c0-1.8-.5-3.4-1.4-4.8 2.5-.8 4.5-2.8 5.4-5.4.2-.5-.1-1.1-.6-1.4-.6-.3-1.3 0-1.6.6-.6 1.6-1.8 3-3.1 3.5L13.1 2.9C13 2.4 12.5 2 12 2z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Best {{ $bestStreak ?? 0 }}</span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 tracking-tight">{{ number_format($currentStreak ?? 0) }}</div>
                    <div class="text-xs font-medium text-gray-500 mt-1">Day Streak</div>
                </div>
            </div>

            {{-- Membership Card (same) --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-2xl p-5 shadow-lg text-white">
                <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10 blur-xl"></div>
                <div class="pointer-events-none absolute -left-6 -bottom-6 h-32 w-32 rounded-full bg-white/5 blur-2xl"></div>
                <div class="relative flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/15 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-indigo-200">Current Plan</div>
                            <div class="text-xl font-bold tracking-tight">{{ $subscription?->plan_name ?? 'No Active Plan' }}</div>
                        </div>
                    </div>
                    @if($subscription)
                        @php
                            $daysRemaining = (int)max(0, \Carbon\Carbon::now()->diffInDays($subscription->end_date, false));
                            $totalDays = (int)\Carbon\Carbon::parse($subscription->start_date)->diffInDays($subscription->end_date);
                            $daysUsed = (int)max(0, $totalDays - $daysRemaining);
                            $progressPercent = $totalDays > 0 ? min(100, max(0, ($daysUsed / $totalDays) * 100)) : 0;
                        @endphp
                        <div class="flex-1 max-w-md">
                            <div class="text-xs font-medium text-indigo-200 mb-1">{{ $daysRemaining }} days remaining</div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-white/20">
                                <div class="h-full rounded-full bg-white transition-all duration-700" style="width: {{ $progressPercent }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-indigo-200 font-medium mt-1">
                                <span>Day {{ $daysUsed }}</span>
                                <span>{{ round($progressPercent) }}% used</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-xs font-semibold text-white bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">Active</span>
                        </div>
                    @else
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-semibold text-white bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">No Subscription</span>
                            <a href="{{ route('plans.index') }}" class="text-xs font-semibold text-white underline hover:no-underline">View Plans →</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Action Row --}}
            <div class="flex flex-wrap items-center gap-3">
                <button onclick="openScheduleWorkoutModal()"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl inline-flex items-center shadow-lg shadow-indigo-200 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Schedule Workout
                </button>

                @if($checkedInToday ?? false)
                    <button disabled class="px-5 py-2.5 bg-emerald-50 text-emerald-700 text-sm font-semibold rounded-xl inline-flex items-center border border-emerald-200 cursor-not-allowed">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Checked In Today
                    </button>
                @else
                    <button id="checkInBtn" onclick="checkIn()"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl inline-flex items-center shadow-lg shadow-emerald-200 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                        Check In
                    </button>
                @endif

                <button onclick="openLogWeightModal()"
                    class="px-5 py-2.5 bg-white/85 backdrop-blur-md border border-white/40 hover:bg-white text-indigo-700 text-sm font-semibold rounded-xl inline-flex items-center shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    Log Weight
                </button>

                <button onclick="openSelectTrainerModal()"
                    class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-xl inline-flex items-center shadow-lg shadow-purple-200 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Find Trainer
                </button>
            </div>

            {{-- Main Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left Column --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Today's Workout --}}
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-sm tracking-tight">Today's Workout</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ now()->format('l, F j') }}</p>
                                </div>
                            </div>
                            <button onclick="openScheduleWorkoutModal()"
                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                Schedule
                            </button>
                        </div>
                        <div class="p-5">
                            @if(isset($todayWorkout) && $todayWorkout && ($todayWorkout->exercises->count() ?? 0) > 0)
                                <div class="space-y-5" id="todayWorkoutContainer">
                                    <div class="flex items-start justify-between gap-4">
                                        <div><h4 class="text-lg font-bold text-gray-900 tracking-tight">{{ $todayWorkout->title }}</h4></div>
                                        <div class="flex shrink-0 gap-2">
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full">{{ $todayWorkout->duration_minutes ?? 45 }} min</span>
                                            @if($todayWorkout->calories_burn)
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full">🔥 {{ number_format($todayWorkout->calories_burn) }} kcal</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="space-y-2 max-h-80 overflow-y-auto pr-1 custom-scrollbar">
                                        @foreach($todayWorkout->exercises as $index => $exercise)
                                            <div class="flex items-center gap-4 p-3.5 bg-gray-50/80 border border-gray-100 rounded-xl hover:bg-indigo-50/40 hover:border-indigo-200 transition-all duration-150 exercise-row {{ ($exercise->pivot->completed ?? false) ? 'opacity-60' : '' }}"
                                                 data-exercise-id="{{ $exercise->pivot->id ?? $exercise->id }}">
                                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-xs font-bold text-gray-500 shadow-sm">{{ $index + 1 }}</span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-sm font-semibold text-gray-800 {{ ($exercise->pivot->completed ?? false) ? 'line-through text-gray-400' : '' }}">{{ $exercise->name }}</p>
                                                    <p class="text-xs text-gray-400 mt-0.5">
                                                        {{ $exercise->pivot->sets ?? 3 }} sets × {{ $exercise->pivot->reps ?? 12 }} reps
                                                        @if($exercise->pivot->weight_kg ?? false) • {{ $exercise->pivot->weight_kg }} kg @endif
                                                    </p>
                                                </div>
                                                <input
                                                    type="checkbox"
                                                    onchange="completeExercise({{ $exercise->pivot->id ?? $exercise->id }}, this)"
                                                    {{ ($exercise->pivot->completed ?? false) ? 'checked disabled' : '' }}
                                                    class="h-5 w-5 rounded border-gray-300 text-indigo-600 accent-indigo-600 cursor-pointer disabled:cursor-not-allowed">
                                            </div>
                                        @endforeach
                                    </div>
                                    @if(($todayWorkout->status ?? 'scheduled') !== 'completed')
                                        <button id="completeWorkoutBtn" onclick="completeWorkout({{ $todayWorkout->id }})"
                                            class="w-full px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-200 hover:shadow-xl transition-all duration-200 active:scale-[0.99]">
                                            Mark Workout Complete
                                        </button>
                                    @else
                                        <div class="flex items-center justify-center gap-2 py-3 bg-emerald-50 text-emerald-700 text-sm font-semibold rounded-xl border border-emerald-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            Workout Completed
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="flex flex-col items-center py-12 text-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25"/></svg>
                                    </div>
                                    <p class="mt-4 text-sm font-semibold text-gray-700">No workout scheduled today</p>
                                    <p class="mt-1 text-xs text-gray-400">Schedule one to get started</p>
                                    <button onclick="openScheduleWorkoutModal()"
                                        class="mt-5 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl inline-flex items-center shadow-lg shadow-indigo-200 hover:shadow-xl transition-all duration-200">
                                        Schedule a Workout
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Weight Progress Chart --}}
                    <div class="bg-white/90 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                        <div class="flex flex-col gap-4 px-5 py-4 border-b border-gray-100 sm:flex-row sm:items-center sm:justify-between bg-gradient-to-r from-indigo-50/50 to-purple-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-sm tracking-tight">Weight Progress Chart</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Your fitness journey visualized</p>
                                </div>
                            </div>
                            <div class="flex gap-1.5" id="chartPillContainer">
                                @foreach(['7' => '7D', '30' => '30D', '90' => '90D', 'all' => 'All'] as $val => $label)
                                    <button type="button"
                                        class="chart-pill text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-200 {{ $val === '30' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' : 'bg-white/80 text-gray-600 hover:bg-gray-100' }}"
                                        data-days="{{ $val }}"
                                        onclick="updateChart('{{ $val }}')">{{ $label }}</button>
                                @endforeach
                            </div>
                        </div>
                        <div class="p-5 bg-gradient-to-b from-white/50 to-transparent">
                            @if(isset($progressLabels) && count($progressLabels) > 0 && isset($progressValues) && count($progressValues) > 0)
                                <div id="chartSkeleton" class="skeleton h-72 w-full rounded-xl"></div>
                                <canvas id="progressChart" class="hidden" height="280"></canvas>
                            @else
                                <div class="flex flex-col items-center py-16 text-center">
                                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center shadow-inner">
                                        <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 17l6-6 4 4 8-8"/></svg>
                                    </div>
                                    <p class="mt-5 text-sm font-semibold text-gray-700">No weight data yet</p>
                                    <p class="text-xs text-gray-400 mt-1">Start tracking your progress today</p>
                                    <button onclick="openLogWeightModal()"
                                        class="mt-5 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-semibold rounded-xl inline-flex items-center shadow-lg shadow-indigo-200 hover:shadow-xl transition-all duration-200">
                                        Log Your First Weight
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="space-y-6">

                    {{-- Quick Actions --}}
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100/50">
                            <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">⚡ Quick Actions</h3>
                        </div>
                        <div class="p-4 grid grid-cols-2 gap-2.5">
                            <button onclick="openLogWeightModal()"
                                class="flex flex-col items-center gap-1.5 p-3 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-xl border border-blue-100 transition-all duration-200 active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                Log Weight
                            </button>
                            <button onclick="openSetGoalModal()"
                                class="flex flex-col items-center gap-1.5 p-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-xl border border-emerald-100 transition-all duration-200 active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                Set Goal
                            </button>
                            <button onclick="openLogNutritionModal()"
                                class="flex flex-col items-center gap-1.5 p-3 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-semibold rounded-xl border border-amber-100 transition-all duration-200 active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Nutrition
                            </button>
                            <a href="{{ route('member.workouts.history') }}"
                                class="flex flex-col items-center gap-1.5 p-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-xl border border-indigo-100 transition-all duration-200 active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                All Workouts
                            </a>
                        </div>
                    </div>

                    {{-- Active Goals --}}
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50/50 to-cyan-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-sm tracking-tight">Active Goals</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Your path to mastery</p>
                                </div>
                            </div>
                            <button onclick="openSetGoalModal()"
                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition-colors">+ Add</button>
                        </div>
                        <div class="p-5">
                            @if(isset($goals) && $goals->count() > 0)
                                <div class="space-y-4" id="goalsContainer">
                                    @foreach($goals as $goal)
                                        @php $pct = $goal->current_value && $goal->target_value ? min(100, round(($goal->current_value / $goal->target_value) * 100)) : 0; @endphp
                                        <div class="flex items-center gap-3">
                                            <div class="relative shrink-0">
                                                <svg viewBox="0 0 36 36" class="h-14 w-14 -rotate-90">
                                                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                                                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#4f46e5" stroke-width="3"
                                                        stroke-dasharray="{{ $pct }} 100" stroke-linecap="round"/>
                                                </svg>
                                                <span class="absolute inset-0 flex items-center justify-center text-xs font-bold text-gray-700">{{ round($pct) }}%</span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-semibold text-gray-800">{{ $goal->title }}</p>
                                                <p class="mt-0.5 text-xs text-gray-400">{{ number_format($goal->current_value, 1) }} / {{ number_format($goal->target_value, 1) }} {{ $goal->unit ?? 'units' }}</p>
                                                @if($goal->target_date)
                                                    <span class="inline-block mt-1 text-[10px] font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Due {{ \Carbon\Carbon::parse($goal->target_date)->format('M d') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center py-8 text-center">
                                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <p class="mt-3 text-sm font-semibold text-gray-700">No goals yet</p>
                                    <button onclick="openSetGoalModal()"
                                        class="mt-3 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-200 transition-all duration-200">
                                        Set First Goal
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Today's Nutrition --}}
                    @php
                        $nutritionCalories = isset($todayNutrition) ? ($todayNutrition->calories ?? 0) : 0;
                        $nutritionProtein  = isset($todayNutrition) ? ($todayNutrition->protein_grams ?? 0) : 0;
                        $nutritionCarbs    = isset($todayNutrition) ? ($todayNutrition->carbs_grams ?? 0) : 0;
                        $nutritionFat      = isset($todayNutrition) ? ($todayNutrition->fat_grams ?? 0) : 0;
                        $calTarget = $user_nutrition_targets['calories'] ?? 2500;
                        $proteinTarget = $user_nutrition_targets['protein'] ?? 150;
                        $carbsTarget   = $user_nutrition_targets['carbs'] ?? 300;
                        $fatTarget     = $user_nutrition_targets['fat'] ?? 80;
                    @endphp
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50/50 to-orange-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-sm tracking-tight">Today's Nutrition</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Fueling your growth</p>
                                </div>
                            </div>
                            <button onclick="openLogNutritionModal()"
                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition-colors">
                                Log Meal
                            </button>
                        </div>
                        <div class="p-5" id="nutritionPanel">
                            <div class="flex items-center gap-5">
                                <div class="relative shrink-0">
                                    <svg viewBox="0 0 36 36" class="h-20 w-20 -rotate-90">
                                        <circle cx="18" cy="18" r="14" fill="none" stroke="#f3f4f6" stroke-width="4"/>
                                        <circle cx="18" cy="18" r="14" fill="none"
                                            stroke="{{ $nutritionCalories > $calTarget ? '#ef4444' : '#f59e0b' }}"
                                            stroke-width="4"
                                            stroke-dasharray="{{ min(round(($nutritionCalories / $calTarget) * 100), 100) }} 100"
                                            stroke-linecap="round"/>
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-sm font-bold text-gray-800" id="calDisplay">{{ number_format($nutritionCalories) }}</span>
                                        <span class="text-[9px] font-medium text-gray-400">kcal</span>
                                    </div>
                                </div>
                                <div class="flex-1 space-y-2.5">
                                    <div>
                                        <div class="mb-1 flex items-center justify-between text-xs">
                                            <span class="font-semibold text-gray-700">Protein</span>
                                            <span class="text-gray-400 font-medium" id="proteinDisplay">{{ number_format($nutritionProtein) }}/{{ $proteinTarget }}g</span>
                                        </div>
                                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                                            <div class="h-full rounded-full transition-all duration-500" id="proteinBar"
                                                style="width:{{ min(round(($nutritionProtein / $proteinTarget) * 100), 100) }}%; background-color:#4f46e5"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="mb-1 flex items-center justify-between text-xs">
                                            <span class="font-semibold text-gray-700">Carbs</span>
                                            <span class="text-gray-400 font-medium" id="carbsDisplay">{{ number_format($nutritionCarbs) }}/{{ $carbsTarget }}g</span>
                                        </div>
                                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                                            <div class="h-full rounded-full transition-all duration-500" id="carbsBar"
                                                style="width:{{ min(round(($nutritionCarbs / $carbsTarget) * 100), 100) }}%; background-color:#10b981"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="mb-1 flex items-center justify-between text-xs">
                                            <span class="font-semibold text-gray-700">Fat</span>
                                            <span class="text-gray-400 font-medium" id="fatDisplay">{{ number_format($nutritionFat) }}/{{ $fatTarget }}g</span>
                                        </div>
                                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                                            <div class="h-full rounded-full transition-all duration-500" id="fatBar"
                                                style="width:{{ min(round(($nutritionFat / $fatTarget) * 100), 100) }}%; background-color:#f59e0b"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Upcoming Workouts List --}}
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50/50 to-purple-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-sm tracking-tight">Upcoming Workouts</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Your scheduled sessions</p>
                                </div>
                            </div>
                            <button onclick="openScheduleWorkoutModal()"
                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition-colors">+ Schedule</button>
                        </div>
                        <div class="max-h-64 overflow-y-auto divide-y divide-gray-50 custom-scrollbar">
                            @forelse($upcomingWorkouts ?? [] as $workout)
                                <button type="button" onclick="viewWorkoutDetails({{ $workout->id }})"
                                    class="w-full px-5 py-3.5 text-left hover:bg-indigo-50/30 transition-colors duration-150">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-gray-800">{{ $workout->title }}</p>
                                            <p class="mt-0.5 text-xs text-gray-400">
                                                {{ \Carbon\Carbon::parse($workout->date)->format('M d, Y') }}
                                                @if($workout->duration_minutes) • {{ $workout->duration_minutes }} min @endif
                                            </p>
                                        </div>
                                        <span class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full {{ $workout->status === 'scheduled' ? 'text-emerald-700 bg-emerald-50' : 'text-gray-600 bg-gray-100' }}">
                                            {{ ucfirst($workout->status) }}
                                        </span>
                                    </div>
                                </button>
                            @empty
                                <div class="flex flex-col items-center py-10 text-center">
                                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25"/></svg>
                                    </div>
                                    <p class="mt-3 text-sm font-semibold text-gray-700">No upcoming workouts</p>
                                    <button onclick="openScheduleWorkoutModal()"
                                        class="mt-3 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-200 transition-all duration-200">
                                        Schedule One
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- My Trainer Card --}}
                    <div class="bg-white/80 backdrop-blur-xl border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50/50 to-pink-50/50">
                            <div class="w-9 h-9 bg-purple-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm tracking-tight">My Personal Trainer</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Your dedicated fitness coach</p>
                            </div>
                            @if(isset($instructor) && $instructor)
                                <button onclick="openChatModal()"
                                    class="ml-auto bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-xs px-3 py-1.5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                                    💬 Chat
                                </button>
                            @endif
                        </div>
                        <div class="p-5">
                            @if(isset($instructor) && $instructor)
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-xl font-bold shadow-lg">
                                        {{ strtoupper(substr($instructor->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-base">{{ $instructor->name }}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $instructor->email }}</p>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                            <span class="text-xs font-medium text-emerald-600">Online</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col items-center py-4 text-center">
                                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">No trainer assigned yet</p>
                                    <button onclick="openSelectTrainerModal()"
                                        class="mt-3 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all duration-200">
                                        Find a Trainer
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- ============================================================ --}}
    {{-- MODALS (All preserved) --}}
    {{-- ============================================================ --}}

    {{-- Schedule Workout Modal --}}
    <div id="scheduleWorkoutModal"
         class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         onclick="if(event.target===this) closeScheduleWorkoutModal()">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="font-bold text-gray-900">📅 Schedule Workout</h3>
                <button onclick="closeScheduleWorkoutModal()" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="scheduleWorkoutForm" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Workout Type</label>
                    <select name="workout_template_id" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition">
                        <option value="">Select a workout...</option>
                        @foreach($workoutTemplates ?? [] as $template)
                            <option value="{{ $template->id }}">{{ $template->title }} ({{ $template->duration_minutes ?? 45 }} min)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Date</label>
                    <input type="date" name="scheduled_date" required min="{{ now()->format('Y-m-d') }}"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition">
                </div>
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Time (optional)</label>
                    <input type="time" name="scheduled_time"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition">
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeScheduleWorkoutModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition">Cancel</button>
                    <button type="submit" id="scheduleWorkoutSubmitBtn"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition">
                        Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Log Weight Modal --}}
    <div id="logWeightModal"
         class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         onclick="if(event.target===this) closeLogWeightModal()">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50">
                <h3 class="font-bold text-gray-900">⚖️ Log Weight</h3>
                <button onclick="closeLogWeightModal()" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="logWeightForm" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Weight (kg)</label>
                    <input type="number" step="0.1" name="weight_kg" required placeholder="e.g. 75.5" min="20" max="300"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none transition">
                </div>
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Date</label>
                    <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none transition">
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeLogWeightModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition">Cancel</button>
                    <button type="submit" id="logWeightSubmitBtn"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Set Goal Modal --}}
    <div id="setGoalModal"
         class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         onclick="if(event.target===this) closeSetGoalModal()">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                <h3 class="font-bold text-gray-900">🎯 Set New Goal</h3>
                <button onclick="closeSetGoalModal()" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="setGoalForm" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Goal Title</label>
                    <input type="text" name="title" required placeholder="e.g. Lose 5kg"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 outline-none transition">
                </div>
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Goal Type</label>
                    <select name="type" required
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 outline-none transition">
                        <option value="weight_loss">Weight Loss</option>
                        <option value="muscle_gain">Muscle Gain</option>
                        <option value="endurance">Endurance</option>
                        <option value="strength">Strength</option>
                        <option value="workouts">Workouts</option>
                        <option value="attendance">Attendance</option>
                        <option value="nutrition">Nutrition</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Target</label>
                        <input type="number" step="0.1" name="target_value" required placeholder="Value" min="1"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 outline-none transition">
                    </div>
                    <div>
                        <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Unit</label>
                        <select name="unit"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 outline-none transition">
                            <option value="kg">kg</option>
                            <option value="lbs">lbs</option>
                            <option value="km">km</option>
                            <option value="minutes">minutes</option>
                            <option value="workouts">workouts</option>
                            <option value="days">days</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Target Date</label>
                    <input type="date" name="target_date" required min="{{ now()->addDay()->format('Y-m-d') }}"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 outline-none transition">
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeSetGoalModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition">Cancel</button>
                    <button type="submit" id="setGoalSubmitBtn"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition">
                        Set Goal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Log Nutrition Modal --}}
    <div id="logNutritionModal"
         class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         onclick="if(event.target===this) closeLogNutritionModal()">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50">
                <h3 class="font-bold text-gray-900">🍽️ Log Nutrition</h3>
                <button onclick="closeLogNutritionModal()" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="logNutritionForm" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Calories</label>
                    <input type="number" name="calories" required placeholder="e.g. 450" min="0" max="10000"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-200 focus:border-amber-400 outline-none transition">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Protein (g)</label>
                        <input type="number" name="protein_grams" step="0.1" placeholder="0" min="0"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-200 focus:border-amber-400 outline-none transition">
                    </div>
                    <div>
                        <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Carbs (g)</label>
                        <input type="number" name="carbs_grams" step="0.1" placeholder="0" min="0"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-200 focus:border-amber-400 outline-none transition">
                    </div>
                    <div>
                        <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wide">Fat (g)</label>
                        <input type="number" name="fat_grams" step="0.1" placeholder="0" min="0"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-200 focus:border-amber-400 outline-none transition">
                    </div>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeLogNutritionModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition">Cancel</button>
                    <button type="submit" id="logNutritionSubmitBtn"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition">
                        Log Meal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Select Trainer Modal --}}
    <div id="selectTrainerModal"
         class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         onclick="if(event.target===this) closeSelectTrainerModal()">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50">
                <h3 class="font-bold text-gray-900">🏋️ Select Your Personal Trainer</h3>
                <button onclick="closeSelectTrainerModal()" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                @forelse($availableTrainers ?? [] as $trainer)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-purple-200 hover:bg-purple-50/30 transition-all duration-200">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                                <span class="text-lg font-bold text-white">{{ strtoupper(substr($trainer->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $trainer->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $trainer->specialization ?? 'Certified Personal Trainer' }}</p>
                            </div>
                        </div>
                        <button onclick="selectTrainer({{ $trainer->id }}, '{{ addslashes($trainer->name) }}')"
                            class="trainer-select-btn px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                            Select
                        </button>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <p class="text-sm text-gray-500">No trainers available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Workout Details Modal --}}
    <div id="workoutDetailsModal"
         class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         onclick="if(event.target===this) closeWorkoutDetailsModal()">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="font-bold text-gray-900">💪 Workout Details</h3>
                <button onclick="closeWorkoutDetailsModal()" class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="workoutDetailsContent" class="max-h-[28rem] overflow-y-auto p-6 text-sm text-gray-700 custom-scrollbar"></div>
        </div>
    </div>

    {{-- Chat Modal (Fully Functional with Pin/Edit/Delete) --}}
    <div id="chatModal"
         class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4"
         onclick="if(event.target===this) closeChatModal()">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="chatModalContent">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 to-indigo-500"></div>
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center shadow-md">
                        <span class="text-base font-bold text-white">{{ strtoupper(substr(isset($instructor) && $instructor ? $instructor->name : 'T', 0, 1)) }}</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ isset($instructor) && $instructor ? $instructor->name : 'Trainer' }}</h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            <span class="text-xs text-emerald-600 font-medium">Online</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="refreshChatMessages()" class="p-2 rounded-full hover:bg-gray-200 transition" title="Refresh">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                    <button onclick="closeChatModal()" class="p-2 rounded-full hover:bg-gray-200 transition">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="chatMessagesContainer" class="h-96 overflow-y-auto p-4 space-y-3 bg-gray-50 custom-scrollbar">
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto mb-3"></div>
                        <p class="text-sm text-gray-500">Loading messages...</p>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-gray-100 bg-white">
                <div class="flex gap-2">
                    <input type="text" id="chatMessageInput" placeholder="Type your message..."
                        class="flex-1 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-purple-200 focus:border-purple-400 outline-none transition"
                        onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); sendChatMessage(); }">
                    <button onclick="sendChatMessage()" id="sendMessageBtn"
                        class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95">
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Message Modal --}}
    <div id="editMessageModal"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4"
         onclick="if(event.target===this) closeEditModal()">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-indigo-50">
                <h3 class="font-bold text-gray-900">✏️ Edit Message</h3>
                <button onclick="closeEditModal()" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <textarea id="editMessageInput" rows="3"
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-200 focus:border-purple-400 outline-none transition resize-none"></textarea>
                <div class="flex gap-3 mt-4">
                    <button onclick="closeEditModal()"
                        class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition">Cancel</button>
                    <button onclick="confirmEditMessage()" id="confirmEditBtn"
                        class="flex-1 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-gradient-to-r from-gray-900 to-gray-800 border-t border-purple-500/30 mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
                <div class="col-span-2 md:col-span-1 lg:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/Project_Logo.png') }}" alt="Gym Logo" class="h-7 w-auto object-contain ml-1">
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Train smart, stay consistent, and celebrate your growth. We're a community rooted in African strength and unity.
                    </p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.77l-.44 2.89h-2.33v6.987A10 10 0 0022 12z" clip-rule="evenodd"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.715.01 3.67.058 1.036.05 1.745.21 2.37.456.684.276 1.258.74 1.717 1.259.46.52.825 1.094 1.102 1.717.246.625.407 1.334.456 2.37.048.955.058 1.23.058 3.67s-.01 2.715-.058 3.67c-.05.97-.21 1.745-.456 2.37-.276.684-.74 1.258-1.259 1.717-.52.46-1.094.825-1.717 1.102-.625.246-1.334.407-2.37.456-.955.048-1.23.058-3.67.058s-2.715-.01-3.67-.058c-.97-.05-1.745-.21-2.37-.456-.684-.276-1.258-.74-1.717-1.259-.46-.52-.825-1.094-1.102-1.717-.246-.625-.407-1.334-.456-2.37-.048-.955-.058-1.23-.058-3.67s.01-2.715.058-3.67c.05-.97.21-1.745.456-2.37.276-.684.74-1.258 1.259-1.717.46-.52 1.094-.825 1.717-1.102.625-.246 1.334-.407 2.37-.456C9.59 2.01 9.875 2 12.315 2zm0 1.637c-2.35 0-2.6.01-3.535.056-.983.05-1.503.21-1.85.347-.417.164-.78.384-1.095.698-.315.315-.534.678-.698 1.095-.137.347-.297.867-.347 1.85-.046.935-.056 1.185-.056 3.535s.01 2.6.056 3.535c.05.983.21 1.503.347 1.85.164.417.384.78.698 1.095.315.315.678.534 1.095.698.347.137.867.297 1.85.347.935.046 1.185.056 3.535.056s2.6-.01 3.535-.056c.983-.05 1.503-.21 1.85-.347.417-.164.78-.384 1.095-.698.315-.315.534-.678.698-1.095.137-.347.297-.867.347-1.85.046-.935.056-1.185.056-3.535s-.01-2.6-.056-3.535c-.05-.983-.21-1.503-.347-1.85-.164-.417-.384-.78-.698-1.095-.315-.315-.678-.534-1.095-.698-.347-.137-.867-.297-1.85-.347-.935-.046-1.185-.056-3.535-.056zM12.315 5.564c-3.714 0-6.75 3.036-6.75 6.75s3.036 6.75 6.75 6.75 6.75-3.036 6.75-6.75-3.036-6.75-6.75-6.75zm0 11.235c-2.476 0-4.485-2.009-4.485-4.485S9.839 7.828 12.315 7.828s4.485 2.009 4.485 4.485-2.009 4.485-4.485 4.485zm4.991-9.982c-.52 0-.942-.423-.942-.942s.422-.942.942-.942.942.423.942.942-.422.942-.942.942z" clip-rule="evenodd"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd"/></svg>
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
                        <li><a href="{{ route('plans.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Membership Plans</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Popular Classes</h5>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🧘 Yoga</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💪 HIIT</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💃 Zumba</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🥊 Boxing</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🧘 Pilates</a></li>
                    </ul>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <h5 class="text-lg font-semibold text-white mb-4">Get In Touch</h5>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-start"><span class="mr-2 text-purple-400">📍</span><span>Ggaba Road, Kampala, UGANDA</span></li>
                        <li class="flex items-start"><span class="mr-2 text-purple-400">📞</span><span>+256 700 123 456</span></li>
                        <li class="flex items-start"><span class="mr-2 text-purple-400">📧</span><span><a href="mailto:info@mygym.com" class="hover:text-purple-400">info@mygym.com</a></span></li>
                    </ul>
                    <div class="mt-6">
                        <h5 class="text-sm font-semibold text-white mb-2">Support Hours</h5>
                        <p class="text-xs text-gray-400">Mon-Fri: 9AM - 6PM</p>
                        <p class="text-xs text-gray-400">Sat: 10AM - 4PM</p>
                        <p class="text-xs text-gray-400">Sun: Closed</p>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-purple-500/30">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex gap-6">
                        <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">About Us</a>
                        <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">Terms of Service</a>
                        <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">Privacy Policy</a>
                        <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">Cookie Policy</a>
                    </div>
                    <p class="text-sm text-gray-500">&copy; {{ date('Y') }} MyGym. All rights reserved. Powered by Passion.</p>
                </div>
            </div>
        </div>
    </footer>

{{-- ============================================================ --}}
{{-- STYLES --}}
{{-- ============================================================ --}}
<style>
    @keyframes shimmer {
        0%  { background-position: -200% 0; }
        100%{ background-position:  200% 0; }
    }
    .skeleton {
        background: linear-gradient(90deg, #f3f4f6 25%, #e9eaeb 50%, #f3f4f6 75%);
        background-size: 200% 100%;
        animation: shimmer 1.6s infinite;
        border-radius: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to   { transform: translateX(0);    opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0);    opacity: 1; }
        to   { transform: translateX(100%); opacity: 0; }
    }
    .toast-slide-in  { animation: slideIn  0.3s ease-out forwards; }
    .toast-slide-out { animation: slideOut 0.3s ease-in  forwards; }

    /* Message hover actions */
    .msg-bubble-wrapper { position: relative; }
    .msg-actions {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        gap: 2px;
        background: white;
        border-radius: 10px;
        padding: 3px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.15s ease;
        z-index: 10;
        white-space: nowrap;
    }
    .msg-bubble-wrapper:hover .msg-actions { opacity: 1; pointer-events: all; }
    .msg-actions-left  { right: calc(100% + 8px); }
    .msg-actions-right { left: calc(100% + 8px); }

    .exercise-row.completed { opacity: 0.5; }
    .exercise-row.completed p:first-child { text-decoration: line-through; color: #9ca3af; }
</style>

{{-- ============================================================ --}}
{{-- SCRIPTS (Fully Functional with Pin/Edit/Delete) --}}
{{-- ============================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    'use strict';

    /* ─── CSRF & Routes ─── */
    const CSRF = '{{ csrf_token() }}';
    const AUTH_ID = {{ Auth::id() }};
    const TRAINER_ID = {{ isset($instructor) && $instructor ? $instructor->id : 0 }};

    const ROUTES = {
        scheduleWorkout  : '{{ route("member.workouts.schedule") }}',
        logWeight        : '{{ route("member.progress.weight") }}',
        setGoal          : '{{ route("member.goals.store") }}',
        logNutrition     : '{{ route("member.nutrition.store") }}',
        checkIn          : '{{ route("member.attendance.check-in") }}',
        selectTrainer    : '/member/select-trainer',
        messages: {
            conversation : (tid) => `/member/messages/conversation/${tid}`,
            send         : '/member/messages/send',
            update       : (id)  => `/member/messages/${id}`,
            delete       : (id)  => `/member/messages/${id}`,
            pin          : (id)  => `/member/messages/${id}/pin`,
        },
        workout: {
            complete : (id) => `/member/workouts/${id}/complete`,
            details  : (id) => `/member/workouts/${id}`,
        },
        exercise: {
            complete : (id) => `/member/workout-exercises/${id}/complete`,
        }
    };

    /* ─── Chart data ─── */
    const ALL_LABELS = @json($progressLabels ?? []);
    const ALL_VALUES = @json($progressValues ?? []);
    let progressChart = null;
    let activeDays    = '30';

    /* ─── State ─── */
    let currentEditingMessageId = null;
    let isSendingMessage        = false;
    let chatPollInterval        = null;

    /* ================================================================
       TOAST (Improved)
    ================================================================ */
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const colors = {
            success : 'bg-emerald-500',
            error   : 'bg-red-500',
            info    : 'bg-blue-500',
            warning : 'bg-amber-500',
        };

        const toast = document.createElement('div');
        toast.className = `${colors[type] || colors.success} text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 toast-slide-in pointer-events-auto max-w-sm`;
        toast.innerHTML = `
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="${type === 'error' ? 'M6 18L18 6M6 6l12 12' : type === 'warning' ? 'M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z' : 'M5 13l4 4L19 7'}"/>
            </svg>
            <span class="text-sm font-medium flex-1">${escapeHtml(message)}</span>
            <button class="text-white/80 hover:text-white flex-shrink-0" onclick="this.closest('div').remove()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('toast-slide-in');
            toast.classList.add('toast-slide-out');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    /* ================================================================
       MODAL HELPERS
    ================================================================ */
    function openModal(id) {
        const m = document.getElementById(id);
        if (!m) return;
        m.classList.remove('hidden');
        m.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        const m = document.getElementById(id);
        if (!m) return;
        m.classList.add('hidden');
        m.classList.remove('flex');
        document.body.style.overflow = '';
    }

    window.openScheduleWorkoutModal  = () => openModal('scheduleWorkoutModal');
    window.closeScheduleWorkoutModal = () => { closeModal('scheduleWorkoutModal'); document.getElementById('scheduleWorkoutForm')?.reset(); };
    window.openLogWeightModal        = () => openModal('logWeightModal');
    window.closeLogWeightModal       = () => { closeModal('logWeightModal'); document.getElementById('logWeightForm')?.reset(); };
    window.openSetGoalModal          = () => openModal('setGoalModal');
    window.closeSetGoalModal         = () => { closeModal('setGoalModal'); document.getElementById('setGoalForm')?.reset(); };
    window.openLogNutritionModal     = () => openModal('logNutritionModal');
    window.closeLogNutritionModal    = () => { closeModal('logNutritionModal'); document.getElementById('logNutritionForm')?.reset(); };
    window.openSelectTrainerModal    = () => openModal('selectTrainerModal');
    window.closeSelectTrainerModal   = () => closeModal('selectTrainerModal');
    window.openWorkoutDetailsModal   = () => openModal('workoutDetailsModal');
    window.closeWorkoutDetailsModal  = () => closeModal('workoutDetailsModal');

    /* ================================================================
       CHAT MODAL (Fully Functional)
    ================================================================ */
    window.openChatModal = function () {
        const modal   = document.getElementById('chatModal');
        const content = document.getElementById('chatModalContent');
        if (!modal || !content) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        });
        loadChatMessages();
        chatPollInterval = setInterval(loadChatMessages, 15000);
    };

    window.closeChatModal = function () {
        const modal   = document.getElementById('chatModal');
        const content = document.getElementById('chatModalContent');
        if (!modal || !content) return;
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        clearInterval(chatPollInterval);
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 200);
    };

    window.refreshChatMessages = function () {
        loadChatMessages();
        showToast('Messages refreshed', 'info');
    };

    async function loadChatMessages(silent = false) {
        const container = document.getElementById('chatMessagesContainer');
        if (!container) return;

        if (!TRAINER_ID) {
            container.innerHTML = `
                <div class="flex h-full items-center justify-center">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <p class="text-sm text-gray-500">No trainer assigned yet</p>
                        <button onclick="closeChatModal(); openSelectTrainerModal();"
                            class="mt-3 px-4 py-2 bg-purple-600 text-white text-xs rounded-xl hover:bg-purple-700 transition">
                            Find a Trainer
                        </button>
                    </div>
                </div>`;
            return;
        }

        if (!silent) {
            container.innerHTML = `
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto mb-3"></div>
                        <p class="text-sm text-gray-500">Loading messages...</p>
                    </div>
                </div>`;
        }

        try {
            const res  = await apiFetch(ROUTES.messages.conversation(TRAINER_ID));
            const data = await res.json();

            if (!data.success) throw new Error(data.error || 'Failed to load messages');

            const messages = data.messages || [];
            const scrolledToBottom = container.scrollHeight - container.clientHeight <= container.scrollTop + 50;

            if (messages.length === 0) {
                container.innerHTML = `
                    <div class="flex h-full items-center justify-center">
                        <div class="text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="text-sm text-gray-500 font-semibold">No messages yet</p>
                            <p class="text-xs text-gray-400 mt-1">Send a message to start the conversation</p>
                        </div>
                    </div>`;
            } else {
                container.innerHTML = messages.map(msg => renderMessage(msg)).join('');
            }

            if (scrolledToBottom || !silent) {
                container.scrollTop = container.scrollHeight;
            }
        } catch (err) {
            if (!silent) {
                container.innerHTML = `
                    <div class="flex h-full items-center justify-center">
                        <div class="text-center">
                            <p class="text-sm text-red-500 font-semibold">Error loading messages</p>
                            <p class="text-xs text-gray-400 mt-1">${escapeHtml(err.message)}</p>
                            <button onclick="loadChatMessages()"
                                class="mt-3 px-4 py-2 bg-purple-600 text-white text-xs rounded-xl hover:bg-purple-700 transition">Retry</button>
                        </div>
                    </div>`;
            }
        }
    }

    function renderMessage(msg) {
        const isOwn = msg.sender_id == AUTH_ID;
        const text  = escapeHtml(msg.message || '');
        const time  = formatDate(msg.created_at);

        const actions = isOwn ? `
            <div class="msg-actions ${isOwn ? 'msg-actions-left' : 'msg-actions-right'}">
                <button onclick="editMessage(${msg.id}, ${JSON.stringify(msg.message || '')})"
                    title="Edit"
                    class="p-1.5 rounded-lg hover:bg-blue-50 text-gray-500 hover:text-blue-600 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <button onclick="pinMessage(${msg.id}, ${msg.is_pinned ? 1 : 0})"
                    title="${msg.is_pinned ? 'Unpin' : 'Pin'}"
                    class="p-1.5 rounded-lg hover:bg-amber-50 text-gray-500 hover:text-amber-600 transition">
                    <svg class="w-3.5 h-3.5" fill="${msg.is_pinned ? 'currentColor' : 'none'}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                </button>
                <button onclick="deleteMessage(${msg.id})"
                    title="Delete"
                    class="p-1.5 rounded-lg hover:bg-red-50 text-gray-500 hover:text-red-600 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>` : '';

        return `
            <div class="flex ${isOwn ? 'justify-end' : 'justify-start'} mb-2">
                <div class="msg-bubble-wrapper max-w-[75%]">
                    ${actions}
                    <div class="rounded-2xl px-4 py-2.5 ${isOwn ? 'rounded-br-sm bg-gradient-to-r from-purple-600 to-indigo-600 text-white' : 'rounded-bl-sm bg-white border border-gray-200 text-gray-700 shadow-sm'} ${msg.is_pinned ? 'ring-2 ring-amber-400' : ''}">
                        ${msg.is_pinned ? '<div class="flex items-center gap-1 mb-1"><span class="text-[10px] font-semibold text-amber-400">📌 Pinned</span></div>' : ''}
                        <p class="text-sm break-words whitespace-pre-wrap">${text}</p>
                        <p class="text-[10px] mt-1 ${isOwn ? 'text-purple-200' : 'text-gray-400'}">
                            ${time}${msg.is_edited ? ' <em>(edited)</em>' : ''}
                        </p>
                    </div>
                </div>
            </div>`;
    }

    window.sendChatMessage = async function () {
        if (isSendingMessage) return;
        const input = document.getElementById('chatMessageInput');
        const message = input?.value.trim();
        if (!message) { showToast('Please enter a message', 'warning'); return; }
        if (!TRAINER_ID) { showToast('No trainer assigned', 'error'); return; }

        isSendingMessage = true;
        const btn = document.getElementById('sendMessageBtn');
        const orig = btn?.innerHTML;
        if (btn) { btn.disabled = true; btn.innerHTML = '<div class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mx-auto"></div>'; }
        if (input) input.disabled = true;

        try {
            const res  = await apiFetch(ROUTES.messages.send, 'POST', { receiver_id: TRAINER_ID, message });
            const data = await res.json();
            if (data.success) {
                input.value = '';
                await loadChatMessages(true);
                const container = document.getElementById('chatMessagesContainer');
                if (container) container.scrollTop = container.scrollHeight;
            } else {
                showToast(data.error || 'Failed to send message', 'error');
            }
        } catch (e) {
            showToast('Network error. Please try again.', 'error');
        } finally {
            isSendingMessage = false;
            if (btn)   { btn.disabled = false; btn.innerHTML = orig; }
            if (input) { input.disabled = false; input.focus(); }
        }
    };

    window.editMessage = function (messageId, currentText) {
        currentEditingMessageId = messageId;
        const inp = document.getElementById('editMessageInput');
        if (inp) inp.value = currentText;
        openModal('editMessageModal');
    };

    window.closeEditModal = function () {
        closeModal('editMessageModal');
        currentEditingMessageId = null;
    };

    window.confirmEditMessage = async function () {
        const newMessage = document.getElementById('editMessageInput')?.value.trim();
        if (!newMessage) { showToast('Message cannot be empty', 'warning'); return; }

        const btn = document.getElementById('confirmEditBtn');
        setButtonLoading(btn, true);
        try {
            const res  = await apiFetch(ROUTES.messages.update(currentEditingMessageId), 'PUT', { message: newMessage });
            const data = await res.json();
            if (data.success) {
                showToast('Message updated', 'success');
                closeEditModal();
                await loadChatMessages(true);
            } else {
                showToast(data.error || 'Failed to update message', 'error');
            }
        } catch (e) {
            showToast('Network error', 'error');
        } finally {
            setButtonLoading(btn, false, 'Save Changes');
        }
    };

    window.deleteMessage = async function (messageId) {
        if (!confirm('Delete this message? This action cannot be undone.')) return;
        try {
            const res  = await apiFetch(ROUTES.messages.delete(messageId), 'DELETE');
            const data = await res.json();
            if (data.success) {
                showToast('Message deleted', 'success');
                await loadChatMessages(true);
            } else {
                showToast(data.error || 'Failed to delete', 'error');
            }
        } catch (e) {
            showToast('Network error', 'error');
        }
    };

    window.pinMessage = async function (messageId, currentlyPinned) {
        try {
            const res  = await apiFetch(ROUTES.messages.pin(messageId), 'POST');
            const data = await res.json();
            if (data.success) {
                showToast(currentlyPinned ? 'Message unpinned' : 'Message pinned 📌', 'success');
                await loadChatMessages(true);
            } else {
                showToast(data.error || 'Failed to pin', 'error');
            }
        } catch (e) {
            showToast('Network error', 'error');
        }
    };

    /* ================================================================
       CHART
    ================================================================ */
    window.updateChart = function (days) {
        activeDays = days;
        document.querySelectorAll('.chart-pill').forEach(p => {
            const active = p.dataset.days === days;
            p.className = `chart-pill text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-200 ${active ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' : 'bg-white/80 text-gray-600 hover:bg-gray-100'}`;
        });
        if (!progressChart) return;
        const cut = days === 'all' ? 0 : Math.max(0, ALL_LABELS.length - parseInt(days));
        progressChart.data.labels              = ALL_LABELS.slice(cut);
        progressChart.data.datasets[0].data   = ALL_VALUES.slice(cut);
        progressChart.update('active');
    };

    function initChart() {
        const canvas = document.getElementById('progressChart');
        if (!canvas || ALL_LABELS.length === 0) return;

        const skeleton = document.getElementById('chartSkeleton');

        progressChart = new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: ALL_LABELS,
                datasets: [{
                    label: 'Weight (kg)',
                    data: ALL_VALUES,
                    borderColor: '#4f46e5',
                    backgroundColor: function (ctx) {
                        const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 280);
                        g.addColorStop(0, 'rgba(79,70,229,0.25)');
                        g.addColorStop(1, 'rgba(139,92,246,0)');
                        return g;
                    },
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2.5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(79,70,229,0.95)',
                        titleColor: '#c7d2fe',
                        bodyColor: '#fff',
                        cornerRadius: 10,
                        padding: 10,
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.y} kg`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { color: '#6b7280', font: { size: 11 } },
                        title: { display: true, text: 'Weight (kg)', color: '#9ca3af', font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280', font: { size: 11 }, maxTicksLimit: 8 },
                        title: { display: true, text: 'Date', color: '#9ca3af', font: { size: 11 } }
                    }
                }
            }
        });

        if (skeleton) skeleton.remove();
        canvas.classList.remove('hidden');
        updateChart('30');
    }

    /* ================================================================
       CHECK IN
    ================================================================ */
    window.checkIn = async function () {
        const btn = document.getElementById('checkInBtn');
        setButtonLoading(btn, true, '');
        try {
            const res  = await apiFetch(ROUTES.checkIn, 'POST');
            const data = await res.json();
            if (data.success) {
                showToast('Checked in successfully! 💪', 'success');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Checked In`;
                    btn.style.opacity = '0.7';
                }
                setTimeout(() => location.reload(), 1800);
            } else {
                showToast(data.error || 'Already checked in today', 'warning');
                setButtonLoading(btn, false, 'Check In');
            }
        } catch (e) {
            showToast('Network error. Please try again.', 'error');
            setButtonLoading(btn, false, 'Check In');
        }
    };

    /* ================================================================
       WORKOUT ACTIONS
    ================================================================ */
    window.completeWorkout = async function (id) {
        const btn = document.getElementById('completeWorkoutBtn');
        setButtonLoading(btn, true);
        try {
            const res  = await apiFetch(ROUTES.workout.complete(id), 'POST');
            const data = await res.json();
            if (data.success) {
                showToast('Workout completed! 🎉 Great job!', 'success');
                if (btn) {
                    btn.outerHTML = `<div class="flex items-center justify-center gap-2 py-3 bg-emerald-50 text-emerald-700 text-sm font-semibold rounded-xl border border-emerald-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Workout Completed
                    </div>`;
                }
                setTimeout(() => location.reload(), 2000);
            } else {
                showToast(data.error || 'Failed to complete workout', 'error');
                setButtonLoading(btn, false, 'Mark Workout Complete');
            }
        } catch (e) {
            showToast('Network error. Please try again.', 'error');
            setButtonLoading(btn, false, 'Mark Workout Complete');
        }
    };

    window.completeExercise = async function (pivotId, checkbox) {
        checkbox.disabled = true;
        try {
            const res  = await apiFetch(ROUTES.exercise.complete(pivotId), 'POST');
            const data = await res.json();
            if (data.success) {
                checkbox.checked  = true;
                const row = checkbox.closest('.exercise-row');
                if (row) {
                    row.classList.add('opacity-60');
                    const name = row.querySelector('p');
                    if (name) name.classList.add('line-through', 'text-gray-400');
                }
                showToast('Exercise done! 🔥', 'success');
            } else {
                checkbox.checked  = false;
                checkbox.disabled = false;
                showToast(data.error || 'Failed to mark exercise', 'error');
            }
        } catch (e) {
            checkbox.checked  = false;
            checkbox.disabled = false;
            showToast('Network error', 'error');
        }
    };

    window.viewWorkoutDetails = async function (id) {
        document.getElementById('workoutDetailsContent').innerHTML = `
            <div class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>`;
        openWorkoutDetailsModal();
        try {
            const res  = await fetch(ROUTES.workout.details(id), { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (data.success && data.workout) {
                const w = data.workout;
                document.getElementById('workoutDetailsContent').innerHTML = `
                    <div>
                        <h4 class="text-lg font-bold text-gray-900">${escapeHtml(w.title)}</h4>
                        ${w.description ? `<p class="text-sm text-gray-500 mt-1">${escapeHtml(w.description)}</p>` : ''}
                        <div class="flex flex-wrap gap-2 mt-3">
                            ${w.date       ? `<span class="text-xs bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-full font-semibold">📅 ${w.date}</span>` : ''}
                            ${w.duration   ? `<span class="text-xs bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-full font-semibold">⏱️ ${w.duration} min</span>` : ''}
                            ${w.calories_burn ? `<span class="text-xs bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full font-semibold">🔥 ${w.calories_burn} kcal</span>` : ''}
                        </div>
                        ${(w.exercises || []).length > 0 ? `
                        <div class="mt-5">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Exercises</p>
                            <div class="space-y-2">
                                ${(w.exercises).map((ex, i) => `
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white text-xs font-bold text-gray-500 shadow-sm flex-shrink-0">${i + 1}</span>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800">${escapeHtml(ex.name)}</p>
                                            <p class="text-xs text-gray-400">${ex.pivot?.sets || 3} sets × ${ex.pivot?.reps || 12} reps${ex.pivot?.weight_kg ? ` · ${ex.pivot.weight_kg} kg` : ''}</p>
                                        </div>
                                    </div>`).join('')}
                            </div>
                        </div>` : '<p class="mt-4 text-sm text-gray-400">No exercises in this workout.</p>'}
                    </div>`;
            } else {
                throw new Error('Workout not found');
            }
        } catch (e) {
            document.getElementById('workoutDetailsContent').innerHTML = `
                <div class="text-center py-8">
                    <p class="text-sm text-red-500">Failed to load workout details</p>
                    <button onclick="closeWorkoutDetailsModal()" class="mt-3 px-4 py-2 text-xs text-gray-600 hover:text-gray-900">Close</button>
                </div>`;
        }
    };

    /* ================================================================
       FORM SUBMISSIONS
    ================================================================ */
    function bindAjaxForm({ formId, url, submitBtnId, successMsg, onSuccess }) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn  = document.getElementById(submitBtnId) || form.querySelector('button[type="submit"]');
            const orig = btn?.innerHTML;
            setButtonLoading(btn, true);

            try {
                const res  = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: new FormData(form),
                });
                const data = await res.json();

                if (data.success) {
                    showToast(successMsg, 'success');
                    form.reset();
                    if (onSuccess) onSuccess(data);
                    const modal = form.closest('[id$="Modal"]');
                    if (modal) closeModal(modal.id);
                    setTimeout(() => location.reload(), 1800);
                } else {
                    const msg = data.message || data.error || 'Something went wrong';
                    showToast(msg, 'error');
                }
            } catch (err) {
                showToast('Network error. Please try again.', 'error');
            } finally {
                setButtonLoading(btn, false, orig);
            }
        });
    }

    /* ================================================================
       SELECT TRAINER
    ================================================================ */
    window.selectTrainer = async function (tid, name) {
        const btns = document.querySelectorAll('.trainer-select-btn');
        btns.forEach(b => b.disabled = true);

        try {
            const res  = await apiFetch(ROUTES.selectTrainer, 'POST', { trainer_id: tid });
            const data = await res.json();
            if (data.success) {
                showToast(`${name} is now your trainer! 🎉`, 'success');
                closeSelectTrainerModal();
                setTimeout(() => location.reload(), 1800);
            } else {
                showToast(data.error || 'Failed to select trainer', 'error');
                btns.forEach(b => b.disabled = false);
            }
        } catch (e) {
            showToast('Network error. Please try again.', 'error');
            btns.forEach(b => b.disabled = false);
        }
    };

    /* ================================================================
       UTILITIES
    ================================================================ */
    function apiFetch(url, method = 'GET', body = null) {
        const opts = {
            method,
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            }
        };
        if (body && method !== 'GET') {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(body);
        }
        return fetch(url, opts);
    }

    function setButtonLoading(btn, loading, restoreHTML = null) {
        if (!btn) return;
        if (loading) {
            btn._orig    = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<div class="flex items-center justify-center gap-2"><div class="animate-spin h-4 w-4 border-2 border-current border-t-transparent rounded-full"></div><span>Loading...</span></div>';
        } else {
            btn.disabled  = false;
            btn.innerHTML = restoreHTML ?? btn._orig ?? btn.innerHTML;
        }
    }

    function escapeHtml(str) {
        if (typeof str !== 'string') return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        try {
            return new Date(dateStr).toLocaleString('en-US', {
                month: 'short', day: 'numeric',
                hour: 'numeric', minute: '2-digit', hour12: true
            });
        } catch { return dateStr; }
    }

    /* ================================================================
       INIT
    ================================================================ */
    document.addEventListener('DOMContentLoaded', () => {
        bindAjaxForm({
            formId     : 'scheduleWorkoutForm',
            url        : ROUTES.scheduleWorkout,
            submitBtnId: 'scheduleWorkoutSubmitBtn',
            successMsg : '📅 Workout scheduled successfully!',
        });
        bindAjaxForm({
            formId     : 'logWeightForm',
            url        : ROUTES.logWeight,
            submitBtnId: 'logWeightSubmitBtn',
            successMsg : '⚖️ Weight logged!',
        });
        bindAjaxForm({
            formId     : 'setGoalForm',
            url        : ROUTES.setGoal,
            submitBtnId: 'setGoalSubmitBtn',
            successMsg : '🎯 Goal created!',
        });
        bindAjaxForm({
            formId     : 'logNutritionForm',
            url        : ROUTES.logNutrition,
            submitBtnId: 'logNutritionSubmitBtn',
            successMsg : '🍽️ Meal logged!',
        });

        initChart();

        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape') return;
            ['scheduleWorkoutModal','logWeightModal','setGoalModal','logNutritionModal',
             'selectTrainerModal','workoutDetailsModal','chatModal','editMessageModal']
                .forEach(id => {
                    const m = document.getElementById(id);
                    if (m && !m.classList.contains('hidden')) {
                        if (id === 'chatModal') window.closeChatModal();
                        else if (id === 'editMessageModal') window.closeEditModal();
                        else closeModal(id);
                    }
                });
        });
    });

})();
</script>
</x-app-layout>
