<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MessageController extends Controller
{
    /**
     * Get all conversations for the authenticated user (for instructor view)
     */
    public function instructorConversations()
    {
        $user = Auth::user();
        $conversations = Message::getConversationsForUser($user->id);

        return view('instructor.messages', compact('conversations'));
    }

    /**
     * Get conversation between current user and another user
     */
    public function getConversation($userId)
    {
        try {
            $user = Auth::user();
            $messages = Message::conversation($user->id, $userId)
                ->orderBy('created_at', 'asc')
                ->get();

            $otherUser = User::find($userId);

            if (!$otherUser) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not found'
                ], 404);
            }

            // Mark messages as read
            Message::markAllAsRead($user->id, $userId);

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'other_user' => $otherUser
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load conversation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000'
            ]);

            $message = Message::send(
                Auth::id(),
                $request->receiver_id,
                $request->message
            );

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to send message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a single message as read
     */
    public function markAsRead(Message $message)
    {
        try {
            if ($message->receiver_id === Auth::id() && !$message->read) {
                $message->update([
                    'read' => 1,
                    'read_at' => Carbon::now()
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to mark message as read'
            ], 500);
        }
    }

    /**
     * Edit an existing message (only by sender)
     */
    public function updateMessage(Request $request, $messageId)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000'
            ]);

            $message = Message::where('id', $messageId)
                ->where('sender_id', Auth::id())
                ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'error' => 'Message not found or you do not have permission to edit it'
                ], 404);
            }

            $message->update([
                'message' => $request->message,
                'is_edited' => 1,
                'edited_at' => Carbon::now()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a message (soft delete - mark as deleted for user)
     */
    public function deleteMessage($messageId)
    {
        try {
            $message = Message::find($messageId);

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'error' => 'Message not found'
                ], 404);
            }

            $userId = Auth::id();

            // Mark as deleted for the appropriate user
            if ($message->sender_id == $userId) {
                $message->update(['is_deleted_by_sender' => 1]);
            } elseif ($message->receiver_id == $userId) {
                $message->update(['is_deleted_by_receiver' => 1]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'You do not have permission to delete this message'
                ], 403);
            }

            // If both users have deleted the message, permanently delete it
            if ($message->is_deleted_by_sender && $message->is_deleted_by_receiver) {
                $message->forceDelete();
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pin or unpin a message (only by sender)
     */
    public function pinMessage($messageId)
    {
        try {
            $message = Message::where('id', $messageId)
                ->where('sender_id', Auth::id())
                ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'error' => 'Message not found or you do not have permission to pin it'
                ], 404);
            }

            $newPinStatus = !$message->is_pinned;

            $message->update([
                'is_pinned' => $newPinStatus,
                'pinned_at' => $newPinStatus ? Carbon::now() : null
            ]);

            return response()->json([
                'success' => true,
                'is_pinned' => $newPinStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to pin/unpin message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread message count for the authenticated user
     */
    public function getUnreadCount()
    {
        try {
            $count = Message::where('receiver_id', Auth::id())
                ->where('read', 0)
                ->where('is_deleted_by_receiver', 0)
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'unread_count' => 0
            ], 500);
        }
    }

    /**
     * Get recent conversations with last message preview
     */
    public function getRecentConversations()
    {
        try {
            $userId = Auth::id();
            $conversations = Message::getConversationsForUser($userId);

            return response()->json([
                'success' => true,
                'conversations' => $conversations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to load conversations'
            ], 500);
        }
    }
}
