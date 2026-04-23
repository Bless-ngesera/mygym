{{-- resources/views/instructor/schedule/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Class Details
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    View and manage your scheduled class
                </p>
            </div>
            <div class="flex gap-3">
                @if(!$isPast)
                    <a href="{{ route('instructor.schedule.edit', $scheduledClass) }}"
                       class="px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Class
                    </a>
                    <form method="POST" action="{{ route('instructor.schedule.destroy', $scheduledClass) }}"
                          onsubmit="return confirm('Are you sure you want to cancel this class? This will notify all booked members.');"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Cancel Class
                        </button>
                    </form>
                @endif
                <a href="{{ route('instructor.upcoming') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen"
         style="background-image: url('{{ asset('images/background2.jpg') }}');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Class Information Card -->
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-black">Class Information</h3>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $isPast ? 'bg-gray-100 text-gray-600' : ($isFull ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600') }}">
                                    {{ $isPast ? 'Past Class' : ($isFull ? 'Fully Booked' : 'Available') }}
                                </span>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Class Type</p>
                                        <p class="text-lg font-semibold text-gray-900">{{ $scheduledClass->classType->name }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Date & Time</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ $scheduledClass->date_time->format('l, F j, Y') }}
                                            <span class="text-sm text-gray-500 ml-2">{{ $scheduledClass->date_time->format('g:i A') }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Price</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            UGX {{ number_format($scheduledClass->price, 0) }}
                                        </p>
                                    </div>
                                </div>

                                @if($scheduledClass->capacity)
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Capacity</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ $scheduledClass->members_count }} / {{ $scheduledClass->capacity }} booked
                                            @if($availableSpots !== null && $availableSpots > 0)
                                                <span class="text-sm text-green-600 ml-2">{{ $availableSpots }} spots left</span>
                                            @elseif($availableSpots === 0)
                                                <span class="text-sm text-red-600 ml-2">Fully booked</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Booked Members List -->
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Booked Members ({{ $scheduledClass->members_count }})
                                </h3>
                                <button onclick="exportMembers()"
                                        class="text-sm text-purple-600 hover:text-purple-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Export List
                                </button>
                            </div>

                            @if($scheduledClass->members->count() > 0)
                                <div class="space-y-3">
                                    @foreach($scheduledClass->members as $member)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                            <div class="flex items-center gap-3">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=4F46E5&color=fff&bold=true&size=64"
                                                     alt="{{ $member->name }}"
                                                     class="w-10 h-10 rounded-lg">
                                                <div>
                                                    <p class="font-semibold text-gray-900">{{ $member->name }}</p>
                                                    <p class="text-sm text-gray-500">{{ $member->email }}</p>
                                                </div>
                                            </div>
                                            <button onclick="messageMember({{ $member->id }}, '{{ $member->name }}')"
                                                    class="px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg text-sm font-medium transition">
                                                Message
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-gray-500">No members have booked this class yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Stats Card -->
                    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-purple-100 text-sm">Your Stats</p>
                            <svg class="w-6 h-6 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-2xl font-bold">{{ $totalClients }}</p>
                                <p class="text-purple-100 text-sm">Total Clients</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold">{{ $totalBookings }}</p>
                                <p class="text-purple-100 text-sm">Total Bookings</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <button onclick="sendBulkMessage()"
                                        class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    Message All Members
                                </button>
                                <button onclick="downloadAttendance()"
                                        class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download Attendance
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Member Modal -->
    <div id="messageModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4" id="modalTitle">Send Message</h3>
                <form id="messageForm" method="POST">
                    @csrf
                    <input type="hidden" name="receiver_id" id="receiverId">
                    <textarea name="message" id="messageText" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="Type your message here..."></textarea>
                    <div class="flex gap-3 mt-4">
                        <button type="button" onclick="closeMessageModal()"
                                class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function messageMember(memberId, memberName) {
            document.getElementById('modalTitle').textContent = `Message ${memberName}`;
            document.getElementById('receiverId').value = memberId;
            document.getElementById('messageForm').action = `/instructor/members/${memberId}/message`;
            document.getElementById('messageModal').classList.remove('hidden');
            document.getElementById('messageModal').classList.add('flex');
        }

        function closeMessageModal() {
            document.getElementById('messageModal').classList.add('hidden');
            document.getElementById('messageModal').classList.remove('flex');
            document.getElementById('messageText').value = '';
        }

        function sendBulkMessage() {
            alert('Bulk messaging feature coming soon!');
        }

        function downloadAttendance() {
            alert('Attendance download feature coming soon!');
        }

        function exportMembers() {
            alert('Export members feature coming soon!');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMessageModal();
            }
        });
    </script>
</x-app-layout>
