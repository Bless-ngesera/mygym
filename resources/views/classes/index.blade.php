{{-- resources/views/classes/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Available Classes
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Browse and book your next fitness session
                </p>
            </div>
            @auth
                @if(auth()->user()->role === 'member')
                    <a href="{{ route('member.bookings.index') }}"
                       class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        My Bookings
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="mb-6">
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl p-4 shadow-lg border border-white/40 dark:border-gray-700">
                    <div class="flex flex-wrap gap-4 items-center justify-between">
                        <div class="flex flex-wrap gap-2">
                            <button onclick="filterClasses('all')"
                                    class="filter-btn px-4 py-2 rounded-lg text-sm font-semibold transition-all bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md">
                                All Classes
                            </button>
                            <button onclick="filterClasses('week')"
                                    class="filter-btn px-4 py-2 rounded-lg text-sm font-semibold transition-all bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200">
                                This Week
                            </button>
                        </div>
                        <div class="relative">
                            <input type="text" id="searchInput"
                                   placeholder="Search classes..."
                                   class="px-4 py-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="classesGrid">
                @if(isset($classes) && $classes->count() > 0)
                    @foreach($classes as $class)
                        <div class="class-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <!-- Class Header -->
                            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-4 text-white">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-bold text-lg">{{ $class->classType->name ?? 'Fitness Class' }}</h3>
                                        <p class="text-purple-100 text-sm">{{ $class->classType->duration ?? 45 }} minutes</p>
                                    </div>
                                    <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-semibold">
                                        {{ $class->date_time ? $class->date_time->format('D, M j') : 'TBA' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Class Body -->
                            <div class="p-5">
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $class->date_time ? $class->date_time->format('g:i A') : 'TBA' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Instructor: {{ $class->instructor->name ?? 'TBA' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>
                                            {{ $class->members_count ?? 0 }} / {{ $class->capacity ?? '∞' }} booked
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>UGX {{ number_format($class->price, 0) }}</span>
                                    </div>
                                </div>

                                @auth
                                    @if(auth()->user()->role === 'member')
                                        <button onclick="bookClass({{ $class->id }})"
                                                class="mt-5 w-full px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                                            Book Now
                                        </button>
                                    @elseif(auth()->user()->role !== 'member')
                                        <p class="mt-5 text-center text-sm text-gray-500">Only members can book classes</p>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}"
                                       class="mt-5 w-full block text-center px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                                        Login to Book
                                    </a>
                                @endauth
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-12">
                        <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-lg mb-2">No classes available</p>
                        <p class="text-gray-400 text-sm">Check back later for new classes</p>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if(isset($classes) && method_exists($classes, 'links') && $classes->hasPages())
                <div class="mt-8">
                    {{ $classes->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Confirm Booking</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to book this class?</p>
                <form id="bookingForm" method="POST" action="{{ route('member.classes.book') }}">
                    @csrf
                    <input type="hidden" name="scheduled_class_id" id="bookingClassId">
                    <div class="flex gap-3">
                        <button type="button" onclick="closeBookingModal()"
                                class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                            Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function bookClass(classId) {
            document.getElementById('bookingClassId').value = classId;
            const modal = document.getElementById('bookingModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeBookingModal() {
            const modal = document.getElementById('bookingModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function filterClasses(filter) {
            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => {
                btn.classList.remove('bg-gradient-to-r', 'from-purple-600', 'to-indigo-600', 'text-white', 'shadow-md');
                btn.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            });

            if (event && event.target) {
                const clickedButton = event.target;
                clickedButton.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
                clickedButton.classList.add('bg-gradient-to-r', 'from-purple-600', 'to-indigo-600', 'text-white', 'shadow-md');
            }

            // Reload with filter parameter
            window.location.href = `/classes?filter=${filter}`;
        }

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.class-card');

                cards.forEach(card => {
                    const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
                    if (title.includes(searchTerm)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }

        // Close modal on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBookingModal();
            }
        });

        // Close modal when clicking outside
        const modal = document.getElementById('bookingModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeBookingModal();
                }
            });
        }
    </script>
</x-app-layout>
