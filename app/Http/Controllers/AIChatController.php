<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Booking;
use App\Models\ScheduledClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AIChatController extends Controller
{
    /**
     * Send a message and get AI response
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:2000|min:1',
                'session_id' => 'nullable|exists:chat_sessions,id'
            ]);

            $user = Auth::user();
            $message = trim($request->message);
            $sessionId = $request->session_id;

            // Get or create session
            $session = $this->getOrCreateSession($user, $sessionId);

            // Save user message
            $userMessage = ChatMessage::create([
                'user_id' => $user->id,
                'chat_session_id' => $session->id,
                'role' => 'user',
                'message' => $message,
                'created_at' => now()
            ]);

            // Update session stats
            $session->increment('message_count');
            $session->update(['last_message_at' => now()]);

            // Get AI response
            $aiResponse = $this->generateAIResponse($user, $session, $message);

            // Save AI response
            $aiMessage = ChatMessage::create([
                'user_id' => $user->id,
                'chat_session_id' => $session->id,
                'role' => 'assistant',
                'message' => $aiResponse,
                'created_at' => now()
            ]);

            // Update session stats again
            $session->increment('message_count');

            // Update title if needed
            if ($session->message_count <= 2) {
                $session->update(['title' => Str::limit($message, 40)]);
            }

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'message' => $aiResponse,
                'message_id' => $aiMessage->id,
                'message_count' => $session->message_count
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors()['message'] ?? ['Invalid input'])
            ], 422);
        } catch (\Exception $e) {
            Log::error('Send Message Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry, I encountered an error. Please try again.'
            ], 500);
        }
    }

    /**
     * Get or create chat session
     */
    private function getOrCreateSession($user, $sessionId)
    {
        if ($sessionId) {
            $session = ChatSession::where('user_id', $user->id)
                ->where('id', $sessionId)
                ->first();
            if ($session) {
                return $session;
            }
        }

        // Check for empty session to reuse
        $emptySession = ChatSession::where('user_id', $user->id)
            ->where('message_count', 0)
            ->latest()
            ->first();

        if ($emptySession) {
            return $emptySession;
        }

        // Create new session
        return ChatSession::create([
            'user_id' => $user->id,
            'title' => 'New Conversation',
            'last_message_at' => now(),
            'message_count' => 0,
            'is_active' => true
        ]);
    }

    /**
     * Generate AI response
     */
    private function generateAIResponse($user, $session, $message)
    {
        try {
            // Get conversation history
            $history = $this->getConversationHistory($session->id);

            // Get user context
            $userContext = $this->getUserContext($user);

            // Build system prompt
            $systemPrompt = $this->buildSystemPrompt($user, $userContext);

            // Try Groq API first
            $groqApiKey = env('GROQ_API_KEY');

            if ($groqApiKey && $groqApiKey !== 'your_groq_api_key_here') {
                $response = $this->callGroqAPI($systemPrompt, $history, $message, $groqApiKey);
                if ($response) {
                    return $response;
                }
            }

            // Fallback response
            return $this->getFallbackResponse($message, $user, $userContext);

        } catch (\Exception $e) {
            Log::error('AI Generation Error: ' . $e->getMessage());
            return $this->getFallbackResponse($message, $user, []);
        }
    }

    /**
     * Call Groq API
     */
    private function callGroqAPI($systemPrompt, $history, $message, $apiKey)
    {
        try {
            $messages = array_merge(
                [['role' => 'system', 'content' => $systemPrompt]],
                $history,
                [['role' => 'user', 'content' => $message]]
            );

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'mixtral-8x7b-32768',
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'];
            }
        } catch (\Exception $e) {
            Log::error('Groq API error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get conversation history
     */
    private function getConversationHistory($sessionId, $limit = 10)
    {
        $messages = ChatMessage::where('chat_session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        return $messages->map(function ($msg) {
            return [
                'role' => $msg->role,
                'content' => $msg->message
            ];
        })->toArray();
    }

    /**
     * Get user context
     */
    private function getUserContext($user)
    {
        $context = [
            'role' => $user->role,
            'name' => $user->name,
            'email' => $user->email,
            'member_since' => $user->created_at->format('F Y')
        ];

        if ($user->role === 'member') {
            $bookings = Booking::where('user_id', $user->id)
                ->with('scheduledClass')
                ->get();

            $context['member_data'] = [
                'total_bookings' => $bookings->count(),
                'upcoming_bookings' => $bookings->where('date', '>=', now())->count(),
                'past_bookings' => $bookings->where('date', '<', now())->count()
            ];
        }

        if ($user->role === 'instructor') {
            $classes = ScheduledClass::where('instructor_id', $user->id)->get();

            $context['instructor_data'] = [
                'total_classes' => $classes->count(),
                'upcoming_classes' => $classes->where('date_time', '>=', now())->count()
            ];
        }

        if ($user->role === 'admin') {
            $context['admin_data'] = [
                'total_members' => User::where('role', 'member')->count(),
                'total_instructors' => User::where('role', 'instructor')->count(),
                'total_classes' => ScheduledClass::count()
            ];
        }

        return $context;
    }

    /**
     * Build system prompt
     */
    private function buildSystemPrompt($user, $userContext)
    {
        $rolePrompts = [
            'admin' => "You are helping a GYM ADMINISTRATOR. Focus on business analytics, member management, revenue tracking, and strategic decisions.",
            'instructor' => "You are helping a FITNESS INSTRUCTOR. Focus on class management, student engagement, professional development, and earnings tracking.",
            'member' => "You are helping a GYM MEMBER. Focus on personal fitness, workout plans, nutrition advice, class bookings, and motivation."
        ];

        $rolePrompt = $rolePrompts[$user->role] ?? "You are helping a gym user with fitness-related questions.";

        return "You are MyGym AI, an intelligent fitness assistant.\n\n" .
               $rolePrompt . "\n\n" .
               "User: {$user->name} (Member since {$userContext['member_since']})\n" .
               "Role: {$user->role}\n\n" .
               "Keep responses concise (under 500 words), friendly, and helpful. Use emojis and bullet points for better readability. " .
               "Be professional and encouraging. If you don't know something, say so politely.";
    }

    /**
     * Get fallback response
     */
    private function getFallbackResponse($message, $user, $userContext)
    {
        $lowerMessage = strtolower($message);
        $role = $user->role;

        // Member responses
        if ($role === 'member') {
            if (str_contains($lowerMessage, 'workout') || str_contains($lowerMessage, 'exercise')) {
                return "💪 **Workout Recommendation**\n\nBased on your fitness level, I recommend:\n• Monday: Cardio (30 min)\n• Wednesday: Strength Training\n• Friday: HIIT (20 min)\n• Weekend: Active Recovery\n\nWant a detailed plan?";
            }

            if (str_contains($lowerMessage, 'class') || str_contains($lowerMessage, 'book')) {
                $upcoming = $userContext['member_data']['upcoming_bookings'] ?? 0;
                if ($upcoming > 0) {
                    return "📅 You have {$upcoming} upcoming class" . ($upcoming != 1 ? 'es' : '') . " scheduled!\n\nCheck your dashboard for details.";
                }
                return "📚 **Available Classes**\n\n• Yoga 🧘 - Today 6PM\n• Pilates 💪 - Tomorrow 8AM\n• HIIT 🔥 - Wed 7PM\n\nBook a class from the Classes section!";
            }

            if (str_contains($lowerMessage, 'motivation')) {
                return "🔥 **Stay Motivated!**\n\n• Every workout brings you closer to your goals\n• You're stronger than you think\n• Progress, not perfection\n• Take it one day at a time\n\nYou've got this! 💪";
            }
        }

        // Instructor responses
        if ($role === 'instructor') {
            if (str_contains($lowerMessage, 'class') || str_contains($lowerMessage, 'schedule')) {
                return "📅 **Your Classes**\n\n• Today: Check your schedule\n• This week: " . ($userContext['instructor_data']['upcoming_classes'] ?? 0) . " classes\n• Total taught: " . ($userContext['instructor_data']['total_classes'] ?? 0) . "\n\nView full schedule in Instructor Panel!";
            }

            if (str_contains($lowerMessage, 'earning') || str_contains($lowerMessage, 'payment')) {
                return "💰 **Earnings Summary**\n\n• Check Instructor Dashboard for detailed earnings\n• Payouts processed monthly\n• View transaction history in your profile\n\nNeed specific figures? Visit your Earnings page!";
            }
        }

        // Admin responses
        if ($role === 'admin') {
            if (str_contains($lowerMessage, 'member') || str_contains($lowerMessage, 'analytics')) {
                $total = $userContext['admin_data']['total_members'] ?? 0;
                return "📊 **Member Analytics**\n\n• Total members: {$total}\n• Active this month: View dashboard\n• New signups: Check reports\n\nAccess Admin Dashboard for detailed analytics!";
            }

            if (str_contains($lowerMessage, 'revenue') || str_contains($lowerMessage, 'earning')) {
                return "💰 **Revenue Overview**\n\n• Monthly revenue: View Reports\n• Instructor payouts: Check Earnings\n• Financial summary in Admin Panel\n\nGenerate detailed report from dashboard!";
            }
        }

        // Default response
        $greetings = [
            'admin' => "👋 Welcome back, Administrator! I can help with:\n• 📊 Member analytics\n• 💰 Revenue reports\n• 👨‍🏫 Instructor management",
            'instructor' => "👋 Hello Instructor! I can help with:\n• 📅 Class schedule\n• 👥 Student engagement\n• 💰 Earnings tracking",
            'member' => "👋 Hey {$user->name}! I can help with:\n• 💪 Workout plans\n• 📅 Class bookings\n• 🥗 Nutrition advice\n• 🎯 Goal setting"
        ];

        return $greetings[$role] ?? "👋 Hi! How can I help with your fitness journey today?";
    }

    /**
     * Get all chat sessions
     */
    public function getSessions(Request $request)
    {
        try {
            $sessions = ChatSession::where('user_id', Auth::id())
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($session) {
                    $lastMessage = ChatMessage::where('chat_session_id', $session->id)
                        ->where('role', 'user')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    return [
                        'id' => $session->id,
                        'title' => $session->title ?: 'Conversation',
                        'preview' => $lastMessage ? Str::limit($lastMessage->message, 60) : 'No messages yet',
                        'message_count' => $session->message_count,
                        'created_at' => $session->created_at->toISOString(),
                        'updated_at' => $session->updated_at->toISOString(),
                        'last_activity' => $session->updated_at->diffForHumans()
                    ];
                });

            return response()->json([
                'success' => true,
                'sessions' => $sessions
            ]);
        } catch (\Exception $e) {
            Log::error('Get Sessions Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'sessions' => [],
                'message' => 'Failed to load sessions'
            ]);
        }
    }

    /**
     * Get current session
     */
    public function getCurrentSession(Request $request)
    {
        try {
            $session = ChatSession::where('user_id', Auth::id())
                ->latest()
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => true,
                    'messages' => [],
                    'session_id' => null,
                    'has_history' => false
                ]);
            }

            $messages = ChatMessage::where('chat_session_id', $session->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'role' => $message->role,
                        'message' => $message->message,
                        'created_at' => $message->created_at->toISOString(),
                        'formatted_time' => $message->created_at->format('g:i A'),
                        'formatted_date' => $message->created_at->format('M j, Y')
                    ];
                });

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'session_id' => $session->id,
                'session_title' => $session->title,
                'has_history' => $messages->count() > 0
            ]);
        } catch (\Exception $e) {
            Log::error('Get Current Session Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'messages' => [],
                'session_id' => null,
                'has_history' => false,
                'message' => 'Failed to load current session'
            ]);
        }
    }

    /**
     * Get specific session
     */
    public function getSession($id)
    {
        try {
            $session = ChatSession::where('user_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();

            $messages = ChatMessage::where('chat_session_id', $session->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'role' => $message->role,
                        'message' => $message->message,
                        'created_at' => $message->created_at->toISOString(),
                        'formatted_time' => $message->created_at->format('g:i A'),
                        'formatted_date' => $message->created_at->format('M j, Y')
                    ];
                });

            return response()->json([
                'success' => true,
                'session' => [
                    'id' => $session->id,
                    'title' => $session->title,
                    'created_at' => $session->created_at->toISOString(),
                    'updated_at' => $session->updated_at->toISOString()
                ],
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            Log::error('Get Session Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found',
                'messages' => []
            ], 404);
        }
    }

    /**
     * Create new session
     */
    public function createSession(Request $request)
    {
        try {
            // Check for empty session first
            $emptySession = ChatSession::where('user_id', Auth::id())
                ->where('message_count', 0)
                ->latest()
                ->first();

            if ($emptySession) {
                return response()->json([
                    'success' => true,
                    'session' => [
                        'id' => $emptySession->id,
                        'title' => $emptySession->title,
                        'created_at' => $emptySession->created_at->toISOString()
                    ],
                    'message' => 'Using existing conversation'
                ]);
            }

            $session = ChatSession::create([
                'user_id' => Auth::id(),
                'title' => 'New Conversation',
                'last_message_at' => now(),
                'message_count' => 0,
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'session' => [
                    'id' => $session->id,
                    'title' => $session->title,
                    'created_at' => $session->created_at->toISOString()
                ],
                'message' => 'New conversation created'
            ]);
        } catch (\Exception $e) {
            Log::error('Create Session Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create new conversation'
            ], 500);
        }
    }

    /**
     * Delete session with beautiful response
     */
    public function deleteSession($id)
    {
        try {
            $session = ChatSession::where('user_id', Auth::id())
                ->where('id', $id)
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found',
                    'toast_type' => 'error'
                ], 404);
            }

            $messageCount = ChatMessage::where('chat_session_id', $session->id)->count();
            $sessionTitle = $session->title ?: 'Conversation';

            // Delete all messages first
            ChatMessage::where('chat_session_id', $session->id)->delete();

            // Delete the session
            $session->delete();

            return response()->json([
                'success' => true,
                'message' => "✓ '{$sessionTitle}' has been deleted successfully",
                'toast_type' => 'success',
                'deleted_messages' => $messageCount,
                'session_title' => $sessionTitle
            ]);

        } catch (\Exception $e) {
            Log::error('Delete Session Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete conversation. Please try again.',
                'toast_type' => 'error'
            ], 500);
        }
    }

    /**
     * Clear all history with beautiful response
     */
    public function clearAllHistory(Request $request)
    {
        try {
            $sessions = ChatSession::where('user_id', Auth::id())->get();

            if ($sessions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No conversations to clear',
                    'toast_type' => 'info'
                ]);
            }

            $deletedSessions = 0;
            $deletedMessages = 0;

            foreach ($sessions as $session) {
                $messageCount = ChatMessage::where('chat_session_id', $session->id)->count();
                ChatMessage::where('chat_session_id', $session->id)->delete();
                $session->delete();

                $deletedSessions++;
                $deletedMessages += $messageCount;
            }

            return response()->json([
                'success' => true,
                'message' => "✓ All chat history cleared! Removed {$deletedSessions} conversation(s) with {$deletedMessages} message(s).",
                'toast_type' => 'success',
                'deleted_sessions' => $deletedSessions,
                'deleted_messages' => $deletedMessages
            ]);

        } catch (\Exception $e) {
            Log::error('Clear All History Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear chat history. Please try again.',
                'toast_type' => 'error'
            ], 500);
        }
    }

    /**
     * Get role-based suggestions
     */
    public function getSuggestions(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        $suggestions = [
            'admin' => [
                '📊 Show member analytics',
                '💰 View revenue reports',
                '👨‍🏫 Instructor performance',
                '📈 Monthly growth stats',
                '🆕 Recent signups',
                '⭐ Popular classes report'
            ],
            'instructor' => [
                '📅 What classes do I have today?',
                '👥 How many students do I have?',
                '💰 My earnings this month',
                '📊 Show my class attendance',
                '⭐ Most popular class',
                '💡 Teaching tips'
            ],
            'member' => [
                '💪 What is my next workout?',
                '📋 Suggest a workout plan',
                '🥗 Healthy meal ideas',
                '🔥 How to stay motivated?',
                '📅 Show my upcoming classes',
                '🎯 Help me set fitness goals'
            ]
        ];

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions[$role] ?? $suggestions['member'],
            'role' => $role
        ]);
    }

    /**
     * Export chat history
     */
    public function exportHistory(Request $request)
    {
        try {
            $user = Auth::user();
            $sessions = ChatSession::where('user_id', $user->id)
                ->with('messages')
                ->orderBy('created_at', 'desc')
                ->get();

            $export = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'export_date' => now()->toISOString(),
                'total_conversations' => $sessions->count(),
                'total_messages' => $sessions->sum(function($session) {
                    return $session->messages->count();
                }),
                'conversations' => $sessions->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'title' => $session->title,
                        'created_at' => $session->created_at->toISOString(),
                        'last_message_at' => $session->last_message_at,
                        'message_count' => $session->message_count,
                        'messages' => $session->messages->map(function ($message) {
                            return [
                                'role' => $message->role,
                                'content' => $message->message,
                                'timestamp' => $message->created_at->toISOString()
                            ];
                        })
                    ];
                })
            ];

            $filename = 'chat_history_' . $user->id . '_' . date('Y-m-d_His') . '.json';

            return response()->json($export, 200, [
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Type' => 'application/json'
            ]);

        } catch (\Exception $e) {
            Log::error('Export History Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to export chat history'
            ], 500);
        }
    }

    /**
     * Legacy method - Get history list (alias for getSessions)
     */
    public function getHistoryList(Request $request)
    {
        return $this->getSessions($request);
    }

    /**
     * Legacy method - Get history (alias for getCurrentSession)
     */
    public function getHistory(Request $request)
    {
        return $this->getCurrentSession($request);
    }

    /**
     * Legacy method - Clear history (alias for clearAllHistory)
     */
    public function clearHistory(Request $request)
    {
        return $this->clearAllHistory($request);
    }
}
