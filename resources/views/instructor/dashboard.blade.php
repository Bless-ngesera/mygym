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
                        <p class="text-lg font-bold text-gray-900">24</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Plans</p>
                        <p class="text-lg font-bold text-gray-900">6</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Sessions</p>
                        <p class="text-lg font-bold text-gray-900">3</p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('schedule.store') }}" class="px-5 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transform transition">
                    🗓️ Manage Schedule
                </a>
                <a href="#" class="px-5 py-3 bg-white text-gray-800 font-semibold rounded-full shadow border border-gray-100 hover:shadow-md transition">
                    👥 View Clients
                </a>
                <a href="#" class="px-5 py-3 bg-green-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transition">
                    📊 Track Sessions
                </a>
                <a href="#" class="px-5 py-3 bg-yellow-500 text-white font-semibold rounded-full shadow hover:scale-[1.02] transition">
                    💵 Check Payments
                </a>
            </div>

            {{-- Stats Overview (enhanced) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-purple-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-purple-700">Active Clients</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">24</p>
                            <p class="text-xs text-gray-500 mt-1">Training this month</p>
                        </div>
                        <div class="bg-purple-50 text-purple-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="w-full bg-purple-100 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width:65%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">65% of monthly target</p>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-green-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-green-700">Workout Plans</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">6</p>
                            <p class="text-xs text-gray-500 mt-1">Customized for your clients</p>
                        </div>
                        <div class="bg-green-50 text-green-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="w-full bg-green-100 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width:40%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">40% completed plans</p>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-white/90 backdrop-blur-sm border border-yellow-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-yellow-700">Upcoming Sessions</h3>
                            <p class="text-3xl font-extrabold text-gray-900 mt-2">3</p>
                            <p class="text-xs text-gray-500 mt-1">In the next 24 hours</p>
                        </div>
                        <div class="bg-yellow-50 text-yellow-700 p-3 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="w-full bg-yellow-100 rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width:25%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Next session in 6 hrs</p>
                    </div>
                </div>
            </div>

            {{-- Motivation --}}
            <div class="bg-gradient-to-r from-purple-600/80 to-indigo-700/80 text-white rounded-2xl shadow-lg p-8 text-center">
                <h2 class="text-2xl font-bold mb-2">🔥 Daily Motivation</h2>
                <p class="text-lg italic mb-4">“Discipline is the bridge between goals and achievements.”</p>
                <p class="text-sm text-purple-100">– African Fitness Wisdom</p>
            </div>

            {{-- Community Section & Recent Sessions --}}
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
                        <h3 class="text-lg font-bold text-gray-800">📆 Recent Sessions</h3>
                        <a href="#" class="text-sm text-indigo-600 font-semibold">See all</a>
                    </div>

                    <ul class="space-y-4">
                        <li class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">AK</div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-900">Abena K. — Strength Training</p>
                                        <p class="text-xs text-gray-500">Today • 10:00 AM</p>
                                    </div>
                                    <span class="text-xs text-green-600 font-semibold">Completed</span>
                                </div>
                            </div>
                        </li>

                        <li class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center text-pink-700 font-bold">MO</div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-900">Mohammed S. — Cardio</p>
                                        <p class="text-xs text-gray-500">Yesterday • 5:00 PM</p>
                                    </div>
                                    <span class="text-xs text-yellow-600 font-semibold">Missed</span>
                                </div>
                            </div>
                        </li>

                        <li class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold">LN</div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-900">Lina N. — Nutrition Check</p>
                                        <p class="text-xs text-gray-500">2 days ago • 2:00 PM</p>
                                    </div>
                                    <span class="text-xs text-gray-500">Scheduled</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- Floating Action Button --}}
        <a href="#" class="fixed bottom-8 right-8 bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-full shadow-xl ring-4 ring-white/50 transform hover:scale-105 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </a>
    </div>
</x-app-layout>
