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

    <div class="py-12 bg-gray-50 min-h-screen"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
               background-size: cover;
               background-position: center;
               background-attachment: fixed;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- Toast Notification Container --}}
            <div id="toastContainer" class="fixed top-20 right-4 z-50 space-y-2"></div>

            {{-- Hero / Profile Header --}}
            <div class="bg-white/80 backdrop-blur-sm shadow-lg sm:rounded-2xl p-6 border border-gray-100 flex flex-col md:flex-row items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg ring-4 ring-white/60">
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

                <div class="ml-auto grid grid-cols-3 gap-2 sm:gap-4 w-full md:w-auto mt-4 md:mt-0">
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
                <a href="{{ route('instructor.create') }}" class="px-5 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transform transition">🗓️ Schedule a Class</a>
                <a href="{{ route('instructor.classes') }}" class="px-5 py-3 bg-white text-gray-800 font-semibold rounded-full shadow border border-gray-100 hover:shadow-md transition">👥 My Classes</a>
                <a href="{{ route('instructor.upcoming') }}" class="px-5 py-3 bg-green-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transition">📊 Upcoming Classes</a>
                <a href="{{ route('instructor.calendar') }}" class="px-5 py-3 bg-blue-600 text-white font-semibold rounded-full shadow hover:scale-[1.02] transition">📅 Calendar View</a>
                <a href="{{ route('instructor.earnings.index') }}" class="px-5 py-3 bg-yellow-500 text-white font-semibold rounded-full shadow hover:scale-[1.02] transition">💵 My Earnings</a>
            </div>

            {{-- Stats Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-6 rounded-2xl bg-white/90 border border-purple-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div><h3 class="text-sm font-semibold text-purple-700">Unique Clients</h3><p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($totalUniqueClients ?? 0) }}</p><p class="text-xs text-gray-500 mt-1">Distinct members who booked you</p></div>
                        <div class="bg-purple-50 text-purple-700 p-3 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
                    </div>
                </div>
                <div class="p-6 rounded-2xl bg-white/90 border border-indigo-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div><h3 class="text-sm font-semibold text-indigo-700">Total Bookings</h3><p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($totalBookings ?? 0) }}</p><p class="text-xs text-gray-500 mt-1">All class reservations</p></div>
                        <div class="bg-indigo-50 text-indigo-700 p-3 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    </div>
                </div>
                <div class="p-6 rounded-2xl bg-white/90 border border-green-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div><h3 class="text-sm font-semibold text-green-700">Total Classes</h3><p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($totalClasses ?? 0) }}</p><p class="text-xs text-gray-500 mt-1">Classes you've created</p></div>
                        <div class="bg-green-50 text-green-700 p-3 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg></div>
                    </div>
                </div>
                <div class="p-6 rounded-2xl bg-white/90 border border-yellow-100 shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-start justify-between">
                        <div><h3 class="text-sm font-semibold text-yellow-700">Upcoming Classes</h3><p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($upcomingClasses ?? 0) }}</p><p class="text-xs text-gray-500 mt-1">Scheduled for future dates</p></div>
                        <div class="bg-yellow-50 text-yellow-700 p-3 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                    </div>
                </div>
            </div>

            {{-- Additional Stats Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-6 rounded-2xl bg-white/90 border border-blue-100 shadow hover:shadow-lg transition">
                    <div class="flex items-start justify-between">
                        <div><h3 class="text-sm font-semibold text-blue-700">Past Classes</h3><p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($pastClasses ?? 0) }}</p><p class="text-xs text-gray-500 mt-1">Completed classes</p></div>
                        <div class="bg-blue-50 text-blue-700 p-3 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    </div>
                </div>
                <div class="p-6 rounded-2xl bg-white/90 border border-emerald-100 shadow hover:shadow-lg transition">
                    <div class="flex items-start justify-between">
                        <div><h3 class="text-sm font-semibold text-emerald-700">Total Students</h3><p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($totalStudents ?? 0) }}</p><p class="text-xs text-gray-500 mt-1">Student bookings (including repeats)</p></div>
                        <div class="bg-emerald-50 text-emerald-700 p-3 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div>
                    </div>
                </div>
            </div>

            {{-- Top Classes Section --}}
            @if(isset($topClasses) && count($topClasses) > 0)
            <div class="bg-white/90 shadow-lg rounded-2xl p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4"><h3 class="text-lg font-bold text-gray-800">🏆 Top Performing Classes</h3><span class="text-xs text-gray-500">Most booked classes</span></div>
                <div class="space-y-3">@foreach($topClasses as $class)<div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl"><div><p class="font-semibold text-gray-900">{{ $class->classType->name ?? 'Class' }}</p><p class="text-xs text-gray-500">{{ $class->date_time->format('M d, Y') }} • {{ $class->date_time->format('h:i A') }}</p></div><div class="text-right"><p class="text-xl font-bold text-purple-600">{{ $class->members_count ?? 0 }}</p><p class="text-xs text-gray-500">bookings</p></div></div>@endforeach</div>
            </div>
            @endif

            {{-- Motivation --}}
            <div class="bg-gradient-to-r from-purple-600/80 to-indigo-700/80 text-white rounded-2xl shadow-lg p-8 text-center">
                <h2 class="text-2xl font-bold mb-2">🔥 Daily Motivation</h2>
                <p class="text-lg italic mb-4">“Discipline is the bridge between goals and achievements.”</p>
                <p class="text-sm text-purple-100">– African Fitness Wisdom</p>
            </div>

            {{-- Community Section, Recent Classes & Chat --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white/90 shadow-md rounded-2xl border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">🌿 Community & Wellness Corner</h2>
                    <p class="text-gray-600 mb-4">In Africa, we rise by lifting others. Keep your clients motivated — wellness is not just physical, it's mental and communal.</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 text-left"><li>🏆 Highlight your top-performing client this week</li><li>🤝 Encourage group workout sessions</li><li>🥗 Share nutrition tips with your community</li><li>💬 Collect feedback to improve your coaching</li></ul>
                </div>

                <div class="bg-white/90 shadow-md rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4"><h3 class="text-lg font-bold text-gray-800">📆 Your Recent Classes</h3><a href="{{ route('instructor.classes') }}" class="text-sm text-indigo-600 font-semibold hover:underline">See all</a></div>
                    <ul class="space-y-4">@forelse($recentClasses ?? [] as $class)<li class="flex items-center gap-4"><div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">{{ strtoupper(substr($class->classType->name ?? 'C',0,2)) }}</div><div class="flex-1"><div class="flex items-center justify-between"><div><p class="font-semibold text-gray-900">{{ $class->classType->name ?? 'Class' }}</p><p class="text-xs text-gray-500">{{ $class->date_time->format('D, M d • h:i A') }}</p></div><div class="text-right"><span class="text-xs font-semibold {{ $class->date_time->isPast() ? 'text-gray-500' : 'text-green-600' }}">{{ $class->date_time->isPast() ? 'Completed' : 'Upcoming' }}</span><p class="text-xs text-purple-600">{{ $class->members_count ?? 0 }} booked</p></div></div></div></li>@empty<li class="text-center text-gray-500 py-4">No classes scheduled yet.</li>@endforelse</ul>
                </div>
            </div>

            {{-- Chat Section with Members --}}
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-indigo-50">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <h3 class="text-lg font-bold text-gray-900">Messages from Members</h3>
                    </div>
                    <span class="text-xs text-gray-500">Real-time chat with your clients</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3">
                    {{-- Members List --}}
                    <div class="border-r border-gray-100 bg-gray-50/50">
                        <div class="p-4 border-b border-gray-100 bg-white">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input type="text" id="memberSearch" placeholder="Search members..." class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-purple-200 focus:border-purple-400 outline-none">
                            </div>
                        </div>
                        <div id="membersList" class="max-h-96 overflow-y-auto custom-scrollbar">
                            @forelse($members ?? [] as $member)
                                <div class="member-item p-4 border-b border-gray-100 hover:bg-purple-50 cursor-pointer transition-all duration-200 {{ $loop->first ? 'bg-purple-50 border-l-4 border-l-purple-500' : '' }}" data-member-id="{{ $member->id }}" data-member-name="{{ $member->name }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">{{ strtoupper(substr($member->name, 0, 1)) }}</div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="font-semibold text-gray-900 truncate">{{ $member->name }}</p>
                                                <span class="text-xs text-gray-400">{{ $member->last_message_time ?? 'New' }}</span>
                                            </div>
                                            <p class="text-xs text-gray-500 truncate">{{ $member->last_message ?? 'No messages yet' }}</p>
                                        </div>
                                        @if(($member->unread_count ?? 0) > 0)
                                            <span class="w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">{{ $member->unread_count }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500">No members assigned yet.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Chat Area --}}
                    <div class="md:col-span-2 flex flex-col h-[500px]">
                        <div id="chatHeader" class="p-4 border-b border-gray-100 bg-white">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-md" id="selectedAvatar">M</div>
                                <div>
                                    <h4 class="font-bold text-gray-900" id="selectedMemberName">Select a member</h4>
                                    <p class="text-xs text-green-600">● Online</p>
                                </div>
                            </div>
                        </div>
                        <div id="chatMessagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 custom-scrollbar">
                            <div class="flex h-full items-center justify-center text-center text-gray-400">Select a member to start chatting</div>
                        </div>
                        <div class="p-4 border-t border-gray-100 bg-white">
                            <div class="flex gap-2">
                                <input type="text" id="chatMessageInput" placeholder="Type your message..." class="flex-1 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-purple-200 focus:border-purple-400 outline-none" disabled>
                                <button onclick="sendInstructorMessage()" id="sendMessageBtn" disabled class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md opacity-50 cursor-not-allowed">Send</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Floating Action Button --}}
        <a href="{{ route('instructor.create') }}" class="fixed bottom-8 right-8 bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-full shadow-xl ring-4 ring-white/50 transform hover:scale-105 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </a>
    </div>
    <footer class="bg-gradient-to-r from-gray-900 to-gray-800 border-t border-purple-500/30">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
            {{-- Column 1: Logo/Brand Info --}}
            <div class="col-span-2 md:col-span-1 lg:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-14 rounded-xl flex items-center justify-center shadow-lg overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="MyGym Logo" class="w-full h-full object-cover">
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

            {{-- Column 2: Instructor Quick Links --}}
            <div>
                <h5 class="text-lg font-semibold text-white mb-4">Instructor Hub</h5>
                <ul class="space-y-3">
                    <li><a href="{{ route('instructor.dashboard') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📊 Dashboard</a></li>
                    <li><a href="{{ route('instructor.create') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🗓️ Schedule Class</a></li>
                    <li><a href="{{ route('instructor.classes') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">👥 My Classes</a></li>
                    <li><a href="{{ route('instructor.upcoming') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📊 Upcoming Classes</a></li>
                    <li><a href="{{ route('instructor.calendar') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📅 Calendar View</a></li>
                    <li><a href="{{ route('instructor.earnings.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💵 My Earnings</a></li>
                </ul>
            </div>

            {{-- Column 3: Resources & Support --}}
            <div>
                <h5 class="text-lg font-semibold text-white mb-4">Resources</h5>
                <ul class="space-y-3">
                    <li><a href="{{ route('instructor.members.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">👥 My Members</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📚 Training Guides</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💡 Tips & Tricks</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🎓 Certification</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">❓ Help Center</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📧 Support</a></li>
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
                        <span><a href="mailto:instructors@mygym.com" class="hover:text-purple-400">instructors@mygym.com</a></span>
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

        {{-- Copyright Section --}}
        <div class="mt-12 pt-8 border-t border-purple-500/30 text-center">
            <p class="text-sm text-gray-500">
                &copy; {{ date('Y') }} MyGym. All rights reserved. Powered by Passion.
            </p>
        </div>
    </div>
    </footer>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .toast-slide-in { animation: slideIn 0.3s ease-out; }
    </style>

    <script>
        let currentMemberId = null;
        let currentMemberName = null;

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            if (!container) return;
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-emerald-500' : (type === 'error' ? 'bg-red-500' : 'bg-blue-500');
            toast.className = `${bgColor} text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 toast-slide-in`;
            toast.innerHTML = `<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="text-sm font-medium">${message}</span><button onclick="this.closest('div').remove()" class="ml-auto text-white/80 hover:text-white"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        }

        async function loadMessages(memberId, memberName) {
            currentMemberId = memberId;
            currentMemberName = memberName;

            document.getElementById('selectedMemberName').innerText = memberName;
            document.getElementById('selectedAvatar').innerText = memberName.charAt(0).toUpperCase();

            const chatContainer = document.getElementById('chatMessagesContainer');
            chatContainer.innerHTML = '<div class="flex justify-center items-center h-full"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div></div>';

            try {
                const response = await fetch(`/instructor/messages/conversation/${memberId}`, {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                const data = await response.json();

                if (data.success && data.messages) {
                    if (data.messages.length === 0) {
                        chatContainer.innerHTML = '<div class="flex h-full items-center justify-center text-center text-gray-400">No messages yet. Start the conversation!</div>';
                    } else {
                        chatContainer.innerHTML = data.messages.map(msg => `
                            <div class="flex ${msg.sender_id === {{ Auth::id() }} ? 'justify-end' : 'justify-start'}">
                                <div class="max-w-[75%] rounded-2xl px-4 py-2.5 ${msg.sender_id === {{ Auth::id() }} ? 'rounded-br-sm bg-gradient-to-r from-purple-600 to-indigo-600 text-white' : 'rounded-bl-sm bg-white border border-gray-200 text-gray-700 shadow-sm'}">
                                    <p class="text-sm leading-relaxed break-words">${escapeHtml(msg.message)}</p>
                                    <p class="text-[10px] mt-1 ${msg.sender_id === {{ Auth::id() }} ? 'text-purple-200' : 'text-gray-400'}">${new Date(msg.created_at).toLocaleString()}</p>
                                </div>
                            </div>
                        `).join('');
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }
                }

                document.getElementById('chatMessageInput').disabled = false;
                document.getElementById('sendMessageBtn').disabled = false;
                document.getElementById('sendMessageBtn').classList.remove('opacity-50', 'cursor-not-allowed');
                document.getElementById('chatMessageInput').focus();
            } catch (error) {
                console.error('Error loading messages:', error);
                chatContainer.innerHTML = '<div class="flex h-full items-center justify-center text-center text-red-400">Error loading messages. Please try again.</div>';
            }
        }

        async function sendInstructorMessage() {
            const input = document.getElementById('chatMessageInput');
            const message = input?.value.trim();

            if (!message) return;
            if (!currentMemberId) {
                showToast('Please select a member first', 'error');
                return;
            }

            const sendBtn = document.getElementById('sendMessageBtn');
            const originalText = sendBtn.innerHTML;
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<div class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mx-auto"></div>';
            input.disabled = true;

            try {
                const response = await fetch('/instructor/messages/send', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ receiver_id: currentMemberId, message: message })
                });
                const data = await response.json();

                if (data.success) {
                    showToast('Message sent!', 'success');
                    input.value = '';
                    await loadMessages(currentMemberId, currentMemberName);
                } else {
                    showToast(data.error || 'Failed to send message', 'error');
                }
            } catch (error) {
                showToast('Network error. Please try again.', 'error');
            } finally {
                sendBtn.disabled = false;
                sendBtn.innerHTML = originalText;
                input.disabled = false;
                input.focus();
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Member selection and search
        document.querySelectorAll('.member-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.member-item').forEach(i => {
                    i.classList.remove('bg-purple-50', 'border-l-4', 'border-l-purple-500');
                });
                this.classList.add('bg-purple-50', 'border-l-4', 'border-l-purple-500');
                const memberId = this.dataset.memberId;
                const memberName = this.dataset.memberName;
                loadMessages(memberId, memberName);
            });
        });

        document.getElementById('memberSearch')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.member-item').forEach(item => {
                const name = item.dataset.memberName?.toLowerCase() || '';
                item.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        });

        document.getElementById('chatMessageInput')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !this.disabled && currentMemberId) {
                sendInstructorMessage();
            }
        });
    </script>
</x-app-layout>
