{{-- resources/views/notifications/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Notifications
                </h2>
                <p class="text-sm text-gray-500 mt-1">Stay updated with your fitness journey</p>
            </div>
            <div class="flex gap-3">
                @if($unreadCount > 0)
                    <button onclick="markAllAsRead()"
                            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Mark all read
                    </button>
                @endif
                @if($notifications->total() > 0)
                    <button onclick="openClearAllModal()"
                            class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Clear all
                    </button>
                @endif
                <a href="{{ route('notifications.settings') }}"
                   class="px-4 py-2 bg-white/80 backdrop-blur-sm border border-white/40 hover:bg-white text-gray-700 rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
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

            {{-- Flash Messages --}}
            @if(session('success'))
                <div id="successMessage" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl shadow-md flex items-center justify-between animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.closest('#successMessage').remove()" class="text-green-700 hover:text-green-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div id="errorMessage" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl shadow-md flex items-center justify-between animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.closest('#errorMessage').remove()" class="text-red-700 hover:text-red-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm">Total Notifications</p>
                            <p class="text-2xl font-bold">{{ $notifications->total() }}</p>
                        </div>
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-lg border border-white/40">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Unread</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $unreadCount }}</p>
                        </div>
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-lg border border-white/40">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Read</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $notifications->total() - $unreadCount }}</p>
                        </div>
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-lg border border-white/40">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Critical</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $priorityCounts['critical'] ?? 0 }}</p>
                        </div>
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Premium Filter Tabs --}}
            <div class="mb-6">
                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-1 inline-flex shadow-lg border border-white/40">
                    <button onclick="filterNotifications('all')"
                            class="filter-pill px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md"
                            data-filter="all">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            All
                        </div>
                    </button>
                    <button onclick="filterNotifications('unread')"
                            class="filter-pill px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-600 hover:text-purple-600 hover:bg-purple-50"
                            data-filter="unread">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            Unread
                        </div>
                    </button>
                </div>
            </div>

            {{-- Notifications Count --}}
            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-4 mb-4 text-sm text-gray-500 flex justify-between items-center">
                <span>Showing <span class="font-semibold text-purple-600" id="visibleCount">{{ $notifications->count() }}</span> notification(s)</span>
                <span class="text-xs text-gray-400">Total: {{ $notifications->total() }} notifications</span>
            </div>

            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                <div class="p-6 md:p-8">

                    {{-- Notifications Grid --}}
                    <div class="grid grid-cols-1 gap-4" id="notificationsGrid">
                        @forelse($notifications as $notification)
                            <div class="notification-item bg-white/90 backdrop-blur-sm border rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 hover:scale-[1.01] {{ $notification->read ? 'border-white/60' : 'border-purple-200 bg-purple-50/40' }}" data-read="{{ $notification->read ? 'read' : 'unread' }}" data-notification-id="{{ $notification->id }}">
                                <div class="flex items-start gap-4">
                                    {{-- Icon based on notification type --}}
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md
                                        {{ $notification->priority === 'critical' ? 'bg-gradient-to-br from-red-500 to-red-600 text-white' :
                                           ($notification->priority === 'high' ? 'bg-gradient-to-br from-orange-500 to-orange-600 text-white' :
                                           ($notification->priority === 'medium' ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white' :
                                           'bg-gradient-to-br from-purple-500 to-indigo-600 text-white')) }}">
                                        <span class="text-2xl">{{ $notification->icon }}</span>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-4 flex-wrap">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                                    <h3 class="font-bold text-black-900 text-lg">
                                                        {{ $notification->title }}
                                                    </h3>
                                                    @if(!$notification->read)
                                                        <span class="px-2 py-1 bg-gradient-to-r from-purple-400 to-indigo-400 text-white rounded-full text-xs font-medium shadow-sm">New</span>
                                                    @endif
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $notification->priority_badge }}">
                                                        {{ ucfirst($notification->priority) }}
                                                    </span>
                                                </div>
                                                <p class="text-gray-600 text-sm mb-3 leading-relaxed">
                                                    {{ $notification->message }}
                                                </p>
                                                <div class="flex items-center gap-4 text-xs text-gray-400">
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        {{ $notification->created_at->format('F j, Y \a\t g:i A') }}
                                                    </span>
                                                    @if($notification->action_url)
                                                        <a href="{{ $notification->action_url }}"
                                                           class="text-purple-600 hover:text-purple-800 font-semibold transition-colors inline-flex items-center gap-1">
                                                            View details
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Action buttons --}}
                                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                @if(!$notification->read)
                                                    <button onclick="markAsRead({{ $notification->id }})"
                                                            class="p-2 rounded-lg text-purple-600 hover:bg-purple-100 transition-colors"
                                                            title="Mark as read">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                                <button onclick="deleteNotification({{ $notification->id }})"
                                                        class="p-2 rounded-lg text-red-500 hover:bg-red-100 transition-colors"
                                                        title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-lg mb-2">No notifications found</p>
                                <p class="text-gray-400 text-sm">You're all caught up! We'll notify you here when something important happens.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if($notifications->hasPages())
                        <div class="mt-8 pt-4 border-t border-gray-100">
                            {{ $notifications->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Clear All Confirmation Modal --}}
    <div id="clearAllModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4" onclick="if(event.target===this) closeClearAllModal()">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="clearAllModalContent">
            <div class="relative">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 to-red-600"></div>
                <div class="p-6 text-center">
                    <div class="mx-auto w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Clear all notifications?</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        This will permanently delete all your notifications.
                        <span class="font-semibold text-red-600">This cannot be undone.</span>
                    </p>
                    <div class="flex gap-3">
                        <button onclick="closeClearAllModal()"
                                class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button onclick="confirmClearAll()"
                                class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold rounded-xl shadow-lg transition-all duration-200">
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
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 to-red-600"></div>
                <div class="p-6 text-center">
                    <div class="mx-auto w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Delete notification?</h3>
                    <p class="text-sm text-gray-500 mb-6">This notification will be permanently removed.</p>
                    <div class="flex gap-3">
                        <button onclick="closeDeleteModal()"
                                class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button onclick="confirmDelete()"
                                class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold rounded-xl transition-all duration-200">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let pendingNotificationId = null;
        let currentFilter = 'all';

        // Add group-hover class to notification items for hover effects
        document.querySelectorAll('.notification-item').forEach(item => {
            item.classList.add('group');
        });

        // Filter notifications
        function filterNotifications(filter) {
            currentFilter = filter;

            // Update pill styles
            document.querySelectorAll('.filter-pill').forEach(pill => {
                const isActive = pill.dataset.filter === filter;
                if (isActive) {
                    pill.className = 'filter-pill px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md';
                } else {
                    pill.className = 'filter-pill px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 text-gray-600 hover:text-purple-600 hover:bg-purple-50';
                }
            });

            // Filter notifications
            const items = document.querySelectorAll('.notification-item');
            let visibleCount = 0;
            items.forEach(item => {
                if (filter === 'all') {
                    item.style.display = '';
                    visibleCount++;
                } else if (filter === 'unread') {
                    if (item.dataset.read === 'unread') {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                }
            });

            const visibleSpan = document.getElementById('visibleCount');
            if (visibleSpan) {
                visibleSpan.textContent = visibleCount;
            }
        }

        // Mark single notification as read
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

        // Mark all as read
        async function markAllAsRead() {
            if (!confirm('Mark all notifications as read?')) return;

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

        // Delete single notification
        function deleteNotification(notificationId) {
            pendingNotificationId = notificationId;
            const modal = document.getElementById('deleteModal');
            const content = document.getElementById('deleteModalContent');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
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

        // Clear all notifications
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

        async function confirmClearAll() {
            if (!confirm('Are you sure you want to delete ALL notifications? This cannot be undone.')) {
                closeClearAllModal();
                return;
            }

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

        // Auto-dismiss flash messages after 5 seconds
        setTimeout(function() {
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s ease';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500);
            }
            if (errorMessage) {
                errorMessage.style.transition = 'opacity 0.5s ease';
                errorMessage.style.opacity = '0';
                setTimeout(() => errorMessage.remove(), 500);
            }
        }, 5000);
    </script>

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

        /* Pagination styling */
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
            background: linear-gradient(to right, #9333ea, #4f46e5);
            color: white;
            box-shadow: 0 4px 8px rgba(147, 51, 234, 0.2);
        }
        .pagination .disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Group hover styles */
        .group-hover\:opacity-100 {
            opacity: 0;
        }
        .group:hover .group-hover\:opacity-100 {
            opacity: 1;
        }
    </style>
</x-app-layout>
