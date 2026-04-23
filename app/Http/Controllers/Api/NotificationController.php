<?php
// app/Http/Controllers/Api/NotificationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => Notification::where('user_id', auth()->id())->where('read', false)->count()
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        return response()->json(['success' => true]);
    }

    public function registerDevice(Request $request)
    {
        $request->validate([
            'push_token' => 'required|string',
            'device_type' => 'required|in:ios,android,web'
        ]);

        $user = auth()->user();
        $user->push_token = $request->push_token;
        $user->device_type = $request->device_type;
        $user->save();

        return response()->json(['success' => true]);
    }

    public function getSettings()
    {
        $settings = auth()->user()->notificationSettings;

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function updateSettings(Request $request)
    {
        $settings = auth()->user()->notificationSettings;
        $settings->update($request->only(['push_enabled', 'email_enabled', 'in_app_enabled', 'preferences']));

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }
}
