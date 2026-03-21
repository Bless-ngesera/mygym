<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                All Classes
            </h2>
            <a href="{{ route('schedule.create') }}"
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

            {{-- Error Messages --}}
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
                    </div>

                    {{-- Classes Count --}}
                    <div class="mb-4 text-sm text-gray-500">
                        Showing <span class="font-semibold text-purple-600">{{ $classes->total() }}</span>
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
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Booked</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Members</th>
                                 </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($classes as $class)
                                <tr class="hover:bg-purple-50/30 transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-800">{{ $class->classType->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500">{{ Str::limit($class->classType->description ?? 'No description', 60) }}</div>
                                            </div>
                                        </div>
                                     </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-800">
                                            {{ $class->date_time->format('l, M d, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $class->date_time->format('h:i A') }}
                                        </div>
                                     </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-600">{{ $class->classType->minutes ?? 0 }} minutes</span>
                                     </td>
                                    <td class="px-6 py-4">
                                        @if($class->date_time->isPast())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Completed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                                </svg>
                                                Upcoming
                                            </span>
                                        @endif
                                     </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-1">
                                            <span class="text-lg font-bold text-purple-600">{{ $class->members()->count() }}</span>
                                            <span class="text-xs text-gray-500">booked</span>
                                        </div>
                                     </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $members = $class->members()->take(3)->get();
                                        @endphp
                                        @if($members->count() > 0)
                                            <div class="flex -space-x-2">
                                                @foreach($members as $member)
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=10b981&color=fff&bold=true&size=32"
                                                         alt="{{ $member->name }}"
                                                         class="w-8 h-8 rounded-full ring-2 ring-white"
                                                         title="{{ $member->name }}">
                                                @endforeach
                                                @if($class->members()->count() > 3)
                                                    <div class="w-8 h-8 rounded-full bg-gray-200 ring-2 ring-white flex items-center justify-center text-xs font-medium text-gray-600">
                                                        +{{ $class->members()->count() - 3 }}
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
                                    <td colspan="6" class="px-6 py-12 text-center">
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
                                            <a href="{{ route('schedule.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
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
                    @if($classes->hasPages())
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            {{ $classes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

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
