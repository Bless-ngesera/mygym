<?php

namespace App\Services;

use App\Models\User;
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
    protected $openRouterApiKey;
    protected $openRouterApiUrl;
    protected $openRouterModel;
    protected $timeout;
    protected $maxTokens;
    protected $temperature;

    public function __construct()
    {
        // Groq Configuration (Primary - Fast & Free)
        $this->groqApiKey = env('GROQ_API_KEY');
        $this->groqApiUrl = env('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions');
        $this->groqModel = env('GROQ_MODEL', 'llama3-8b-8192');

        // OpenRouter Configuration (Fallback)
        $this->openRouterApiKey = env('OPENROUTER_API_KEY');
        $this->openRouterApiUrl = env('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions');
        $this->openRouterModel = env('OPENROUTER_MODEL', 'mistralai/mistral-7b-instruct:free');

        // API Settings
        $this->timeout = 30;
        $this->maxTokens = 800;
        $this->temperature = 0.7;
    }

    public function getResponse($userMessage, User $user)
    {
        // Get user context based on role with enhanced data
        $context = $this->buildEnhancedContext($user);

        // Get recent chat history for context (last 10 messages)
        $chatHistory = $this->getChatHistory($user, 10);

        // Build enhanced prompt with better structure
        $prompt = $this->buildEnhancedPrompt($userMessage, $context, $chatHistory);

        // Try to get AI response with smart fallback
        $response = $this->getAIResponseWithFallback($prompt);

        // Clean and enhance the response
        $response = $this->enhanceResponse($response);

        // Store messages
        $this->storeMessage($user->id, 'user', $userMessage);
        $this->storeMessage($user->id, 'assistant', $response);

        return [
            'success' => true,
            'message' => $response,
            'context' => $context
        ];
    }

    protected function getAIResponseWithFallback($prompt)
    {
        // Check if API keys are configured
        if (empty($this->groqApiKey) && empty($this->openRouterApiKey)) {
            Log::warning('No API keys configured for AI Chat');
            return $this->getIntelligentLocalResponse($prompt);
        }

        // Try Groq API first
        if (!empty($this->groqApiKey)) {
            try {
                Log::info('Attempting Groq API request with model: ' . $this->groqModel);
                $response = $this->callGroqAPI($prompt);
                if ($response && strlen($response) > 10) {
                    Log::info('Groq API succeeded');
                    return $response;
                }
            } catch (\Exception $e) {
                Log::error('Groq API failed: ' . $e->getMessage());
            }
        }

        // Try OpenRouter as fallback
        if (!empty($this->openRouterApiKey)) {
            try {
                Log::info('Attempting OpenRouter API fallback');
                $response = $this->callOpenRouterAPI($prompt);
                if ($response && strlen($response) > 10) {
                    Log::info('OpenRouter API succeeded');
                    return $response;
                }
            } catch (\Exception $e) {
                Log::error('OpenRouter API failed: ' . $e->getMessage());
            }
        }

        // Both APIs failed - use intelligent local responses
        Log::warning('Both APIs failed, using intelligent local responses');
        return $this->getIntelligentLocalResponse($prompt);
    }

    protected function callGroqAPI($prompt)
    {
        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->groqApiKey,
                'Content-Type' => 'application/json',
            ])->post($this->groqApiUrl, [
                'model' => $this->groqModel,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are MyGym AI, a knowledgeable fitness and gym management assistant. Provide helpful, accurate, and engaging responses. Use emojis appropriately. Keep responses concise but informative.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => $this->temperature,
                'max_tokens' => $this->maxTokens,
                'top_p' => 0.9,
                'frequency_penalty' => 0.5,
                'presence_penalty' => 0.5,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Groq API Error: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();

        if (isset($data['choices'][0]['message']['content'])) {
            return trim($data['choices'][0]['message']['content']);
        }

        throw new \Exception('Invalid Groq API response format');
    }

    protected function callOpenRouterAPI($prompt)
    {
        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->openRouterApiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => url('/'),
                'X-Title' => 'MyGym AI Assistant',
            ])->post($this->openRouterApiUrl, [
                'model' => $this->openRouterModel,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are MyGym AI, a knowledgeable fitness and gym management assistant.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => $this->temperature,
                'max_tokens' => $this->maxTokens,
            ]);

        if (!$response->successful()) {
            throw new \Exception('OpenRouter API Error: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();

        if (isset($data['choices'][0]['message']['content'])) {
            return trim($data['choices'][0]['message']['content']);
        }

        throw new \Exception('Invalid OpenRouter API response format');
    }

    protected function buildEnhancedContext(User $user)
    {
        $context = [
            'role' => $user->role,
            'name' => $user->name,
            'email' => $user->email,
            'member_since' => $user->created_at->format('F Y'),
        ];

        switch ($user->role) {
            case 'admin':
                $context['stats'] = $this->getEnhancedAdminStats();
                break;
            case 'instructor':
                $context['stats'] = $this->getEnhancedInstructorStats($user);
                break;
            case 'member':
                $context['stats'] = $this->getEnhancedMemberStats($user);
                break;
        }

        return $context;
    }

    protected function getEnhancedAdminStats()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return [
            'total_members' => User::where('role', 'member')->count(),
            'total_instructors' => User::where('role', 'instructor')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_classes_today' => ScheduledClass::whereDate('date_time', Carbon::today())->count(),
            'total_classes_this_month' => ScheduledClass::whereMonth('date_time', $currentMonth)
                ->whereYear('date_time', $currentYear)
                ->count(),
            'total_revenue_this_month' => (float) Receipt::whereMonth('paid_at', $currentMonth)
                ->whereYear('paid_at', $currentYear)
                ->sum('amount'),
            'new_members_this_month' => User::where('role', 'member')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count(),
        ];
    }

    protected function getEnhancedInstructorStats(User $instructor)
    {
        $now = Carbon::now();

        $classesToday = ScheduledClass::where('instructor_id', $instructor->id)
            ->whereDate('date_time', Carbon::today())
            ->with('classType')
            ->get();

        // FIXED: Get total students without complex queries
        $allStudents = ScheduledClass::where('instructor_id', $instructor->id)
            ->with('members')
            ->get()
            ->pluck('members')
            ->flatten()
            ->unique('id');

        $totalEarnings = (float) Receipt::whereHas('scheduledClass', fn($q) =>
            $q->where('instructor_id', $instructor->id)
        )->sum('amount');

        // FIXED: Calculate average class size manually
        $classes = ScheduledClass::where('instructor_id', $instructor->id)
            ->withCount('members')
            ->get();

        $averageClassSize = 0;
        if ($classes->count() > 0) {
            $totalMembers = 0;
            foreach ($classes as $class) {
                $totalMembers += $class->members_count;
            }
            $averageClassSize = round($totalMembers / $classes->count(), 1);
        }

        return [
            'total_classes_this_week' => ScheduledClass::where('instructor_id', $instructor->id)
                ->whereBetween('date_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
            'classes_today_count' => $classesToday->count(),
            'total_students' => $allStudents->count(),
            'upcoming_classes' => ScheduledClass::where('instructor_id', $instructor->id)
                ->where('date_time', '>', $now)
                ->count(),
            'total_earnings' => $totalEarnings,
            'average_class_size' => $averageClassSize,
        ];
    }

    protected function getEnhancedMemberStats(User $member)
    {
        $now = Carbon::now();

        $nextClass = $member->bookings()
            ->with('classType', 'instructor')
            ->where('date_time', '>', $now)
            ->orderBy('date_time', 'asc')
            ->first();

        $totalBookings = $member->bookings()->count();
        $upcomingBookings = $member->bookings()
            ->where('date_time', '>', $now)
            ->count();
        $pastBookings = $member->bookings()
            ->where('date_time', '<', $now)
            ->count();

        // FIXED: Get favorite class type without GROUP BY - using PHP collection
        $favoriteClassName = 'None yet';
        try {
            $bookingsWithTypes = $member->bookings()
                ->with('classType')
                ->get();

            $classTypeCounts = [];
            foreach ($bookingsWithTypes as $booking) {
                if ($booking->classType && $booking->classType->name) {
                    $className = $booking->classType->name;
                    $classTypeCounts[$className] = ($classTypeCounts[$className] ?? 0) + 1;
                }
            }

            if (!empty($classTypeCounts)) {
                $maxCount = max($classTypeCounts);
                $favoriteClassName = array_search($maxCount, $classTypeCounts);
            }
        } catch (\Exception $e) {
            Log::error('Failed to get favorite class: ' . $e->getMessage());
        }

        $totalSpent = (float) $member->receipts()->sum('amount');

        return [
            'total_bookings' => $totalBookings,
            'upcoming_bookings' => $upcomingBookings,
            'past_bookings' => $pastBookings,
            'total_spent' => $totalSpent,
            'next_class' => $nextClass ? [
                'name' => $nextClass->classType->name ?? 'Class',
                'date' => $nextClass->date_time->format('l, F j, Y'),
                'time' => $nextClass->date_time->format('g:i A'),
                'instructor' => $nextClass->instructor->name ?? 'TBA'
            ] : null,
            'member_since' => $member->created_at->format('F Y'),
            'favorite_class_type' => $favoriteClassName,
            'total_classes_attended' => $pastBookings,
        ];
    }

    protected function buildEnhancedPrompt($userMessage, $context, $chatHistory)
    {
        $prompt = "CONTEXT:\n";
        $prompt .= "- User Role: {$context['role']}\n";
        $prompt .= "- User Name: {$context['name']}\n";
        $prompt .= "- Member Since: {$context['member_since']}\n\n";

        if (!empty($context['stats'])) {
            $prompt .= "REAL-TIME STATISTICS:\n";
            foreach ($context['stats'] as $key => $value) {
                if (is_array($value)) continue;
                if (is_numeric($value) && $key !== 'total_spent') {
                    $prompt .= "- " . str_replace('_', ' ', ucfirst($key)) . ": " . number_format($value) . "\n";
                } elseif ($key === 'total_spent') {
                    $prompt .= "- Total Spent: UGX " . number_format($value, 0) . "\n";
                } else {
                    $prompt .= "- " . str_replace('_', ' ', ucfirst($key)) . ": " . $value . "\n";
                }
            }
            $prompt .= "\n";
        }

        $prompt .= "USER QUESTION: {$userMessage}\n\n";
        $prompt .= "Provide a helpful, friendly, and concise response. Use emojis to make it engaging.\n\n";
        $prompt .= "RESPONSE: ";

        return $prompt;
    }

    protected function getIntelligentLocalResponse($prompt)
    {
        // Extract user message
        preg_match('/USER QUESTION: (.+?)\n\n/s', $prompt, $matches);
        $userMessage = isset($matches[1]) ? strtolower($matches[1]) : '';

        // Extract role
        preg_match('/User Role: (\w+)/', $prompt, $roleMatches);
        $role = isset($roleMatches[1]) ? $roleMatches[1] : 'member';

        // Extract statistics
        $stats = [];
        preg_match_all('/- (.*?): (.*?)\n/', $prompt, $statMatches);
        if (!empty($statMatches[1])) {
            foreach ($statMatches[1] as $index => $key) {
                $stats[trim($key)] = trim($statMatches[2][$index] ?? '');
            }
        }

        return $this->getSmartLocalResponse($userMessage, $role, $stats);
    }

    protected function getSmartLocalResponse($message, $role, $stats = [])
    {
        // Workout related queries
        if (preg_match('/workout|exercise|training|gym/', $message)) {
            if (strpos($message, 'beginner') !== false || strpos($message, 'start') !== false) {
                return "🏋️ **Beginner's Guide to Fitness**\n\n**Getting Started:**\n• Start with 3 days per week\n• Focus on compound exercises (squats, push-ups, rows)\n• Learn proper form before adding weight\n• Rest 48 hours between strength sessions\n\n**Sample Beginner Workout:**\n1️⃣ Bodyweight squats - 3x12\n2️⃣ Incline push-ups - 3x8\n3️⃣ Dumbbell rows (light) - 3x10\n4️⃣ Plank - 3x20 seconds\n5️⃣ Walking lunges - 3x10 each leg\n\n**Pro tip:** Consistency beats intensity! Start slow and build gradually. 💪";
            }
            return "💪 **Workout Recommendations**\n\n**Weekly Structure:**\n• Strength training: 3-4x per week\n• Cardio: 2-3x per week  \n• Flexibility/Mobility: Daily\n• Rest days: 1-2 per week\n\nWhat's your current fitness level? I can create a personalized plan! 🎯";
        }

        // Nutrition related queries
        if (preg_match('/nutrition|diet|meal|food|eat|protein/', $message)) {
            return "🥗 **Healthy Eating Guidelines**\n\n**Balanced Plate Method:**\n• 1/2 plate: Vegetables\n• 1/4 plate: Lean protein\n• 1/4 plate: Complex carbs\n\n**Hydration Tip:** Drink water before meals to reduce overeating 💧\n\nWhat specific nutrition goals do you have?";
        }

        // Class and booking related queries
        if (preg_match('/class|booking|schedule/', $message)) {
            if ($role === 'member') {
                $upcoming = $stats['upcoming_bookings'] ?? 0;
                if ($upcoming > 0) {
                    return "📅 **Your Classes**\n\nYou have {$upcoming} upcoming class" . ($upcoming != 1 ? 'es' : '') . " booked!\n\n**Pro tip:** Arrive 10 minutes early to get a good spot! 🧘";
                }
                return "📅 **Booking a Class**\n\n**How to book:**\n1️⃣ Go to 'Available Classes' from your dashboard\n2️⃣ Click 'Book Now' on your preferred class\n3️⃣ Select payment method\n4️⃣ Confirm and receive email confirmation\n\nReady to book? Check the classes section! ⭐";
            }
            return "📋 **Class Management for Instructors**\n\n**Features:**\n• 📊 View class attendance\n• 💰 Track earnings\n• 📅 Manage schedule\n• 👥 Communicate with members\n\nNeed help with a specific task? 👨‍🏫";
        }

        // Default responses based on role
        switch ($role) {
            case 'admin':
                return "📊 **Admin Dashboard Overview**\n\nCurrent Stats:\n• 👥 Total Members: " . ($stats['total_members'] ?? 'tracking') . "\n• 💰 Revenue this month: " . ($stats['total_revenue_this_month'] ?? 'UGX 0') . "\n\nWhat specific metrics would you like to analyze? 📈";
            case 'instructor':
                return "👨‍🏫 **Your Instructor Dashboard**\n\n• 👥 Total students: " . ($stats['total_students'] ?? 'your') . "\n• 💰 Earnings this month: " . ($stats['total_earnings'] ?? 'UGX 0') . "\n\nNeed help with class planning? 💪";
            default:
                $bookings = $stats['total_bookings'] ?? 0;
                if ($bookings > 0) {
                    return "💪 **Welcome back to MyGym!**\n\n📊 Your Stats:\n• Total bookings: {$bookings}\n• Upcoming sessions: " . ($stats['upcoming_bookings'] ?? 0) . "\n\nWhat would you like help with today? 🏋️";
                }
                return "💪 **Welcome to MyGym AI Assistant!**\n\nI can help you with:\n• 🏋️ Workout plans\n• 🥗 Nutrition advice\n• 📅 Class bookings\n• 🔥 Motivation\n\nWhat can I help you with today? 🌟";
        }
    }

    protected function enhanceResponse($response)
    {
        $response = preg_replace('/```[\s\S]*?```/', '', $response);
        $response = str_replace("\n\n\n", "\n\n", $response);
        $response = trim($response);
        return $response;
    }

    protected function getChatHistory(User $user, $limit = 10)
    {
        return ChatMessage::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->message
            ])
            ->toArray();
    }

    protected function storeMessage($userId, $role, $message)
    {
        try {
            return ChatMessage::create([
                'user_id' => $userId,
                'role' => $role,
                'message' => $message,
                'context_type' => $this->detectContextType($message),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store chat message: ' . $e->getMessage());
            return null;
        }
    }

    protected function detectContextType($message)
    {
        $message = strtolower($message);
        if (str_contains($message, 'workout') || str_contains($message, 'exercise')) return 'workout';
        if (str_contains($message, 'nutrition') || str_contains($message, 'diet')) return 'nutrition';
        if (str_contains($message, 'class') || str_contains($message, 'booking')) return 'schedule';
        return 'general';
    }
}
