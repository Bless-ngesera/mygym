{{-- resources/views/livewire/notification-bell.blade.php --}}
<div class="relative" x-data="{ open: false }">
    <!-- Bell Icon -->
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-indigo-600 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open" @click.away="open = false"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
         x-cloak>

        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-semibold text-gray-900">Notifications</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs text-indigo-600 hover:text-indigo-800">
                    Mark all as read
                </button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ !$notification['read'] ? 'bg-indigo-50' : '' }}">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <span class="text-2xl">{{ $notification['icon'] }}</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $notification['title'] }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $notification['message'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notification['time_ago'] }}</p>
                        </div>
                        @if(!$notification['read'])
                            <button wire:click="markAsRead({{ $notification['id'] }})"
                                    class="text-indigo-500 hover:text-indigo-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-gray-500 text-sm">No new notifications</p>
                </div>
            @endforelse
        </div>

        <div class="p-3 bg-gray-50 border-t border-gray-200">
            <a href="{{ route('notifications.index') }}"
               class="block text-center text-sm text-indigo-600 hover:text-indigo-800">
                View all notifications →
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Listen for new notifications via Echo/Laravel WebSockets
    window.Echo.private(`user.{{ auth()->id() }}`)
        .listen('NotificationSent', (e) => {
            Livewire.dispatch('refreshNotifications');
        });
</script>
@endpush
