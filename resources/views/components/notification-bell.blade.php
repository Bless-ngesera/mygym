@props(['count' => 0, 'notifications' => []])

<div x-data="{
    open: false,
    notifications: @js($notifications),
    unreadCount: {{ $count }}
}" x-init="() => {
    // Store reference to this component globally for the markAsRead function
    window.notificationComponent = this;
}" class="relative">

    <!-- Notification Bell Button -->
    <button @click="open = !open"
            class="relative p-2 text-gray-600 hover:text-purple-600 hover:bg-purple-50 dark:text-gray-400 dark:hover:text-purple-400 dark:hover:bg-purple-900/30 rounded-xl transition-all duration-200 group">
        <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>

        <!-- Unread Badge -->
        <span x-show="unreadCount > 0"
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              x-cloak
              class="absolute -top-1 -right-1 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold rounded-full min-w-[20px] h-5 px-1.5 flex items-center justify-center shadow-lg ring-2 ring-white dark:ring-gray-800">
        </span>
    </button>

    <!-- Notification Dropdown -->
    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="absolute right-0 mt-3 w-96 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
         style="display: none;">

        <!-- Header -->
        <div class="px-5 py-4 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Notifications</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Stay updated with your latest activities</p>
                </div>
                <button x-show="unreadCount > 0"
                        @click="markAllAsRead()"
                        class="text-xs text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-semibold transition-colors duration-200">
                    Mark all as read
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-[480px] overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800">
            <template x-for="notification in notifications" :key="notification.id">
                <div @click="markAsRead(notification.id, $event)"
                     :class="{
                         'bg-purple-50/50 dark:bg-purple-900/20': !notification.read,
                         'hover:bg-gray-50 dark:hover:bg-gray-800/50': notification.read
                     }"
                     class="p-4 cursor-pointer transition-all duration-200 group">
                    <div class="flex items-start gap-3">
                        <!-- Icon based on notification type -->
                        <div class="flex-shrink-0">
                            <!-- Workout Icon -->
                            <div x-show="notification.type === 'workout'"
                                 class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <!-- Attendance Icon -->
                            <div x-show="notification.type === 'attendance'"
                                 class="w-10 h-10 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <!-- Payment Icon -->
                            <div x-show="notification.type === 'payment'"
                                 class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-green-500 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <!-- Message Icon -->
                            <div x-show="notification.type === 'message'"
                                 class="w-10 h-10 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                            </div>
                            <!-- Default Icon -->
                            <div x-show="!['workout', 'attendance', 'payment', 'message'].includes(notification.type)"
                                 class="w-10 h-10 bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="notification.title"></p>
                                <span x-show="!notification.read" class="flex-shrink-0">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-purple-600 dark:bg-purple-500"></span>
                                    </span>
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 line-clamp-2" x-text="notification.message"></p>
                            <div class="flex items-center gap-2 mt-2">
                                <p class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span x-text="formatDate(notification.created_at)"></span>
                                </p>
                                <span x-show="notification.type" class="text-xs text-purple-600 dark:text-purple-400 capitalize" x-text="notification.type"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div x-show="notifications.length === 0" class="py-12 px-4 text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">All caught up!</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">No new notifications at the moment</p>
            </div>
        </div>

        <!-- Footer Link -->
        <div x-show="notifications.length > 0" class="px-5 py-3 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 text-center">
            <a href="#" class="text-xs text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-semibold transition-colors duration-200 inline-flex items-center gap-1">
                View all notifications
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    @keyframes ping {
        75%, 100% {
            transform: scale(2);
            opacity: 0;
        }
    }
    .animate-ping {
        animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
    }
</style>

<script>
    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;

        if (diffHours < 24 && date.toDateString() === now.toDateString()) {
            return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        }

        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);
        if (date.toDateString() === yesterday.toDateString()) {
            return `Yesterday at ${date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}`;
        }

        if (diffDays < 7) {
            return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
        }

        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function markAllAsRead() {
        fetch('/member/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(response => response.json()).then(data => {
            if (data.success) {
                // Reload the page to refresh notifications
                location.reload();
            }
        }).catch(error => console.error('Error marking all as read:', error));
    }

    function markAsRead(notificationId, event) {
        const notificationDiv = event.currentTarget;

        fetch(`/member/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(response => response.json()).then(data => {
            if (data.success) {
                // Find the Alpine component and update data
                const component = event.currentTarget.closest('[x-data]').__x.$data;
                const notification = component.notifications.find(n => n.id === notificationId);
                if (notification) {
                    notification.read = true;
                    component.unreadCount--;
                }

                // Update UI classes
                notificationDiv.classList.remove('bg-purple-50/50', 'dark:bg-purple-900/20');
                notificationDiv.classList.add('hover:bg-gray-50', 'dark:hover:bg-gray-800/50');

                // Remove unread indicator
                const unreadIndicator = notificationDiv.querySelector('.flex-shrink-0 .relative');
                if (unreadIndicator) unreadIndicator.style.display = 'none';
            }
        }).catch(error => console.error('Error marking notification as read:', error));
    }
</script>
