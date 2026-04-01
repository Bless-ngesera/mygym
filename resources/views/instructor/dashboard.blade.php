<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                <span>💪</span> {{ __('Instructor Dashboard') }}
            </h2>
            <span class="text-sm text-gray-500">
                {{ now()->format('l, F j, Y') }}
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen relative"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
               background-size: cover;
               background-position: center;
               background-attachment: fixed;">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/30 to-white/70 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10 relative z-10">

            {{-- Hero / Profile Header --}}
            <div class="bg-white/80 backdrop-blur-sm shadow-lg sm:rounded-2xl p-6 border border-gray-100 flex flex-col md:flex-row items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg ring-4 ring-white/60">
                        {{-- initials --}}
                        {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-gray-900">Welcome back, Coach {{ Auth::user()->name }} 👋🏾</h1>
                        <p class="text-sm text-gray-600 mt-1">Keep inspiring — your community looks up to you.</p>
                        <div class="mt-3 flex items-center gap-3">
                            <span class="inline-flex items-center gap-2 bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path></svg>
                                Certified Coach
                            </span>
                            <span class="text-sm text-gray-500">Member since {{ Auth::user()->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="ml-auto grid grid-cols-3 gap-4 w-full md:w-auto mt-4 md:mt-0">
                    <div class="text-center p-3 bg-white rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Clients</p>
                        <p class="text-lg font-bold text-gray-900">{{ $totalClients ?? 0 }}</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Classes</p>
                        <p class="text-lg font-bold text-gray-900">{{ $totalClasses ?? 0 }}</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Earnings</p>
                        <p class="text-lg font-bold text-gray-900">UGX {{ number_format($totalEarnings ?? 0, 0) }}</p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('schedule.create') }}" class="px-5 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transform transition">
                    🗓️ Schedule a Class
                </a>
                <a href="{{ route('instructor.classes') }}" class="px-5 py-3 bg-white text-gray-800 font-semibold rounded-full shadow border border-gray-100 hover:shadow-md transition">
                    👥 My Classes
                </a>
                <a href="{{ route('instructor.upcoming') }}" class="px-5 py-3 bg-green-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transition">
                    📊 Upcoming Classes
                </a>
                <a href="{{ route('instructor.earnings') }}" class="px-5 py-3 bg-yellow-500 text-white font-semibold rounded-full shadow hover:scale-[1.02] transition">
                    💵 My Earnings
                </a>
            </div>

            {{-- Stats Overview (dynamic) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-purple-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-purple-700">Total Classes</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ $totalClasses ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">Classes you've created</p>
                        </div>
                        <div class="bg-purple-50 text-purple-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-green-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-green-700">Upcoming Classes</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ $upcomingClasses ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">Scheduled for future dates</p>
                        </div>
                        <div class="bg-green-50 text-green-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-yellow-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-yellow-700">Total Students</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ $totalStudents ?? 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">Across all your classes</p>
                        </div>
                        <div class="bg-yellow-50 text-yellow-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Motivation --}}
            <div class="bg-gradient-to-r from-purple-600/80 to-indigo-700/80 text-white rounded-2xl shadow-lg p-8 text-center">
                <h2 class="text-2xl font-bold mb-2">🔥 Daily Motivation</h2>
                <p class="text-lg italic mb-4">“Discipline is the bridge between goals and achievements.”</p>
                <p class="text-sm text-purple-100">– African Fitness Wisdom</p>
            </div>

            {{-- Community Section & Recent Classes --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-md rounded-2xl border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">🌿 Community & Wellness Corner</h2>
                    <p class="text-gray-600 mb-4">In Africa, we rise by lifting others. Keep your clients motivated — wellness is not just physical, it’s mental and communal.</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 text-left">
                        <li>🏆 Highlight your top-performing client this week</li>
                        <li>🤝 Encourage group workout sessions</li>
                        <li>🥗 Share nutrition tips with your community</li>
                        <li>💬 Collect feedback to improve your coaching</li>
                    </ul>
                </div>

                <div class="bg-white shadow-md rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">📆 Your Recent Classes</h3>
                        <a href="{{ route('instructor.classes') }}" class="text-sm text-indigo-600 font-semibold hover:underline">See all</a>
                    </div>

                    <ul class="space-y-4">
                        @forelse($recentClasses ?? [] as $class)
                        <li class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                {{ strtoupper(substr($class->classType->name ?? 'C',0,2)) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $class->classType->name ?? 'Class' }}</p>
                                        <p class="text-xs text-gray-500">{{ $class->date_time->format('D, M d • h:i A') }}</p>
                                    </div>
                                    <span class="text-xs font-semibold {{ $class->date_time->isPast() ? 'text-gray-500' : 'text-green-600' }}">
                                        {{ $class->date_time->isPast() ? 'Completed' : 'Upcoming' }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="text-center text-gray-500 py-4">No classes scheduled yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>

        {{-- Floating Action Button --}}
        <a href="{{ route('schedule.create') }}" class="fixed bottom-8 right-8 bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-full shadow-xl ring-4 ring-white/50 transform hover:scale-105 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </a>
    </div>
</x-app-layout>
