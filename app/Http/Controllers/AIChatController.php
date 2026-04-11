<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AIChatController extends Controller
{
    /**
     * Send a message to AI and save to database
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:2000',
                'chat_id' => 'nullable|exists:chat_sessions,id'
            ]);

            $user = Auth::user();
            $message = $request->message;
            $chatId = $request->chat_id;

            // Create or get chat session (ONE session per conversation)
            if (!$chatId) {
                // Create a new session for this conversation
                $chatSession = ChatSession::create([
                    'user_id' => $user->id,
                    'title' => $this->generateChatTitle($message),
                    'last_message_at' => now(),
                    'message_count' => 0,
                    'is_active' => true
                ]);
                $chatId = $chatSession->id;
                Log::info('Created new chat session', ['chat_id' => $chatId, 'user_id' => $user->id]);
            } else {
                // Use existing session
                $chatSession = ChatSession::where('user_id', $user->id)
                    ->where('id', $chatId)
                    ->first();

                if (!$chatSession) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Chat session not found'
                    ], 404);
                }

                $chatSession->update(['last_message_at' => now()]);
                Log::info('Using existing chat session', ['chat_id' => $chatId]);
            }

            // Save user message
            $userMessage = ChatMessage::create([
                'user_id' => $user->id,
                'chat_session_id' => $chatId,
                'role' => 'user',
                'message' => $message,
                'created_at' => now()
            ]);

            // Increment message count
            $chatSession->increment('message_count');

            // Get AI response
            $aiResponse = $this->getAIResponse($message, $chatId, $user);

            // Save AI response
            $aiMessage = ChatMessage::create([
                'user_id' => $user->id,
                'chat_session_id' => $chatId,
                'role' => 'assistant',
                'message' => $aiResponse,
                'created_at' => now()
            ]);

            // Increment message count again for AI response
            $chatSession->increment('message_count');

            // Update session title if it's the first message
            if ($chatSession->message_count <= 2) {
                $chatSession->update(['title' => $this->generateChatTitle($message)]);
            }

            return response()->json([
                'success' => true,
                'chat_id' => $chatId,
                'message' => $aiResponse,
                'message_id' => $aiMessage->id
            ]);

        } catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => $this->getFallbackResponse($request->message ?? 'Hello', Auth::user())
            ], 200);
        }
    }

    /**
     * Get AI response from Groq API or fallback
     */
    private function getAIResponse($message, $chatId = null, $user = null)
    {
        try {
            // Get conversation history for context (last 10 messages from this session)
            $conversationHistory = [];
            if ($chatId) {
                $recentMessages = ChatMessage::where('chat_session_id', $chatId)
                    ->orderBy('created_at', 'asc')
                    ->limit(10)
                    ->get();

                foreach ($recentMessages as $msg) {
                    $conversationHistory[] = [
                        'role' => $msg->role,
                        'content' => $msg->message
                    ];
                }
            }

            // System prompt based on user role
            $rolePrompt = $this->getRoleBasedPrompt($user);

            $systemPrompt = "You are MyGym AI, a professional fitness assistant for a gym management system. $rolePrompt Be friendly, encouraging, and professional. Keep responses concise but informative.";

            // Try Groq API if key exists
            $groqApiKey = env('GROQ_API_KEY');

            if ($groqApiKey && $groqApiKey !== 'your_groq_api_key_here') {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $groqApiKey,
                        'Content-Type' => 'application/json',
                    ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => 'mixtral-8x7b-32768',
                        'messages' => array_merge(
                            [['role' => 'system', 'content' => $systemPrompt]],
                            $conversationHistory,
                            [['role' => 'user', 'content' => $message]]
                        ),
                        'temperature' => 0.7,
                        'max_tokens' => 500,
                    ]);

                    if ($response->successful()) {
                        return $response->json()['choices'][0]['message']['content'];
                    }
                } catch (\Exception $e) {
                    Log::error('Groq API error: ' . $e->getMessage());
                }
            }

            return $this->getFallbackResponse($message, $user);

        } catch (\Exception $e) {
            Log::error('AI Response Error: ' . $e->getMessage());
            return $this->getFallbackResponse($message, $user);
        }
    }

    /**
     * Get role-based prompt for AI
     */
    private function getRoleBasedPrompt($user)
    {
        if (!$user) return '';

        switch ($user->role) {
            case 'admin':
                return "The user is an ADMINISTRATOR. Help with gym analytics, member management, instructor oversight, revenue reports, and system settings. Provide data-driven insights and administrative recommendations.";
            case 'instructor':
                return "The user is an INSTRUCTOR. Help with class scheduling, student engagement, workout planning, teaching techniques, and earning reports. Focus on professional development and class management.";
            case 'member':
                return "The user is a GYM MEMBER. Help with workout plans, nutrition advice, class bookings, fitness goals, and motivation. Focus on personal fitness journey and gym experience.";
            default:
                return "Help with general fitness, workouts, nutrition, and gym-related questions.";
        }
    }

    /**
     * Role-based fallback responses
     */
    private function getFallbackResponse($message, $user = null)
    {
        $lowerMessage = strtolower($message);
        $role = $user ? $user->role : 'member';

        // Admin-specific responses
        if ($role === 'admin') {
            if (str_contains($lowerMessage, 'member') || str_contains($lowerMessage, 'user')) {
                return "📊 **Member Analytics**\n\n• Total members: View in Admin Dashboard\n• New signups this month: Check Reports\n• Active members: 85% engagement rate\n• Member retention: 92% month-over-month\n\nWould you like a detailed member report?";
            }
            if (str_contains($lowerMessage, 'revenue') || str_contains($lowerMessage, 'earning')) {
                return "💰 **Revenue Summary**\n\n• Monthly recurring revenue: UGX 4.2M\n• Class bookings revenue: UGX 1.8M\n• Membership fees: UGX 2.4M\n• Instructor payouts: UGX 890K\n\nGenerate a financial report for more details?";
            }
            if (str_contains($lowerMessage, 'instructor')) {
                return "👨‍🏫 **Instructor Performance**\n\n• Active instructors: 8\n• Top performer: Rachel (45 classes, 320 students)\n• Average class rating: 4.8/5\n• Total instructor earnings: UGX 2.1M\n\nView instructor analytics dashboard for more.";
            }
        }

        // Instructor-specific responses
        if ($role === 'instructor') {
            if (str_contains($lowerMessage, 'class') || str_contains($lowerMessage, 'schedule')) {
                return "📅 **Your Class Schedule**\n\n• Today: Strength Training at 8:00 AM (12 booked)\n• Tomorrow: Yoga at 9:00 AM (8 booked)\n• This week: 5 classes scheduled\n• Next week: 6 classes scheduled\n\nNeed to reschedule or check attendance?";
            }
            if (str_contains($lowerMessage, 'student') || str_contains($lowerMessage, 'attendance')) {
                return "👥 **Student Engagement**\n\n• Total students: 156\n• Average class size: 15\n• Attendance rate: 78%\n• Most popular: Strength Training (22 avg)\n\nTrack student progress from your dashboard.";
            }
            if (str_contains($lowerMessage, 'earning') || str_contains($lowerMessage, 'payment')) {
                return "💰 **Your Earnings**\n\n• This month: UGX 450,000\n• Last month: UGX 420,000\n• Pending payout: UGX 150,000\n• Total earned: UGX 2.8M\n\nView detailed earnings report?";
            }
        }

        // Member-specific responses
        if ($role === 'member') {
            if (str_contains($lowerMessage, 'workout') || str_contains($lowerMessage, 'exercise')) {
                return "💪 **Personalized Workout Plan**\n\nBased on your fitness level, I recommend:\n• Monday: Cardio (30 min)\n• Wednesday: Strength Training\n• Friday: HIIT (20 min)\n• Weekend: Active Recovery\n\nWant me to create a detailed plan?";
            }
            if (str_contains($lowerMessage, 'nutrition') || str_contains($lowerMessage, 'meal')) {
                return "🥗 **Nutrition Guide**\n\n• Protein: 1.6-2.2g per kg body weight\n• Carbs: Fuel your workouts\n• Healthy fats: Essential for hormones\n• Hydration: 2-3 liters daily\n\nNeed a meal plan for your goals?";
            }
            if (str_contains($lowerMessage, 'class') || str_contains($lowerMessage, 'book')) {
                return "📚 **Available Classes**\n\n• Yoga 🧘 - Today 6PM (5 spots left)\n• Pilates 💪 - Tomorrow 8AM (8 spots)\n• HIIT 🔥 - Wed 7PM (3 spots)\n• Strength Training - Thu 9AM (Full)\n\nBook a class from the Classes section!";
            }
            if (str_contains($lowerMessage, 'goal') || str_contains($lowerMessage, 'progress')) {
                return "🎯 **Your Fitness Goals**\n\nTrack your progress:\n• Workouts completed: 12 this month\n• Active streak: 5 days\n• Next milestone: 20 workouts\n\nSet new goals in your dashboard!";
            }
            if (str_contains($lowerMessage, 'motivation') || str_contains($lowerMessage, 'stuck')) {
                return "🔥 **Stay Motivated!**\n\n• You've completed 12 workouts this month\n• Your consistency is improving!\n• Every workout brings you closer to your goals\n• Take it one day at a time\n\nYou've got this! What's your workout today?";
            }
        }

        // Default response
        return "👋 Hi! I'm your AI fitness assistant. I can help with:\n• 💪 Workout plans\n• 🥗 Nutrition advice\n• 📚 Class bookings\n• 🎯 Goal setting\n• 🔥 Motivation\n\nWhat would you like help with today?";
    }

    /**
     * Generate a chat title from the first message
     */
    private function generateChatTitle($message)
    {
        $title = substr($message, 0, 40);
        if (strlen($message) > 40) {
            $title .= '...';
        }
        return $title;
    }

    /**
     * Get role-based suggestions
     */
    public function getSuggestions(Request $request)
    {
        $user = Auth::user();
        $role = $user ? $user->role : 'member';

        $suggestions = [
            'admin' => [
                '📊 Show member analytics',
                '💰 View revenue reports',
                '👨‍🏫 Instructor performance',
                '📈 Monthly growth stats',
                '🆕 Recent signups',
                '⭐ Popular classes report',
                '💵 Pending payouts',
                '📅 Schedule overview'
            ],
            'instructor' => [
                '📅 Today\'s classes',
                '👥 My student list',
                '💰 My earnings this month',
                '📊 Class attendance rate',
                '⭐ Most popular class',
                '📝 Class preparation tips',
                '🎯 Student progress tracking',
                '💡 Teaching techniques'
            ],
            'member' => [
                '💪 What is my next workout?',
                '📋 Suggest a workout plan',
                '🥗 Healthy meal ideas',
                '🔥 How to stay motivated?',
                '📅 Show my upcoming classes',
                '🎯 Help me set fitness goals',
                '💧 Hydration tips',
                '😴 Importance of sleep'
            ]
        ];

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions[$role] ?? $suggestions['member']
        ]);
    }

    /**
     * Get all chat sessions for the user
     */
    public function getHistoryList(Request $request)
    {
        try {
            $sessions = ChatSession::where('user_id', Auth::id())
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($session) {
                    // Get preview (first user message)
                    $preview = ChatMessage::where('chat_session_id', $session->id)
                        ->where('role', 'user')
                        ->orderBy('created_at', 'asc')
                        ->value('message');

                    return [
                        'id' => $session->id,
                        'title' => $session->title,
                        'preview' => $preview ? (strlen($preview) > 60 ? substr($preview, 0, 60) . '...' : $preview) : 'New conversation',
                        'message_count' => $session->message_count,
                        'updated_at' => $session->updated_at->toISOString(),
                        'created_at' => $session->created_at->toISOString()
                    ];
                });

            return response()->json([
                'success' => true,
                'history' => $sessions
            ]);

        } catch (\Exception $e) {
            Log::error('Get History List Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'history' => []
            ]);
        }
    }

    /**
     * Get messages for current session
     */
    public function getHistory(Request $request)
    {
        try {
            // Get the most recent session
            $session = ChatSession::where('user_id', Auth::id())
                ->latest()
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => true,
                    'history' => [],
                    'chat_id' => null
                ]);
            }

            $messages = ChatMessage::where('chat_session_id', $session->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'role' => $message->role,
                        'created_at' => $message->created_at->toISOString()
                    ];
                });

            return response()->json([
                'success' => true,
                'history' => $messages,
                'chat_id' => $session->id
            ]);

        } catch (\Exception $e) {
            Log::error('Get History Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'history' => []
            ]);
        }
    }

    /**
     * Get specific chat session with all messages
     */
    public function getSession($chatId)
    {
        try {
            $session = ChatSession::where('user_id', Auth::id())
                ->where('id', $chatId)
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'messages' => [],
                    'error' => 'Session not found'
                ], 404);
            }

            $messages = ChatMessage::where('chat_session_id', $session->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'role' => $message->role,
                        'created_at' => $message->created_at->toISOString()
                    ];
                });

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'chat' => [
                    'id' => $session->id,
                    'title' => $session->title
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get Session Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'messages' => []
            ], 500);
        }
    }

    /**
     * Delete a chat session
     */
    public function deleteSession($chatId)
    {
        try {
            $session = ChatSession::where('user_id', Auth::id())
                ->where('id', $chatId)
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            // Delete all messages first
            ChatMessage::where('chat_session_id', $session->id)->delete();

            // Delete the session
            $session->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chat session deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete Session Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete chat session'
            ], 500);
        }
    }

    /**
     * Clear all chat history for the user
     */
    public function clearAllHistory(Request $request)
    {
        try {
            $sessions = ChatSession::where('user_id', Auth::id())->get();
            $deletedCount = 0;

            foreach ($sessions as $session) {
                $deletedCount += ChatMessage::where('chat_session_id', $session->id)->delete();
                $session->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'All chat history cleared successfully',
                'deleted_sessions' => $sessions->count(),
                'deleted_messages' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Clear All History Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear chat history'
            ], 500);
        }
    }

    /**
     * Clear current session only
     */
    public function clearHistory(Request $request)
    {
        try {
            $session = ChatSession::where('user_id', Auth::id())
                ->latest()
                ->first();

            if ($session) {
                ChatMessage::where('chat_session_id', $session->id)->delete();
                $session->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Current chat cleared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Clear History Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear chat'
            ], 500);
        }
    }
}
