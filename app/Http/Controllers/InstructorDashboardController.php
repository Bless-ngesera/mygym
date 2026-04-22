<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ScheduledClass;
use App\Models\Message;
use App\Models\Receipt;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InstructorDashboardController extends Controller
{
    /**
     * Display the instructor dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Basic Statistics
        $totalUniqueClients = ScheduledClass::where('instructor_id', $user->id)
            ->with('members')
            ->get()
            ->pluck('members')
            ->flatten()
            ->unique('id')
            ->count();

        $totalBookings = ScheduledClass::where('instructor_id', $user->id)
            ->withCount('members')
            ->get()
            ->sum('members_count');

        $totalClasses = ScheduledClass::where('instructor_id', $user->id)->count();

        $upcomingClasses = ScheduledClass::where('instructor_id', $user->id)
            ->where('date_time', '>', Carbon::now())
            ->count();

        $pastClasses = ScheduledClass::where('instructor_id', $user->id)
            ->where('date_time', '<', Carbon::now())
            ->count();

        $totalStudents = ScheduledClass::where('instructor_id', $user->id)
            ->withCount('members')
            ->get()
            ->sum('members_count');

        $totalEarnings = Receipt::whereHas('scheduledClass', function($q) use ($user) {
                $q->where('instructor_id', $user->id);
            })->sum('amount');

        $recentClasses = ScheduledClass::where('instructor_id', $user->id)
            ->with(['classType', 'instructor'])
            ->withCount('members')
            ->orderBy('date_time', 'desc')
            ->take(5)
            ->get();

        $topClasses = ScheduledClass::where('instructor_id', $user->id)
            ->with('classType')
            ->withCount('members')
            ->orderByDesc('members_count')
            ->take(5)
            ->get();

        // Get all members (clients) for chat - SIMPLIFIED APPROACH
        $members = $this->getMembersForChat($user);

        return view('instructor.dashboard', compact(
            'totalUniqueClients',
            'totalBookings',
            'totalClasses',
            'upcomingClasses',
            'pastClasses',
            'totalStudents',
            'totalEarnings',
            'recentClasses',
            'topClasses',
            'members'
        ));
    }

    /**
     * Get all members for chat with their latest message and unread count
     * SIMPLIFIED - Get members directly through scheduled classes
     */
    private function getMembersForChat($user)
    {
        // Get all classes taught by this instructor
        $classes = ScheduledClass::where('instructor_id', $user->id)->get();

        // Get all member IDs from these classes
        $memberIds = [];
        foreach ($classes as $class) {
            foreach ($class->members as $member) {
                $memberIds[] = $member->id;
            }
        }

        // Remove duplicates
        $memberIds = array_unique($memberIds);

        // Get unique members
        $members = User::whereIn('id', $memberIds)->get();

        foreach ($members as $member) {
            // Get last message between instructor and member
            $lastMessage = Message::where(function($query) use ($user, $member) {
                    $query->where('sender_id', $user->id)->where('receiver_id', $member->id);
                })->orWhere(function($query) use ($user, $member) {
                    $query->where('sender_id', $member->id)->where('receiver_id', $user->id);
                })
                ->latest()
                ->first();

            $member->last_message = $lastMessage->message ?? null;
            $member->last_message_time = $lastMessage ? $lastMessage->created_at->diffForHumans() : null;

            // Count unread messages from this member
            $member->unread_count = Message::where('receiver_id', $user->id)
                ->where('sender_id', $member->id)
                ->where('read', false)
                ->count();
        }

        return $members;
    }

    /**
     * Get conversation with a specific member
     */
    public function getConversation($memberId)
    {
        $user = Auth::user();
        $member = User::findOrFail($memberId);

        $messages = Message::where(function($query) use ($user, $member) {
                $query->where('sender_id', $user->id)->where('receiver_id', $member->id);
            })->orWhere(function($query) use ($user, $member) {
                $query->where('sender_id', $member->id)->where('receiver_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages from member as read
        Message::where('sender_id', $member->id)
            ->where('receiver_id', $user->id)
            ->where('read', false)
            ->update(['read' => true, 'read_at' => now()]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'member' => $member
        ]);
    }

    /**
     * Send a message to a member
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        $user = Auth::user();

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
            'read' => false,
        ]);

        // Create notification for the member
        Notification::create([
            'user_id' => $validated['receiver_id'],
            'type' => 'message',
            'title' => 'New Message from Instructor ' . $user->name,
            'message' => substr($validated['message'], 0, 100),
            'data' => json_encode(['message_id' => $message->id]),
            'read' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Delete a message (instructor can delete their own messages)
     */
    public function deleteMessage(Message $message)
    {
        if ($message->sender_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Edit a message
     */
    public function updateMessage(Request $request, Message $message)
    {
        if ($message->sender_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message->update([
            'message' => $validated['message'],
            'is_edited' => true,
            'edited_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }

    /**
     * Pin/unpin a message
     */
    public function pinMessage(Message $message)
    {
        // Both sender and receiver can pin messages
        if ($message->receiver_id !== Auth::id() && $message->sender_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $isPinning = !$message->is_pinned;

        $message->update([
            'is_pinned' => $isPinning,
            'pinned_at' => $isPinning ? now() : null
        ]);

        return response()->json(['success' => true, 'is_pinned' => $message->is_pinned]);
    }

    /**
     * Get all members (for chat list)
     */
    public function getMembers()
    {
        $user = Auth::user();
        $members = $this->getMembersForChat($user);

        return response()->json([
            'success' => true,
            'members' => $members
        ]);
    }

    /**
     * Get unread message count
     */
    public function getUnreadCount()
    {
        $user = Auth::user();

        $unreadCount = Message::where('receiver_id', $user->id)
            ->where('read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark all messages from a member as read
     */
    public function markConversationAsRead($memberId)
    {
        $user = Auth::user();

        $updated = Message::where('sender_id', $memberId)
            ->where('receiver_id', $user->id)
            ->where('read', false)
            ->update(['read' => true, 'read_at' => now()]);

        return response()->json([
            'success' => true,
            'updated' => $updated
        ]);
    }
}
