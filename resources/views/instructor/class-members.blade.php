{{-- resources/views/instructor/class-members.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Class Members
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $scheduledClass->classType->name }} - {{ $scheduledClass->date_time->format('F j, Y \a\t g:i A') }}
                </p>
            </div>
            <div class="flex gap-3">
                <button onclick="exportMembers()"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export List
                </button>
                <a href="{{ route('instructor.schedule.show', $scheduledClass) }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Class
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6">
                    <!-- Stats Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl p-4 text-white">
                            <p class="text-purple-100 text-sm">Total Members</p>
                            <p class="text-2xl font-bold">{{ $members->total() }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-700 rounded-xl p-4 shadow">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Class Capacity</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $scheduledClass->members_count }} / {{ $scheduledClass->capacity ?? '∞' }}
                            </p>
                        </div>
                        <div class="bg-white dark:bg-gray-700 rounded-xl p-4 shadow">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Class Status</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $scheduledClass->date_time->isPast() ? 'Completed' : 'Upcoming' }}
                            </p>
                        </div>
                    </div>

                    <!-- Members List -->
                    @if($members->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Member</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Booked On</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($members as $member)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=4F46E5&color=fff&bold=true&size=64"
                                                         alt="{{ $member->name }}"
                                                         class="w-8 h-8 rounded-lg">
                                                    <div>
                                                        <p class="font-medium text-gray-900 dark:text-white">{{ $member->name }}</p>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">Member since {{ $member->created_at->format('M Y') }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <p class="text-sm text-gray-900 dark:text-white">{{ $member->email }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $member->phone ?? 'No phone' }}</p>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <p class="text-sm text-gray-900 dark:text-white">{{ $member->pivot->created_at->format('M d, Y') }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $member->pivot->created_at->format('g:i A') }}</p>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                    Confirmed
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <button onclick="messageMember({{ $member->id }}, '{{ $member->name }}')"
                                                        class="text-purple-600 hover:text-purple-700 dark:text-purple-400 transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $members->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-gray-500 text-lg mb-2">No members have booked this class yet</p>
                            <p class="text-gray-400 text-sm">Share this class to attract more participants</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Message Modal -->
    <div id="messageModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4" id="modalTitle">Send Message</h3>
                <form id="messageForm" method="POST" action="{{ route('instructor.members.send-message', ['userId' => ':userId']) }}">
                    @csrf
                    <textarea name="message" id="messageText" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                              placeholder="Type your message here..."></textarea>
                    <div class="flex gap-3 mt-4">
                        <button type="button" onclick="closeMessageModal()"
                                class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
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
            const form = document.getElementById('messageForm');
            form.action = `/instructor/members/${memberId}/message`;
            document.getElementById('messageModal').classList.remove('hidden');
            document.getElementById('messageModal').classList.add('flex');
        }

        function closeMessageModal() {
            document.getElementById('messageModal').classList.add('hidden');
            document.getElementById('messageModal').classList.remove('flex');
            document.getElementById('messageText').value = '';
        }

        function exportMembers() {
            window.location.href = `{{ route('instructor.export.classes') }}?class_id={{ $scheduledClass->id }}`;
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMessageModal();
            }
        });
    </script>
</x-app-layout>
