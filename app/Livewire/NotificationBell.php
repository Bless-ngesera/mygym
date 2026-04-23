<?php
// app/Livewire/NotificationBell.php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;

class NotificationBell extends Component
{
    public $unreadCount = 0;
    public $notifications = [];
    public $showDropdown = false;

    protected $listeners = [
        'refreshNotifications' => 'loadNotifications',
        'notificationRead' => 'decrementUnreadCount',
        'notificationReceived' => 'loadNotifications'
    ];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $userId = auth()->id();

        $this->unreadCount = Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();

        $this->notifications = Notification::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->short_message,
                    'type' => $notification->type,
                    'priority' => $notification->priority,
                    'icon' => $notification->icon,
                    'time_ago' => $notification->time_ago,
                    'read' => $notification->read,
                    'action_url' => $notification->action_url,
                ];
            });
    }

    public function decrementUnreadCount()
    {
        $this->unreadCount = max(0, $this->unreadCount - 1);
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);
        if ($notification && $notification->user_id === auth()->id() && !$notification->read) {
            $notification->markAsRead();
            $this->unreadCount = max(0, $this->unreadCount - 1);
            $this->loadNotifications();
            $this->dispatch('refreshNotifications');
        }
    }

    public function markAllAsRead()
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->count();

        Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true, 'read_at' => now()]);

        $this->unreadCount = 0;
        $this->loadNotifications();
        $this->dispatch('refreshNotifications');

        session()->flash('success', "{$count} notifications marked as read");
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
