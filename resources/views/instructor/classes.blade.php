<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    All Classes
                </h2>
                <p class="text-sm text-gray-500 mt-1">View and manage all your scheduled classes</p>
            </div>
            <a href="{{ route('instructor.create') }}"
               class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                + Schedule New Class
            </a>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))
                <div id="successMessage" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl shadow-md flex items-center justify-between animate-fade-in">
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

            {{-- Error Message --}}
            @if(session('error'))
                <div id="errorMessage" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div id="errorMessage" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl shadow-md">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-semibold">Please fix the following errors:</span>
                    </div>
                    <ul class="list-disc pl-8 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                <div class="p-6 md:p-8">

                    {{-- Filter Tabs --}}
                    <div class="flex flex-wrap gap-3 mb-6 pb-4 border-b border-gray-100">
                        <a href="{{ route('instructor.classes') }}?filter=all"
                           class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request('filter', 'all') == 'all' ? 'bg-purple-600 text-white shadow-md' : 'bg-white/80 text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                            All Classes
                        </a>
                        <a href="{{ route('instructor.classes') }}?filter=upcoming"
                           class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request('filter') == 'upcoming' ? 'bg-green-600 text-white shadow-md' : 'bg-white/80 text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                            Upcoming
                        </a>
                        <a href="{{ route('instructor.classes') }}?filter=past"
                           class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ request('filter') == 'past' ? 'bg-gray-600 text-white shadow-md' : 'bg-white/80 text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                            Past Classes
                        </a>
                        <a href="{{ route('instructor.calendar') }}"
                           class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 bg-white/80 text-gray-700 hover:bg-gray-100 border border-gray-200">
                            📅 Calendar
                        </a>
                    </div>

                    {{-- Classes Count --}}
                    <div class="mb-4 text-sm text-gray-500">
                        Showing <span class="font-semibold text-purple-600">{{ isset($classes) && $classes instanceof \Illuminate\Pagination\LengthAwarePaginator ? $classes->total() : 0 }}</span>
                        @if(request('filter') == 'upcoming')
                            upcoming class(es)
                        @elseif(request('filter') == 'past')
                            past class(es)
                        @else
                            total class(es)
                        @endif
                    </div>

                    {{-- Table View for All Filters --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-100">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Class Type</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Booked</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Members</th>
                                  </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse(($classes ?? []) as $class)
                                <tr class="hover:bg-purple-50/30 transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-800">{{ optional($class->classType)->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 line-clamp-2">{{ optional($class->classType)->description ?? 'No description' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-800">
                                            {{ optional($class->date_time)->format('l, M d, Y') ?? 'TBA' }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ optional($class->date_time)->format('h:i A') ?? 'TBA' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-600">{{ optional($class->classType)->minutes ?? 0 }} minutes</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-emerald-600">UGX {{ number_format($class->price ?? 0, 0) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($class->date_time && $class->date_time->isPast())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Completed
                                            </span>
                                        @elseif($class->date_time)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                                </svg>
                                                Upcoming
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-1">
                                            <span class="text-lg font-bold text-purple-600">{{ $class->members_count ?? 0 }}</span>
                                            <span class="text-xs text-gray-500">booked</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $memberList = [];
                                            // Safely get members if they exist and are a collection
                                            if (isset($class->members) && $class->members instanceof \Illuminate\Database\Eloquent\Collection) {
                                                $memberList = $class->members->take(3);
                                            }
                                        @endphp
                                        @if(count($memberList) > 0)
                                            <div class="flex -space-x-2">
                                                @foreach($memberList as $member)
                                                    @if($member && isset($member->name))
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=10b981&color=fff&bold=true&size=32"
                                                             alt="{{ $member->name }}"
                                                             class="w-8 h-8 rounded-full ring-2 ring-white"
                                                             title="{{ $member->name }}">
                                                    @endif
                                                @endforeach
                                                @if(isset($class->members) && $class->members instanceof \Illuminate\Database\Eloquent\Collection && $class->members->count() > 3)
                                                    <div class="w-8 h-8 rounded-full bg-gray-200 ring-2 ring-white flex items-center justify-center text-xs font-medium text-gray-600">
                                                        +{{ $class->members->count() - 3 }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">No bookings yet</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 text-lg mb-2">
                                            @if(request('filter') == 'upcoming')
                                                No upcoming classes
                                            @elseif(request('filter') == 'past')
                                                No past classes
                                            @else
                                                No classes found
                                            @endif
                                        </p>
                                        @if(request('filter') != 'past')
                                            <a href="{{ route('instructor.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                Schedule Your First Class
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if(isset($classes) && $classes instanceof \Illuminate\Pagination\LengthAwarePaginator && $classes->hasPages())
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            {{ $classes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
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
            let errorMessage = document.getElementById('errorMessage');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s ease';
                successMessage.style.opacity = '0';
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 500);
            }
            if (errorMessage) {
                errorMessage.style.transition = 'opacity 0.5s ease';
                errorMessage.style.opacity = '0';
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                }, 500);
            }
        }, 5000);
    </script>
</x-app-layout>
