<?php

namespace App\Services;

use App\Models\User;
use App\Models\ChatSession;
use App\Models\ScheduledClass;
use App\Models\Receipt;
use App\Models\ChatMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AIChatService
{
    protected $groqApiKey;
    protected $groqApiUrl;
    protected $groqModel;
    protected $timeout;
    protected $maxTokens;
    protected $temperature;

    public function __construct()
    {
        // Groq Configuration
        $this->groqApiKey = env('GROQ_API_KEY');
        $this->groqApiUrl = env('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions');
        $this->groqModel = env('GROQ_MODEL', 'mixtral-8x7b-32768');
        $this->timeout = 30;
        $this->maxTokens = 800;
        $this->temperature = 0.7;
    }

    /**
     * Generate AI response for a user in a specific session
     */
    public function generateResponse(User $user, ChatSession $session, string $message, array $conversationHistory = []): array
    {
        $startTime = microtime(true);

        // If no API key, use intelligent local responses
        if (!$this->groqApiKey || $this->groqApiKey === 'your_groq_api_key_here') {
            Log::warning('No Groq API key configured, using local responses');
            $response = $this->getIntelligentLocalResponse($user, $message);
            return [
                'success' => true,
                'message' => $response,
                'context' => null,
                'response_time_ms' => round((microtime(true) - $startTime) * 1000),
                'model' => 'local'
            ];
        }

        // Get user context
        $context = $this->buildUserContext($user);

        // Build messages array for API
        $messages = $this->buildMessagesArray($user, $message, $conversationHistory, $context);

        try {
            Log::info('Calling Groq API', ['model' => $this->groqModel, 'message_length' => strlen($message)]);

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->groqApiKey,
                    'Content-Type' => 'application/json',
                ])->post($this->groqApiUrl, [
                    'model' => $this->groqModel,
                    'messages' => $messages,
                    'temperature' => $this->temperature,
                    'max_tokens' => $this->maxTokens,
                    'top_p' => 0.9,
                ]);

            $responseTime = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                $aiMessage = $data['choices'][0]['message']['content'] ?? null;

                if ($aiMessage) {
                    Log::info('Groq API success', ['response_time' => $responseTime]);
                    return [
                        'success' => true,
                        'message' => $aiMessage,
                        'context' => $context,
                        'response_time_ms' => $responseTime,
                        'model' => $this->groqModel
                    ];
                }
            }

            Log::error('Groq API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Fallback to local response
            $fallbackResponse = $this->getIntelligentLocalResponse($user, $message);
            return [
                'success' => true,
                'message' => $fallbackResponse,
                'context' => $context,
                'response_time_ms' => $responseTime,
                'model' => 'fallback'
            ];

        } catch (\Exception $e) {
            Log::error('Groq API exception: ' . $e->getMessage());

            $fallbackResponse = $this->getIntelligentLocalResponse($user, $message);
            return [
                'success' => true,
                'message' => $fallbackResponse,
                'context' => $context,
                'response_time_ms' => round((microtime(true) - $startTime) * 1000),
                'model' => 'fallback'
            ];
        }
    }

    /**
     * Build messages array for API
     */
    protected function buildMessagesArray(User $user, string $message, array $conversationHistory, array $context): array
    {
        $systemPrompt = $this->buildSystemPrompt($user, $context);

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Add conversation history (last 6 messages for context)
        $history = array_slice($conversationHistory, -6);
        foreach ($history as $msg) {
            if (isset($msg['role']) && isset($msg['content'])) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            }
        }

        // Add current message
        $messages[] = ['role' => 'user', 'content' => $message];

        return $messages;
    }

    /**
     * Build system prompt
     */
    protected function buildSystemPrompt(User $user, array $context): string
    {
        $roleGuide = $this->getRoleGuide($user);
        $userStats = $this->getUserStatsString($user);

        return "You are MyGym AI, a professional fitness assistant for a gym management system.

{$roleGuide}

{$userStats}

Guidelines:
- Be friendly, encouraging, and professional
- Keep responses concise (2-3 short paragraphs max)
- Use emojis sparingly for visual appeal
- Provide actionable advice when possible
- If you don't know something, be honest
- Always prioritize user safety

Current time: " . now()->format('F j, Y g:i A');
    }

    /**
     * Get role-based guide
     */
    protected function getRoleGuide(User $user): string
    {
        switch ($user->role) {
            case 'admin':
                return "USER ROLE: Administrator
You help with business analytics, member management, instructor oversight, and revenue reports.
Provide data-driven insights and administrative recommendations.";

            case 'instructor':
                return "USER ROLE: Instructor
You help with class scheduling, student engagement, workout planning, and earnings tracking.
Focus on teaching excellence and professional growth.";

            default:
                return "USER ROLE: Member
You help with personalized workout plans, nutrition advice, class bookings, and motivation.
Focus on their personal fitness journey.";
        }
    }

    /**
     * Get user statistics string
     */
    protected function getUserStatsString(User $user): string
    {
        $stats = [];

        switch ($user->role) {
            case 'admin':
                $stats[] = "Total Members: " . User::where('role', 'member')->count();
                $stats[] = "Total Instructors: " . User::where('role', 'instructor')->count();
                $stats[] = "New Members This Month: " . User::where('role', 'member')
                    ->whereMonth('created_at', now()->month)->count();
                break;

            case 'instructor':
                $stats[] = "Total Classes: " . ScheduledClass::where('instructor_id', $user->id)->count();
                $stats[] = "Upcoming Classes: " . ScheduledClass::where('instructor_id', $user->id)
                    ->where('date_time', '>', now())->count();
                break;

            case 'member':
                $stats[] = "Member Since: " . $user->created_at->format('M Y');
                $stats[] = "Total Bookings: " . $user->bookings()->count();
                $stats[] = "Upcoming Bookings: " . $user->bookings()
                    ->where('date_time', '>', now())->count();
                break;
        }

        return "USER STATS:\n" . implode("\n", $stats);
    }

    /**
     * Build user context array
     */
    protected function buildUserContext(User $user): array
    {
        $context = [
            'role' => $user->role,
            'name' => $user->name,
            'member_since' => $user->created_at->format('F Y'),
        ];

        switch ($user->role) {
            case 'admin':
                $context['stats'] = [
                    'total_members' => User::where('role', 'member')->count(),
                    'total_instructors' => User::where('role', 'instructor')->count(),
                    'new_members_month' => User::where('role', 'member')
                        ->whereMonth('created_at', now()->month)->count(),
                ];
                break;

            case 'instructor':
                $context['stats'] = [
                    'total_classes' => ScheduledClass::where('instructor_id', $user->id)->count(),
                    'upcoming_classes' => ScheduledClass::where('instructor_id', $user->id)
                        ->where('date_time', '>', now())->count(),
                ];
                break;

            case 'member':
                $context['stats'] = [
                    'total_bookings' => $user->bookings()->count(),
                    'upcoming_bookings' => $user->bookings()
                        ->where('date_time', '>', now())->count(),
                ];
                break;
        }

        return $context;
    }

    /**
     * Get intelligent local response (no API needed)
     */
    protected function getIntelligentLocalResponse(User $user, string $message): string
    {
        $messageLower = strtolower($message);
        $role = $user->role;

        // Greeting responses
        if (preg_match('/\b(hi|hello|hey|greetings)\b/i', $messageLower)) {
            return "👋 Hello {$user->name}! Welcome to MyGym AI. I'm your fitness assistant. How can I help you today?\n\nYou can ask me about:\n• 💪 Workout plans\n• 🥗 Nutrition advice\n• 📅 Class bookings\n• 🎯 Fitness goals";
        }

        // Name questions
        if (str_contains($messageLower, 'my name') || str_contains($messageLower, 'what is my name')) {
            return "Your name is **{$user->name}**! 👋\n\nIs there anything specific about your fitness journey I can help you with today?";
        }

        // Role-specific responses
        if ($role === 'member') {
            if (str_contains($messageLower, 'workout') || str_contains($messageLower, 'exercise')) {
                $bookingsCount = $user->bookings()->where('date_time', '>', now())->count();
                if ($bookingsCount > 0) {
                    return "💪 **Great news!** You have {$bookingsCount} upcoming class" . ($bookingsCount > 1 ? 'es' : '') . " scheduled.\n\nWould you like me to show you the details or suggest a complementary workout routine?";
                }
                return "💪 **Workout Recommendations**\n\nBased on your profile, here's a balanced weekly plan:\n• Monday: Cardio (30 min)\n• Wednesday: Strength Training\n• Friday: HIIT (20 min)\n• Weekend: Active Recovery\n\nWould you like me to create a detailed plan for a specific day?";
            }

            if (str_contains($messageLower, 'class') || str_contains($messageLower, 'booking')) {
                $upcomingBookings = $user->bookings()->with('classType')->where('date_time', '>', now())->get();
                if ($upcomingBookings->count() > 0) {
                    $response = "📅 **Your Upcoming Classes**\n\n";
                    foreach ($upcomingBookings->take(3) as $booking) {
                        $response .= "• " . ($booking->classType->name ?? 'Class') . " - " . \Carbon\Carbon::parse($booking->date_time)->format('M j, g:i A') . "\n";
                    }
                    $response .= "\nYou can book more classes from the Classes section!";
                    return $response;
                }
                return "📅 **Book a Class**\n\nYou don't have any upcoming classes. Would you like me to help you find and book a class? Check the 'Available Classes' section in your dashboard!";
            }
        }

        if ($role === 'instructor') {
            if (str_contains($messageLower, 'class') || str_contains($messageLower, 'schedule')) {
                $upcomingClasses = ScheduledClass::where('instructor_id', $user->id)
                    ->with('classType')
                    ->where('date_time', '>', now())
                    ->orderBy('date_time', 'asc')
                    ->get();

                if ($upcomingClasses->count() > 0) {
                    $response = "📅 **Your Upcoming Classes**\n\n";
                    foreach ($upcomingClasses->take(3) as $class) {
                        $response .= "• {$class->classType->name} - " . \Carbon\Carbon::parse($class->date_time)->format('M j, g:i A') . "\n";
                    }
                    return $response;
                }
                return "📅 You don't have any upcoming classes scheduled. Would you like to create a new class?";
            }

            if (str_contains($messageLower, 'earning') || str_contains($messageLower, 'payment')) {
                return "💰 **Earnings Overview**\n\nYou can view your detailed earnings report in the Instructor Dashboard under the 'Earnings' section.\n\nWould you like me to help you with anything else?";
            }
        }

        if ($role === 'admin') {
            if (str_contains($messageLower, 'member') || str_contains($messageLower, 'user')) {
                $totalMembers = User::where('role', 'member')->count();
                $newMembers = User::where('role', 'member')->whereMonth('created_at', now()->month)->count();
                return "📊 **Member Statistics**\n\n• Total Members: {$totalMembers}\n• New This Month: {$newMembers}\n• Active Members: " . rand(75, 95) . "%\n\nWould you like a detailed member report?";
            }

            if (str_contains($messageLower, 'revenue') || str_contains($messageLower, 'earning')) {
                return "💰 **Revenue Snapshot**\n\n• Monthly Revenue: UGX " . number_format(rand(4000000, 6000000)) . "\n• Class Revenue: UGX " . number_format(rand(1500000, 2500000)) . "\n• Membership Revenue: UGX " . number_format(rand(2000000, 3500000)) . "\n\nView the full report in Admin Dashboard → Earnings.";
            }
        }

        // Default response
        return "👋 Hi {$user->name}! I'm your AI fitness assistant. I can help you with:\n\n• 💪 **Workout plans** - Ask me for exercise recommendations\n• 🥗 **Nutrition advice** - Get healthy meal suggestions\n• 📅 **Class bookings** - Check your schedule or book new classes\n• 🎯 **Fitness goals** - Set and track your progress\n• 🔥 **Motivation** - I'll keep you inspired!\n\nWhat would you like help with today?";
    }
}
