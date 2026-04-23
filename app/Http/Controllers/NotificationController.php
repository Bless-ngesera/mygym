<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role ?? 'member';

        $query = Notification::where('user_id', $user->id)
            ->notExpired()
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('filter')) {
            if ($request->filter === 'unread') {
                $query->unread();
            } elseif ($request->filter === 'read') {
                $query->read();
            }
        }

        // Apply priority filter
        if ($request->filled('priority') && in_array($request->priority, ['critical', 'high', 'medium', 'low'])) {
            $query->byPriority($request->priority);
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        $notifications = $query->paginate($request->get('per_page', 20));

        // Get statistics
        $unreadCount = Notification::getUnreadCount($user->id);
        $priorityCounts = Notification::getPriorityCounts($user->id);
        $typeCounts = Notification::getTypeCounts($user->id);

        // Get available types for filter dropdown
        $availableTypes = Notification::where('user_id', $user->id)
            ->select('type')
            ->distinct()
            ->pluck('type')
            ->toArray();

        Log::info('Notifications page loaded', [
            'user_id' => $user->id,
            'user_role' => $role,
            'total' => $notifications->total(),
            'unread' => $unreadCount
        ]);

        // Use the same view for all roles (member, instructor, admin)
        return view('notifications.index', compact(
            'notifications',
            'unreadCount',
            'priorityCounts',
            'typeCounts',
            'availableTypes',
            'role'
        ));
    }

    /**
     * Get recent notifications for the dropdown.
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 10);

        $notifications = Notification::where('user_id', Auth::id())
            ->notExpired()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->short_message,
                    'read' => $notification->read,
                    'priority' => $notification->priority,
                    'priority_color' => $notification->priority_color,
                    'icon' => $notification->icon,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'formatted_time' => $notification->created_at->format('g:i A'),
                    'formatted_date' => $notification->created_at->format('M j, Y'),
                    'action_url' => $notification->action_url,
                    'action_button_text' => $notification->action_button_text,
                    'data' => $notification->data
                ];
            });

        $unreadCount = Notification::getUnreadCount(Auth::id());

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'has_more' => $notifications->count() >= $limit
        ]);
    }

    /**
     * Get notification statistics.
     */
    public function stats()
    {
        $unreadCount = Notification::getUnreadCount(Auth::id());
        $priorityCounts = Notification::getPriorityCounts(Auth::id());
        $typeCounts = Notification::getTypeCounts(Auth::id());

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'priority_counts' => $priorityCounts,
            'type_counts' => $typeCounts
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

            $unreadCount = Notification::getUnreadCount(Auth::id());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read',
                    'unread_count' => $unreadCount,
                    'notification_id' => $notification->id
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
            ->count();

        if ($count > 0) {
            Notification::where('user_id', Auth::id())
                ->where('read', false)
                ->update(['read' => true, 'read_at' => now()]);
        }

        $message = $count > 0
            ? "{$count} notification(s) marked as read"
            : "No unread notifications to mark";

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
                'unread_count' => 0
            ]);
        }

        return redirect()->back()->with('success', $message);
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

            $unreadCount = Notification::getUnreadCount(Auth::id());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification deleted',
                    'unread_count' => $unreadCount
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

        $message = $count > 0
            ? "{$count} notifications cleared"
            : "No notifications to clear";

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
                'unread_count' => 0
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Clear read notifications only.
     */
    public function clearRead()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('read', true)
            ->delete();

        $unreadCount = Notification::getUnreadCount(Auth::id());

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$count} read notifications cleared",
                'count' => $count,
                'unread_count' => $unreadCount
            ]);
        }

        return redirect()->back()->with('success', "{$count} read notifications cleared");
    }

    /**
     * Get unread count only (for badge updates).
     */
    public function unreadCount()
    {
        $count = Notification::getUnreadCount(Auth::id());

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }

    /**
     * Export notifications to CSV.
     */
    public function export(Request $request)
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'notifications_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        // Add headers
        fputcsv($handle, ['ID', 'Type', 'Title', 'Message', 'Priority', 'Status', 'Created At', 'Read At']);

        // Add data
        foreach ($notifications as $notification) {
            fputcsv($handle, [
                $notification->id,
                $notification->type,
                $notification->title,
                $notification->message,
                $notification->priority,
                $notification->read ? 'Read' : 'Unread',
                $notification->created_at->format('Y-m-d H:i:s'),
                $notification->read_at?->format('Y-m-d H:i:s') ?? 'Not read'
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    /**
     * Show notification settings page.
     */
    public function settings()
    {
        $settings = NotificationSettings::firstOrCreate(
            ['user_id' => Auth::id()],
            ['preferences' => NotificationSettings::getDefaults()]
        );

        return view('notifications.settings', compact('settings'));
    }

    /**
     * Update notification settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'push_enabled' => 'boolean',
            'email_enabled' => 'boolean',
            'in_app_enabled' => 'boolean',
            'preferences' => 'array',
        ]);

        $settings = NotificationSettings::firstOrCreate(
            ['user_id' => Auth::id()]
        );

        $settings->update([
            'push_enabled' => $request->input('push_enabled', false),
            'email_enabled' => $request->input('email_enabled', false),
            'in_app_enabled' => $request->input('in_app_enabled', true),
            'preferences' => array_merge(
                NotificationSettings::getDefaults(),
                $request->input('preferences', [])
            ),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification settings updated successfully',
                'settings' => $settings
            ]);
        }

        return redirect()->back()->with('success', 'Notification settings updated successfully');
    }
}
