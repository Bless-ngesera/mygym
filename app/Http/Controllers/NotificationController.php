<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->count();

        // Debug: Check if we have data
        \Illuminate\Support\Facades\Log::info('Notifications page loaded', [
            'user_id' => Auth::id(),
            'total' => $notifications->total(),
            'unread' => $unreadCount
        ]);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get recent notifications for the dropdown.
     */
    public function recent(Request $request)
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'read' => $notification->read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'formatted_time' => $notification->created_at->format('g:i A'),
                    'formatted_date' => $notification->created_at->format('M j, Y'),
                    'data' => $notification->data
                ];
            });

        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notification::where('user_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();

            $notification->markAsRead();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
            }

            return redirect()->back()->with('success', 'Notification marked as read');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }
            return redirect()->back()->with('error', 'Notification not found');
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true, 'read_at' => now()]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$count} notifications marked as read",
                'count' => $count
            ]);
        }

        return redirect()->back()->with('success', "{$count} notifications marked as read");
    }

    /**
     * Delete a specific notification.
     */
    public function destroy($id)
    {
        try {
            $notification = Notification::where('user_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();

            $notification->delete();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification deleted'
                ]);
            }

            return redirect()->back()->with('success', 'Notification deleted');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }
            return redirect()->back()->with('error', 'Notification not found');
        }
    }

    /**
     * Clear all notifications for the user.
     */
    public function clearAll()
    {
        $count = Notification::where('user_id', Auth::id())->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$count} notifications cleared",
                'count' => $count
            ]);
        }

        return redirect()->back()->with('success', "{$count} notifications cleared");
    }

    /**
     * Get unread count only (for badge updates).
     */
    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }
}
