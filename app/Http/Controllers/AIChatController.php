<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AIChatService;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AIChatController extends Controller
{
    protected $aiService;
    protected $maxMessagesPerMinute = 20;
    protected $maxMessageLength = 2000;
    protected $minMessageLength = 1;

    public function __construct(AIChatService $aiService)
    {
        $this->aiService = $aiService;
        // Middleware is now handled in routes file for Laravel 11
        // Remove the middleware call from here
    }

    /**
     * Send a message to AI and get response
     */
    public function sendMessage(Request $request)
    {
        try {
            // Enhanced validation with custom messages
            $validated = $request->validate([
                'message' => 'required|string|max:' . $this->maxMessageLength . '|min:' . $this->minMessageLength,
            ], [
                'message.required' => 'Please enter a message.',
                'message.max' => 'Message cannot exceed ' . $this->maxMessageLength . ' characters.',
                'message.min' => 'Message must be at least ' . $this->minMessageLength . ' character.',
            ]);

            $user = Auth::user();

            // Check if user is authenticated
            if (!$user) {
                return $this->errorResponse('You must be logged in to use the chat.', 401);
            }

            // Check if user is active/banned (if you have this field)
            if (isset($user->is_banned) && $user->is_banned) {
                return $this->errorResponse('Your account has been restricted from using the chat.', 403);
            }

            // Rate limiting check
            $rateLimitResult = $this->checkRateLimit($user->id);
            if ($rateLimitResult !== true) {
                return $rateLimitResult;
            }

            // Log the request for debugging
            Log::info('Processing chat message', [
                'user_id' => $user->id,
                'message_length' => strlen($validated['message']),
                'message_preview' => substr($validated['message'], 0, 100)
            ]);

            // Get AI response with timeout protection
            $response = $this->getAIResponseWithTimeout($validated['message'], $user);

            // Hit the rate limiter on success
            RateLimiter::hit($this->getRateLimitKey($user->id), 60);

            // Return success response
            return $this->successResponse($response['message'], [
                'context' => $response['context'] ?? null,
                'remaining_attempts' => $this->getRemainingAttempts($user->id)
            ]);

        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed: ' . implode(', ', $e->errors()['message'] ?? ['Invalid input']), 422);
        } catch (\Exception $e) {
            Log::error('Chat Controller Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'message' => $request->message ?? null,
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return $this->errorResponse(
                "I'm having trouble connecting right now. Please try again in a moment.",
                500,
                config('app.debug') ? $e->getMessage() : null
            );
        }
    }

    /**
     * Get AI response with timeout protection
     */
    protected function getAIResponseWithTimeout($message, $user, $timeout = 30)
    {
        $cacheKey = "chat_response_{$user->id}_" . md5($message);

        return Cache::remember($cacheKey, 60, function () use ($message, $user) {
            return $this->aiService->getResponse($message, $user);
        });
    }

    /**
     * Check rate limit for user
     */
    protected function checkRateLimit($userId)
    {
        $rateLimitKey = $this->getRateLimitKey($userId);

        if (RateLimiter::tooManyAttempts($rateLimitKey, $this->maxMessagesPerMinute)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);

            return $this->errorResponse(
                "Too many messages. Please wait " . ($minutes > 1 ? "{$minutes} minutes" : "{$seconds} seconds") . " before sending more messages.",
                429,
                null,
                ['retry_after' => $seconds]
            );
        }

        return true;
    }

    /**
     * Get rate limit key for user
     */
    protected function getRateLimitKey($userId)
    {
        return 'chat:' . $userId;
    }

    /**
     * Get remaining attempts for user
     */
    protected function getRemainingAttempts($userId)
    {
        $rateLimitKey = $this->getRateLimitKey($userId);
        $maxAttempts = $this->maxMessagesPerMinute;
        $currentAttempts = RateLimiter::attempts($rateLimitKey);

        return max(0, $maxAttempts - $currentAttempts);
    }

    /**
     * Get chat history for the logged-in user
     */
    public function getHistory(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $limit = min((int) $request->input('limit', 50), 200); // Max 200 messages
            $contextType = $request->input('context_type');
            $offset = (int) $request->input('offset', 0);

            $query = ChatMessage::where('user_id', $user->id);

            if ($contextType) {
                $query->where('context_type', $contextType);
            }

            $total = $query->count();

            $history = $query->orderBy('created_at', 'desc')
                ->skip($offset)
                ->take($limit)
                ->get()
                ->reverse()
                ->values()
                ->map(function($message) {
                    return [
                        'id' => $message->id,
                        'role' => $message->role,
                        'message' => $message->message,
                        'context_type' => $message->context_type ?? 'general',
                        'created_at' => $message->created_at->toIso8601String(),
                        'formatted_time' => $message->created_at->format('g:i A'),
                        'formatted_date' => $message->created_at->format('M j, Y'),
                        'is_edited' => $message->updated_at != $message->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'history' => $history,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => ($offset + $limit) < $total
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get chat history: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve chat history', 500);
        }
    }

    /**
     * Clear all chat history for the logged-in user
     */
    public function clearHistory(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->errorResponse('Unauthorized', 401);
            }

            // Confirm action if requested
            if ($request->input('confirm') !== 'yes') {
                return $this->errorResponse('Confirmation required. Set confirm=yes to proceed.', 400);
            }

            $deleted = ChatMessage::where('user_id', $user->id)->delete();

            // Clear cache for this user
            Cache::forget("chat_response_{$user->id}_*");

            return response()->json([
                'success' => true,
                'message' => "Chat history cleared successfully. {$deleted} messages deleted.",
                'deleted_count' => $deleted
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to clear chat history: ' . $e->getMessage());
            return $this->errorResponse('Failed to clear chat history. Please try again.', 500);
        }
    }

    /**
     * Delete a specific message
     */
    public function deleteMessage($messageId)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $message = ChatMessage::where('user_id', $user->id)
                ->where('id', $messageId)
                ->firstOrFail();

            $message->delete();

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully.'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Message not found', 404);
        } catch (\Exception $e) {
            Log::error('Failed to delete message: ' . $e->getMessage());
            return $this->errorResponse('Could not delete message', 500);
        }
    }

    /**
     * Get quick suggestion buttons based on user role
     */
    public function getSuggestions(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $suggestions = $this->getSuggestionsByRole($user->role);

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions,
                'role' => $user->role,
                'timestamp' => now()->toIso8601String()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get suggestions: ' . $e->getMessage());
            return $this->errorResponse('Failed to load suggestions', 500);
        }
    }

    /**
     * Get suggestions based on user role
     */
    protected function getSuggestionsByRole($role)
    {
        $suggestions = [
            'admin' => [
                '📊 How many members joined this month?',
                '💰 What is the total revenue this month?',
                '📈 Show me class attendance statistics',
                '⭐ Which classes are most popular?',
                '🆕 Show me recent member signups',
                '🏋️ How many classes were held this week?',
                '💵 What are the total earnings for instructors?',
                '📅 Show me next week\'s schedule summary',
                '📊 Generate monthly performance report',
                '👥 List inactive members'
            ],
            'instructor' => [
                '📅 What classes do I have today?',
                '👥 How many students do I have total?',
                '💡 Tips for engaging students',
                '🏋️ Best warm-up exercises for my class',
                '💰 My earnings this month',
                '📊 Show my class attendance rates',
                '⭐ Most popular class I teach',
                '📈 How can I improve student retention?',
                '📝 Class preparation tips',
                '🎯 Goal setting for students'
            ],
            'member' => [
                '💪 What is my next workout?',
                '📋 Suggest a workout plan for beginners',
                '🥗 Healthy post-workout meal ideas',
                '🔥 How to stay motivated?',
                '🧘 Benefits of stretching',
                '📅 Show my upcoming classes',
                '🎯 Help me set fitness goals',
                '💧 How much water should I drink?',
                '😴 Importance of sleep for fitness',
                '📊 Track my progress'
            ],
            'default' => [
                '💪 How can I start working out?',
                '🥗 What are healthy eating tips?',
                '📅 How to book a class?',
                '🔥 How to stay motivated?',
                '🏋️ Benefits of regular exercise'
            ]
        ];

        return $suggestions[$role] ?? $suggestions['default'];
    }

    /**
     * Export chat history as JSON
     */
    public function exportHistory(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $format = $request->input('format', 'json');

            $history = ChatMessage::where('user_id', $user->id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($message) {
                    return [
                        'role' => $message->role,
                        'message' => $message->message,
                        'context_type' => $message->context_type ?? 'general',
                        'timestamp' => $message->created_at->toDateTimeString()
                    ];
                });

            $exportData = [
                'exported_at' => now()->toDateTimeString(),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'total_messages' => $history->count(),
                'chat_history' => $history
            ];

            $filename = "chat_history_{$user->id}_{$user->name}_{$user->role}_" . now()->format('Y-m-d_His');

            if ($format === 'csv') {
                return $this->exportAsCSV($exportData, $filename);
            }

            return response()->json($exportData, 200, [
                'Content-Disposition' => "attachment; filename={$filename}.json"
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to export chat history: ' . $e->getMessage());
            return $this->errorResponse('Failed to export chat history', 500);
        }
    }

    /**
     * Export as CSV
     */
    protected function exportAsCSV($data, $filename)
    {
        $callback = function() use ($data) {
            $handle = fopen('php://output', 'w');

            // Add headers
            fputcsv($handle, ['Role', 'Message', 'Context Type', 'Timestamp']);

            // Add rows
            foreach ($data['chat_history'] as $message) {
                fputcsv($handle, [
                    $message['role'],
                    $message['message'],
                    $message['context_type'],
                    $message['timestamp']
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}.csv"
        ]);
    }

    /**
     * Get chat statistics for the user
     */
    public function getStatistics(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->errorResponse('Unauthorized', 401);
            }

            // Cache statistics for 5 minutes
            $cacheKey = "chat_stats_{$user->id}";
            $statistics = Cache::remember($cacheKey, 300, function () use ($user) {
                return $this->calculateStatistics($user);
            });

            return response()->json([
                'success' => true,
                'statistics' => $statistics,
                'cached_at' => now()->toIso8601String()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get chat statistics: ' . $e->getMessage());
            return $this->errorResponse('Failed to load statistics', 500);
        }
    }

    /**
     * Calculate statistics for user
     */
    protected function calculateStatistics($user)
    {
        $totalMessages = ChatMessage::where('user_id', $user->id)->count();

        if ($totalMessages === 0) {
            return [
                'total_messages' => 0,
                'user_messages' => 0,
                'ai_messages' => 0,
                'messages_last_week' => 0,
                'context_breakdown' => [],
                'daily_stats' => [],
                'first_chat_date' => null,
                'last_chat_date' => null,
                'has_chat_history' => false,
                'average_message_length' => 0
            ];
        }

        $userMessages = ChatMessage::where('user_id', $user->id)->where('role', 'user')->count();
        $aiMessages = ChatMessage::where('user_id', $user->id)->where('role', 'assistant')->count();

        $lastWeekMessages = ChatMessage::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // Average message length
        $averageLength = ChatMessage::where('user_id', $user->id)
            ->where('role', 'user')
            ->select(DB::raw('AVG(LENGTH(message)) as avg_length'))
            ->value('avg_length') ?? 0;

        // Context breakdown
        $contextBreakdown = ChatMessage::where('user_id', $user->id)
            ->select('context_type', DB::raw('count(*) as count'))
            ->whereNotNull('context_type')
            ->groupBy('context_type')
            ->get()
            ->map(fn($item) => [
                'context_type' => $item->context_type ?? 'general',
                'count' => (int) $item->count,
                'percentage' => round(($item->count / $totalMessages) * 100, 1)
            ]);

        $firstMessage = ChatMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->first();

        $lastMessage = ChatMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // Daily stats for last 7 days
        $dailyStats = ChatMessage::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(fn($item) => [
                'date' => $item->date,
                'count' => (int) $item->count
            ]);

        return [
            'total_messages' => $totalMessages,
            'user_messages' => $userMessages,
            'ai_messages' => $aiMessages,
            'messages_last_week' => $lastWeekMessages,
            'context_breakdown' => $contextBreakdown,
            'daily_stats' => $dailyStats,
            'first_chat_date' => $firstMessage ? $firstMessage->created_at->format('M j, Y') : null,
            'last_chat_date' => $lastMessage ? $lastMessage->created_at->format('M j, Y g:i A') : null,
            'has_chat_history' => true,
            'average_message_length' => round($averageLength, 0)
        ];
    }

    /**
     * Get a specific chat message
     */
    public function getMessage($messageId)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->errorResponse('Unauthorized', 401);
            }

            $message = ChatMessage::where('user_id', $user->id)
                ->where('id', $messageId)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'role' => $message->role,
                    'content' => $message->message,
                    'context_type' => $message->context_type,
                    'created_at' => $message->created_at->toIso8601String(),
                    'formatted_time' => $message->created_at->format('g:i A'),
                    'formatted_date' => $message->created_at->format('M j, Y')
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Message not found', 404);
        } catch (\Exception $e) {
            return $this->errorResponse('Could not retrieve message', 500);
        }
    }

    /**
     * Standard success response
     */
    protected function successResponse($message, $extra = [])
    {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
            'timestamp' => now()->toIso8601String()
        ], $extra));
    }

    /**
     * Standard error response
     */
    protected function errorResponse($message, $code = 400, $debug = null, $extra = [])
    {
        $response = array_merge([
            'success' => false,
            'error' => $message,
            'message' => $message,
            'timestamp' => now()->toIso8601String()
        ], $extra);

        if ($debug && config('app.debug')) {
            $response['debug_message'] = $debug;
        }

        return response()->json($response, $code);
    }
}
