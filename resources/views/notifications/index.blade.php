{{-- resources/views/member/notifications/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-950 via-indigo-900 to-indigo-800 px-7 py-6 shadow-xl">
            {{-- Decorative glows --}}
            <div class="pointer-events-none absolute -top-16 -right-16 h-56 w-56 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-10 left-10 h-40 w-40 rounded-full bg-indigo-400/10 blur-2xl"></div>
            <div class="pointer-events-none absolute top-0 right-1/3 h-px w-1/3 bg-gradient-to-r from-transparent via-indigo-400/40 to-transparent"></div>

            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg ring-1 ring-white/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-indigo-300">Stay Informed</p>
                        <h1 class="mt-1 text-2xl font-bold text-white tracking-tight">Notifications</h1>
                        <p class="mt-1 text-sm text-indigo-200/80">
                            @if($unreadCount > 0)
                                You have {{ $unreadCount }} unread notification{{ $unreadCount !== 1 ? 's' : '' }}
                            @else
                                All caught up!
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    @if($unreadCount > 0)
                        <button onclick="markAllAsRead()"
                                class="px-5 py-2.5 bg-white/15 backdrop-blur-sm hover:bg-white/25 text-white text-sm font-semibold rounded-xl inline-flex items-center gap-2 ring-1 ring-white/20 transition-all duration-200 hover:-translate-y-0.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Mark all as read
                        </button>
                    @endif
                    <button onclick="openClearAllModal()"
                            class="px-5 py-2.5 bg-red-500/80 backdrop-blur-sm hover:bg-red-600 text-white text-sm font-semibold rounded-xl inline-flex items-center gap-2 ring-1 ring-white/20 transition-all duration-200 hover:-translate-y-0.5 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Clear all
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <main class="min-h-screen"
          style="background-image: url('{{ asset('images/background2.jpg') }}');
                 background-size: cover;
                 background-position: center;
                 background-attachment: fixed;">
        <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8 md:py-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div id="successMessage" class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 rounded-2xl" style="transition: opacity 0.5s ease;">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-emerald-800">{{ session('success') }}</span>
                    </div>
                    <button onclick="dismissMessage(this)" class="ml-4 p-1.5 rounded-lg text-emerald-500 hover:bg-emerald-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div id="errorMessage" class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-2xl" style="transition: opacity 0.5s ease;">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-red-800">{{ session('error') }}</span>
                    </div>
                    <button onclick="dismissMessage(this)" class="ml-4 p-1.5 rounded-lg text-red-500 hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            {{-- Notifications Content --}}
            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm tracking-tight">Notification History</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Recent updates and alerts</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if($notifications->count() > 0)
                        <div class="space-y-3">
                            @foreach($notifications as $notification)
                                <div class="group p-4 rounded-xl transition-all duration-200 hover:shadow-md {{ $notification->read ? 'bg-gray-50/80 border border-gray-100' : 'bg-indigo-50/60 border border-indigo-200' }}">
                                    <div class="flex items-start gap-4">
                                        {{-- Icon --}}
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                                            {{ $notification->type === 'booking' ? 'bg-blue-100 text-blue-600' :
                                               ($notification->type === 'achievement' ? 'bg-amber-100 text-amber-600' :
                                               ($notification->type === 'warning' ? 'bg-red-100 text-red-600' : 'bg-indigo-100 text-indigo-600')) }}">
                                            @if($notification->type === 'booking')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            @elseif($notification->type === 'achievement')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @elseif($notification->type === 'warning')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </div>

                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-4 flex-wrap">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <h3 class="text-base font-semibold text-gray-900">
                                                            {{ $notification->title }}
                                                        </h3>
                                                        @if(!$notification->read)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                                                New
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        {{ $notification->message }}
                                                    </p>
                                                    <div class="flex items-center gap-3 mt-2">
                                                        <p class="text-xs text-gray-400">
                                                            {{ $notification->created_at->format('F j, Y \a\t g:i A') }}
                                                        </p>
                                                        @if($notification->data['action_url'] ?? false)
                                                            <a href="{{ $notification->data['action_url'] }}"
                                                               class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                                                View details →
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                    @if(!$notification->read)
                                                        <button onclick="markAsRead({{ $notification->id }})"
                                                                class="p-2 rounded-lg text-indigo-500 hover:bg-indigo-100 transition-colors"
                                                                title="Mark as read">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                    <button onclick="deleteNotification({{ $notification->id }})"
                                                            class="p-2 rounded-lg text-red-500 hover:bg-red-100 transition-colors"
                                                            title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No notifications</h3>
                            <p class="text-sm text-gray-500 max-w-sm mx-auto">
                                You're all caught up! We'll notify you here when something important happens.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    {{-- Clear All Confirmation Modal (Professional Design) --}}
    <div id="clearAllModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4" onclick="if(event.target===this) closeClearAllModal()">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="clearAllModalContent">
            <div class="relative">
                {{-- Decorative header --}}
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 via-red-600 to-red-700"></div>

                <div class="p-6 text-center">
                    {{-- Warning icon --}}
                    <div class="mx-auto w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 mb-2">Clear All Notifications?</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        This action will permanently delete all your notifications.
                        <span class="font-semibold text-red-600">This cannot be undone.</span>
                    </p>

                    <div class="flex gap-3">
                        <button onclick="closeClearAllModal()"
                                class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all duration-200">
                            Cancel
                        </button>
                        <button onclick="confirmClearAll()"
                                class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-200 transition-all duration-200 active:scale-95">
                            Clear All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Single Confirmation Modal --}}
    <div id="deleteModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4" onclick="if(event.target===this) closeDeleteModal()">
        <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="deleteModalContent">
            <div class="relative">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 via-red-600 to-red-700"></div>

                <div class="p-6 text-center">
                    <div class="mx-auto w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Notification?</h3>
                    <p class="text-sm text-gray-500 mb-6">This notification will be permanently removed.</p>

                    <div class="flex gap-3">
                        <button onclick="closeDeleteModal()"
                                class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button onclick="confirmDelete()"
                                class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let pendingNotificationId = null;

        function dismissMessage(btn) {
            const msg = btn.closest('[id$="Message"]');
            if (msg) {
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 500);
            }
        }

        async function markAsRead(notificationId) {
            try {
                const response = await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    const data = await response.json();
                    alert(data.error || 'Failed to mark notification as read');
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
                alert('Failed to mark notification as read');
            }
        }

        async function markAllAsRead() {
            try {
                const response = await fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    const data = await response.json();
                    alert(data.error || 'Failed to mark all as read');
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
                alert('Failed to mark all as read');
            }
        }

        function deleteNotification(notificationId) {
            pendingNotificationId = notificationId;
            const modal = document.getElementById('deleteModal');
            const content = document.getElementById('deleteModalContent');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
            document.body.style.overflow = 'hidden';
        }

        async function confirmDelete() {
            if (!pendingNotificationId) return;

            try {
                const response = await fetch(`/notifications/${pendingNotificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    const data = await response.json();
                    alert(data.error || 'Failed to delete notification');
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
                alert('Failed to delete notification');
            } finally {
                closeDeleteModal();
            }
        }

        function openClearAllModal() {
            const modal = document.getElementById('clearAllModal');
            const content = document.getElementById('clearAllModalContent');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeClearAllModal() {
            const modal = document.getElementById('clearAllModal');
            const content = document.getElementById('clearAllModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }, 200);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            const content = document.getElementById('deleteModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
                pendingNotificationId = null;
            }, 200);
        }

        async function confirmClearAll() {
            try {
                const response = await fetch('/notifications/clear-all', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    const data = await response.json();
                    alert(data.error || 'Failed to clear notifications');
                }
            } catch (error) {
                console.error('Error clearing notifications:', error);
                alert('Failed to clear notifications');
            } finally {
                closeClearAllModal();
            }
        }

        // Close modals on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeClearAllModal();
                closeDeleteModal();
            }
        });

        // Auto-dismiss flash messages
        setTimeout(() => {
            const successMsg = document.getElementById('successMessage');
            const errorMsg = document.getElementById('errorMessage');
            if (successMsg) {
                successMsg.style.opacity = '0';
                setTimeout(() => successMsg.remove(), 500);
            }
            if (errorMsg) {
                errorMsg.style.opacity = '0';
                setTimeout(() => errorMsg.remove(), 500);
            }
        }, 5000);
    </script>

    <style>
        /* Pagination styling to match dashboard */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .pagination .page-item {
            display: inline-block;
        }
        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            border-radius: 12px;
            background: #f3f4f6;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .pagination .page-link:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
        }
        .pagination .active .page-link {
            background: #4f46e5;
            color: white;
            box-shadow: 0 4px 8px rgba(79, 70, 229, 0.2);
        }
        .pagination .disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(79, 70, 229, 0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(79, 70, 229, 0.45); }

        /* Modal animation */
        #clearAllModal, #deleteModal {
            transition: backdrop-filter 0.2s ease;
        }
    </style>
</x-app-layout>
