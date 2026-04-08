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
                        <div class="mt-3 flex flex-wrap items-center gap-3">
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
                        <p class="text-xs text-gray-500">Unique Clients</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($totalUniqueClients ?? 0) }}</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Total Classes</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($totalClasses ?? 0) }}</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Earnings</p>
                        <p class="text-lg font-bold text-gray-900">UGX {{ number_format($totalEarnings ?? 0, 0) }}</p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('instructor.create') }}" class="px-5 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transform transition">
                    🗓️ Schedule a Class
                </a>
                <a href="{{ route('instructor.classes') }}" class="px-5 py-3 bg-white text-gray-800 font-semibold rounded-full shadow border border-gray-100 hover:shadow-md transition">
                    👥 My Classes
                </a>
                <a href="{{ route('instructor.upcoming') }}" class="px-5 py-3 bg-green-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transition">
                    📊 Upcoming Classes
                </a>
                <a href="{{ route('instructor.calendar') }}" class="px-5 py-3 bg-blue-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transition">
                    📅 Calendar View
                </a>
                <a href="{{ route('instructor.earnings.index') }}" class="px-5 py-3 bg-yellow-500 text-white font-semibold rounded-full shadow hover:scale-[1.02] transition">
                    💵 My Earnings
                </a>
            </div>

            {{-- Stats Overview (dynamic) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-purple-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-purple-700">Unique Clients</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($totalUniqueClients ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Distinct members who booked you</p>
                        </div>
                        <div class="bg-purple-50 text-purple-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-indigo-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-indigo-700">Total Bookings</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($totalBookings ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">All class reservations</p>
                        </div>
                        <div class="bg-indigo-50 text-indigo-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-green-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-green-700">Total Classes</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($totalClasses ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Classes you've created</p>
                        </div>
                        <div class="bg-green-50 text-green-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-yellow-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-yellow-700">Upcoming Classes</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($upcomingClasses ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Scheduled for future dates</p>
                        </div>
                        <div class="bg-yellow-50 text-yellow-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Additional Stats Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-blue-100 shadow hover:shadow-lg transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-blue-700">Past Classes</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($pastClasses ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Completed classes</p>
                        </div>
                        <div class="bg-blue-50 text-blue-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-emerald-100 shadow hover:shadow-lg transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-emerald-700">Total Students</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($totalStudents ?? 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Student bookings (including repeats)</p>
                        </div>
                        <div class="bg-emerald-50 text-emerald-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Classes Section --}}
            @if(isset($topClasses) && count($topClasses) > 0)
            <div class="bg-white/90 backdrop-blur-sm shadow-lg rounded-2xl p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">🏆 Top Performing Classes</h3>
                    <span class="text-xs text-gray-500">Most booked classes</span>
                </div>
                <div class="space-y-3">
                    @foreach($topClasses as $class)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $class->classType->name ?? 'Class' }}</p>
                            <p class="text-xs text-gray-500">{{ $class->date_time->format('M d, Y') }} • {{ $class->date_time->format('h:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-purple-600">{{ $class->members_count ?? 0 }}</p>
                            <p class="text-xs text-gray-500">bookings</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Motivation --}}
            <div class="bg-gradient-to-r from-purple-600/80 to-indigo-700/80 text-white rounded-2xl shadow-lg p-8 text-center">
                <h2 class="text-2xl font-bold mb-2">🔥 Daily Motivation</h2>
                <p class="text-lg italic mb-4">“Discipline is the bridge between goals and achievements.”</p>
                <p class="text-sm text-purple-100">– African Fitness Wisdom</p>
            </div>

            {{-- Community Section & Recent Classes --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white/90 backdrop-blur-sm shadow-md rounded-2xl border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">🌿 Community & Wellness Corner</h2>
                    <p class="text-gray-600 mb-4">In Africa, we rise by lifting others. Keep your clients motivated — wellness is not just physical, it’s mental and communal.</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 text-left">
                        <li>🏆 Highlight your top-performing client this week</li>
                        <li>🤝 Encourage group workout sessions</li>
                        <li>🥗 Share nutrition tips with your community</li>
                        <li>💬 Collect feedback to improve your coaching</li>
                    </ul>
                </div>

                <div class="bg-white/90 backdrop-blur-sm shadow-md rounded-2xl border border-gray-100 p-6">
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
                                    <div class="text-right">
                                        <span class="text-xs font-semibold {{ $class->date_time->isPast() ? 'text-gray-500' : 'text-green-600' }}">
                                            {{ $class->date_time->isPast() ? 'Completed' : 'Upcoming' }}
                                        </span>
                                        <p class="text-xs text-purple-600">{{ $class->members_count ?? 0 }} booked</p>
                                    </div>
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
        <a href="{{ route('instructor.create') }}" class="fixed bottom-8 right-8 bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-full shadow-xl ring-4 ring-white/50 transform hover:scale-105 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </a>
    </div>
</x-app-layout>
