<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function instructorConversations()
    {
        $user = Auth::user();
        $conversations = Message::getConversationsForUser($user->id);

        return view('instructor.messages', compact('conversations'));
    }

    public function getConversation($userId)
    {
        $user = Auth::user();
        $messages = Message::conversation($user->id, $userId)->get();
        $otherUser = User::find($userId);

        // Mark messages as read
        Message::markAllAsRead($user->id, $userId);

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'other_user' => $otherUser
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        $message = Message::send(
            Auth::id(),
            $request->receiver_id,
            $request->message
        );

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function markAsRead(Message $message)
    {
        if ($message->receiver_id === Auth::id()) {
            $message->markAsRead();
        }

        return response()->json(['success' => true]);
    }
}
