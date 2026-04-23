<?php

namespace App\View\Components;

use App\Models\Notification;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NotificationStats extends Component
{
    public $stats;

    public function __construct()
    {
        $userId = auth()->id();

        $this->stats = [
            'total' => Notification::where('user_id', $userId)->count(),
            'unread' => Notification::where('user_id', $userId)->where('read', false)->count(),
            'critical' => Notification::where('user_id', $userId)->where('priority', 'critical')->where('read', false)->count(),
            'this_week' => Notification::where('user_id', $userId)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'read_rate' => $this->calculateReadRate($userId),
        ];
    }

    private function calculateReadRate($userId)
    {
        $total = Notification::where('user_id', $userId)->count();
        $read = Notification::where('user_id', $userId)->where('read', true)->count();

        return $total > 0 ? round(($read / $total) * 100) : 0;
    }

    public function render()
    {
        return view('components.notification-stats');
    }
}
