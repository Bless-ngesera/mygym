<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Models\Attendance;
use App\Models\MemberSubscription;
use App\Models\ProgressLog;
use App\Models\Goal;
use App\Models\NutritionLog;
use App\Models\Notification;
use App\Models\Message;
use App\Models\WorkoutExercise;
use App\Models\Payment;
use App\Models\User;
use App\Models\Booking;
use App\Models\ScheduledClass;
use App\Services\NotificationService;
use App\Events\MemberCheckedIn;
use App\Events\WorkoutCompleted;
use App\Events\GoalAchieved;
use App\Events\NewMessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MemberDashboardController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $user = Auth::user();

        // Workout statistics
        $totalWorkouts = Workout::where('user_id', $user->id)->count();
        $completedWorkouts = Workout::where('user_id', $user->id)->where('status', 'completed')->count();
        $workoutIncrease = $this->calculateWorkoutIncrease();

        // Goals
        $goals = Goal::where('user_id', $user->id)->where('status', 'active')->get();
        $activeGoalsCount = $goals->count();
        $completedGoalsCount = Goal::where('user_id', $user->id)->where('status', 'completed')->count();

        // Streak
        $currentStreak = $this->calculateCurrentStreak();
        $bestStreak = $this->calculateBestStreak();

        // Today's workout
        $todayWorkout = Workout::with(['exercises' => function($query) {
                $query->withPivot('id', 'sets', 'reps', 'rest_seconds', 'weight_kg', 'completed');
            }])
            ->where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        // Upcoming workouts
        $upcomingWorkouts = Workout::where('user_id', $user->id)
            ->whereDate('date', '>', today())
            ->where('status', '!=', 'completed')
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

        $upcomingWorkoutsCount = Workout::where('user_id', $user->id)
            ->whereDate('date', '>', today())
            ->where('status', '!=', 'completed')
            ->count();

        // Upcoming classes count
        $upcomingClassesCount = $this->getUpcomingClassesCount($user);

        // Active subscription
        $subscription = MemberSubscription::where('member_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', today())
            ->first();

        if ($subscription) {
            $subscription->daysRemaining = function() use ($subscription) {
                return max(0, Carbon::now()->diffInDays($subscription->end_date, false));
            };
            $subscription->getProgressPercentage = function() use ($subscription) {
                $total = Carbon::parse($subscription->start_date)->diffInDays($subscription->end_date);
                $elapsed = Carbon::parse($subscription->start_date)->diffInDays(now());
                return $total > 0 ? min(100, max(0, ($elapsed / $total) * 100)) : 0;
            };
        }

        // Progress data for charts
        $progressData = ProgressLog::where('user_id', $user->id)
            ->orderBy('date', 'asc')
            ->take(30)
            ->get();

        $progressLabels = $progressData->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d'))->toArray();
        $progressValues = $progressData->pluck('weight_kg')->toArray();

        // Today's nutrition
        $todayNutrition = NutritionLog::where('user_id', $user->id)->whereDate('date', today())->first();
        if (!$todayNutrition) {
            $todayNutrition = new NutritionLog(['user_id' => $user->id, 'date' => today(), 'calories' => 0, 'protein_grams' => 0, 'carbs_grams' => 0, 'fat_grams' => 0]);
        }

        // Notifications
        $unreadNotificationsCount = Notification::where('user_id', $user->id)->where('read', false)->count();

        // Instructor
        $instructor = null;
        if ($user->instructor_id) {
            $instructor = User::find($user->instructor_id);
        }

        // Recent messages
        $recentMessages = collect();
        if ($instructor) {
            $recentMessages = Message::where(function($query) use ($user, $instructor) {
                    $query->where('sender_id', $user->id)->where('receiver_id', $instructor->id);
                })->orWhere(function($query) use ($user, $instructor) {
                    $query->where('sender_id', $instructor->id)->where('receiver_id', $user->id);
                })
                ->where(function($query) use ($user) {
                    $query->where(function($q) use ($user) {
                        $q->where('is_deleted_by_sender', 0)->orWhere('sender_id', '!=', $user->id);
                    })->where(function($q) use ($user) {
                        $q->where('is_deleted_by_receiver', 0)->orWhere('receiver_id', '!=', $user->id);
                    });
                })
                ->orderBy('is_pinned', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        // Workout templates
        $workoutTemplates = Workout::where('user_id', $user->id)->where('status', 'completed')->limit(10)->get();
        if ($workoutTemplates->isEmpty()) {
            $workoutTemplates = Workout::where('user_id', $user->id)->limit(5)->get();
        }

        // Stats
        $stats = [
            'total_workouts' => $totalWorkouts,
            'completed_workouts' => $completedWorkouts,
            'total_hours' => floor(Attendance::where('user_id', $user->id)->sum('duration_minutes') / 60),
            'current_streak' => $currentStreak,
            'total_calories_burned' => Workout::where('user_id', $user->id)->where('status', 'completed')->sum('calories_burn') ?? 0,
        ];

        $checkedInToday = Attendance::where('user_id', $user->id)->whereDate('check_in', today())->exists();

        // Available trainers
        $availableTrainers = User::where('role', 'instructor')
            ->where('id', '!=', $user->instructor_id ?? 0)
            ->take(10)
            ->get();

        // Nutrition targets
        $user_nutrition_targets = [
            'calories' => $user->calorie_target ?? 2500,
            'protein' => $user->protein_target ?? 150,
            'carbs' => $user->carbs_target ?? 300,
            'fat' => $user->fat_target ?? 80
        ];

        $recentAchievements = collect();

        return view('member.dashboard', compact(
            'totalWorkouts',
            'workoutIncrease',
            'goals',
            'activeGoalsCount',
            'completedGoalsCount',
            'currentStreak',
            'bestStreak',
            'todayWorkout',
            'upcomingWorkouts',
            'upcomingWorkoutsCount',
            'upcomingClassesCount',
            'subscription',
            'progressLabels',
            'progressValues',
            'todayNutrition',
            'unreadNotificationsCount',
            'instructor',
            'recentMessages',
            'stats',
            'workoutTemplates',
            'checkedInToday',
            'user',
            'availableTrainers',
            'user_nutrition_targets',
            'recentAchievements'
        ));
    }

    private function getUpcomingClassesCount($user)
    {
        try {
            if (Schema::hasTable('bookings') && Schema::hasTable('scheduled_classes')) {
                return Booking::where('bookings.user_id', $user->id)
                    ->where('bookings.status', 'confirmed')
                    ->join('scheduled_classes', 'bookings.scheduled_class_id', '=', 'scheduled_classes.id')
                    ->where('scheduled_classes.date_time', '>=', Carbon::now())
                    ->count();
            }
        } catch (\Exception $e) {
            Log::warning('Error counting upcoming classes: ' . $e->getMessage());
        }
        return 0;
    }

    private function calculateCurrentStreak()
    {
        $userId = Auth::id();
        $streak = 0;
        $currentDate = now()->startOfDay();
        while (true) {
            $hasWorkout = Workout::where('user_id', $userId)->whereDate('date', $currentDate)->where('status', 'completed')->exists();
            if (!$hasWorkout) break;
            $streak++;
            $currentDate->subDay();
        }
        return $streak;
    }

    private function calculateBestStreak()
    {
        $userId = Auth::id();
        $logs = Workout::where('user_id', $userId)->where('status', 'completed')->select(DB::raw('DATE(date) as workout_date'))->distinct()->orderBy('workout_date', 'asc')->get();
        $best = 0;
        $current = 0;
        $lastDate = null;
        foreach ($logs as $log) {
            $logDate = Carbon::parse($log->workout_date)->startOfDay();
            if ($lastDate && $logDate->diffInDays($lastDate) == 1) {
                $current++;
            } else {
                $current = 1;
            }
            $best = max($best, $current);
            $lastDate = $logDate;
        }
        return $best;
    }

    private function calculateWorkoutIncrease()
    {
        $userId = Auth::id();
        $lastMonth = Workout::where('user_id', $userId)->where('status', 'completed')->whereDate('date', '>=', now()->subMonth())->count();
        $previousMonth = Workout::where('user_id', $userId)->where('status', 'completed')->whereDate('date', '>=', now()->subMonths(2))->whereDate('date', '<', now()->subMonth())->count();
        if ($previousMonth == 0) return 100;
        return round((($lastMonth - $previousMonth) / $previousMonth) * 100);
    }

    // ==================== WORKOUT METHODS ====================

    public function scheduleWorkout(Request $request)
    {
        $validated = $request->validate([
            'workout_template_id' => 'required|exists:workouts,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'nullable|date_format:H:i'
        ]);

        $scheduledDateTime = $validated['scheduled_date'];
        if (!empty($validated['scheduled_time'])) {
            $scheduledDateTime .= ' ' . $validated['scheduled_time'];
        }
        $scheduledDateTime = Carbon::parse($scheduledDateTime);

        $template = Workout::find($validated['workout_template_id']);
        $estimatedCalories = ($template->duration_minutes ?? 45) * 8;

        $workout = Workout::create([
            'user_id' => Auth::id(),
            'title' => $template->title,
            'description' => $template->description,
            'duration_minutes' => $template->duration_minutes ?? 45,
            'calories_burn' => $template->calories_burn ?? $estimatedCalories,
            'date' => $scheduledDateTime->format('Y-m-d'),
            'status' => 'scheduled'
        ]);

        if ($template->exercises && $template->exercises->count() > 0) {
            foreach ($template->exercises as $exercise) {
                $workout->exercises()->attach($exercise->id, [
                    'sets' => $exercise->pivot->sets ?? 3,
                    'reps' => $exercise->pivot->reps ?? 12,
                    'weight_kg' => $exercise->pivot->weight_kg ?? null,
                    'rest_seconds' => $exercise->pivot->rest_seconds ?? 60,
                    'completed' => false
                ]);
            }
        }

        $this->notificationService->sendToUser(Auth::user(), [
            'type' => 'workout_scheduled',
            'title' => '📅 Workout Scheduled',
            'message' => 'Your workout "' . $workout->title . '" has been scheduled for ' . $scheduledDateTime->format('M d, h:i A'),
            'priority' => 'medium',
            'action_url' => route('member.workouts.details', $workout->id),
            'data' => ['workout_id' => $workout->id]
        ]);

        return response()->json(['success' => true, 'workout' => $workout]);
    }

    public function completeWorkout(Workout $workout)
    {
        if ($workout->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $duration = $workout->started_at ? now()->diffInMinutes($workout->started_at) : ($workout->duration_minutes ?? 45);

        $workout->update([
            'status' => 'completed',
            'completed_at' => now(),
            'duration_minutes' => $duration
        ]);

        Goal::where('user_id', Auth::id())->where('type', 'workouts')->where('status', 'active')->increment('current_value', 1);

        event(new WorkoutCompleted(Auth::user(), $workout));
        $this->notificationService->workoutCompleted(Auth::user(), $workout);
        $this->checkGoalProgress(Auth::user());

        return response()->json(['success' => true]);
    }

    public function completeExercise(WorkoutExercise $workoutExercise)
    {
        if ($workoutExercise->workout->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $workoutExercise->update(['completed' => true]);

        $allCompleted = $workoutExercise->workout->exercises()->wherePivot('completed', false)->count() === 0;
        if ($allCompleted && $workoutExercise->workout->status !== 'completed') {
            $this->completeWorkout($workoutExercise->workout);
        }

        return response()->json(['success' => true]);
    }

    public function getWorkoutDetails($workoutId)
    {
        try {
            $workout = Workout::with('exercises')->where('user_id', Auth::id())->findOrFail($workoutId);
            return response()->json([
                'success' => true,
                'workout' => [
                    'id' => $workout->id,
                    'title' => $workout->title,
                    'description' => $workout->description,
                    'date' => $workout->date ? Carbon::parse($workout->date)->format('F j, Y') : null,
                    'duration' => $workout->duration_minutes,
                    'calories_burn' => $workout->calories_burn,
                    'exercises' => $workout->exercises->map(function ($exercise) {
                        return [
                            'name' => $exercise->name,
                            'pivot' => ['sets' => $exercise->pivot->sets ?? null, 'reps' => $exercise->pivot->reps ?? null, 'weight_kg' => $exercise->pivot->weight_kg ?? null]
                        ];
                    }),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Workout not found'], 404);
        }
    }

    public function workoutHistory()
    {
        $workouts = Workout::where('user_id', Auth::id())->where('status', 'completed')->orderBy('date', 'desc')->paginate(20);
        $totalMinutes = Workout::where('user_id', Auth::id())->where('status', 'completed')->sum('duration_minutes');
        $totalCalories = Workout::where('user_id', Auth::id())->where('status', 'completed')->sum('calories_burn');
        return view('member.workouts.history', compact('workouts', 'totalMinutes', 'totalCalories'));
    }

    public function getWorkoutTemplates()
    {
        $templates = Workout::where('user_id', Auth::id())->where('status', 'completed')->limit(10)->get();
        if ($templates->isEmpty()) {
            $templates = Workout::where('user_id', Auth::id())->limit(5)->get();
        }
        return response()->json(['success' => true, 'templates' => $templates]);
    }

    // ==================== ATTENDANCE METHODS ====================

    public function checkIn()
    {
        $user = Auth::user();
        $existing = Attendance::where('user_id', $user->id)->whereDate('check_in', today())->whereNull('check_out')->first();
        if ($existing) {
            return response()->json(['error' => 'You are already checked in'], 400);
        }

        $attendance = Attendance::create(['user_id' => $user->id, 'check_in' => now(), 'status' => 'checked_in']);

        $streakCount = $this->calculateCurrentStreak();

        event(new MemberCheckedIn($user, $streakCount));
        $this->notificationService->checkInConfirmation($user, $streakCount);

        return response()->json(['success' => true, 'message' => 'Checked in successfully']);
    }

    // ==================== PROGRESS METHODS ====================

    public function addProgress(Request $request)
    {
        $validated = $request->validate([
            'weight_kg' => 'required|numeric|min:20|max:300',
            'date' => 'required|date'
        ]);

        $progress = ProgressLog::updateOrCreate(
            ['user_id' => Auth::id(), 'date' => $validated['date']],
            ['weight_kg' => $validated['weight_kg']]
        );

        $this->notificationService->sendToUser(Auth::user(), [
            'type' => 'weight_logged',
            'title' => '⚖️ Weight Logged',
            'message' => 'Your weight of ' . $validated['weight_kg'] . ' kg has been recorded for ' . Carbon::parse($validated['date'])->format('M d, Y'),
            'priority' => 'low',
            'action_url' => route('member.dashboard'),
            'data' => ['weight' => $validated['weight_kg'], 'date' => $validated['date']]
        ]);

        return response()->json(['success' => true, 'progress' => $progress]);
    }

    // ==================== GOAL METHODS ====================

    public function createGoal(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:weight_loss,muscle_gain,endurance,strength,workouts,attendance,nutrition',
            'target_value' => 'required|numeric|min:1',
            'unit' => 'nullable|string|max:50',
            'target_date' => 'required|date|after:today'
        ]);

        $goal = Goal::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'type' => $validated['type'],
            'target_value' => $validated['target_value'],
            'current_value' => 0,
            'unit' => $validated['unit'] ?? 'units',
            'target_date' => $validated['target_date'],
            'status' => 'active'
        ]);

        $this->notificationService->sendToUser(Auth::user(), [
            'type' => 'goal_created',
            'title' => '🎯 New Goal Created',
            'message' => 'Your goal "' . $goal->title . '" has been created. Target: ' . $goal->target_value . ' ' . $goal->unit,
            'priority' => 'medium',
            'action_url' => route('member.dashboard'),
            'data' => ['goal_id' => $goal->id]
        ]);

        return response()->json(['success' => true, 'goal' => $goal]);
    }

    public function getGoals()
    {
        $goals = Goal::where('user_id', Auth::id())->where('status', 'active')->get();
        return response()->json(['success' => true, 'goals' => $goals]);
    }

    private function checkGoalProgress($user)
    {
        $goals = Goal::where('user_id', $user->id)->where('status', 'active')->get();

        foreach ($goals as $goal) {
            $percentage = $this->calculateGoalPercentage($goal);
            $milestones = [25, 50, 75];

            if (in_array($percentage, $milestones) && !$goal->last_notified_at?->isToday()) {
                $this->notificationService->sendToUser($user, [
                    'type' => 'goal_progress',
                    'title' => '🎯 Goal Progress Update',
                    'message' => "You're {$percentage}% of the way to your goal: {$goal->title}",
                    'priority' => 'medium',
                    'action_url' => route('member.dashboard'),
                    'data' => ['goal_id' => $goal->id, 'percentage' => $percentage]
                ]);

                $goal->update(['last_notified_at' => now()]);
            }

            if ($goal->current_value >= $goal->target_value && $goal->status === 'active') {
                $goal->update(['status' => 'completed', 'completed_at' => now()]);
                event(new GoalAchieved($user, $goal));
                $this->notificationService->goalAchieved($user, $goal);
            }
        }
    }

    private function calculateGoalPercentage($goal)
    {
        if ($goal->target_value <= 0) return 0;
        return min(100, round(($goal->current_value / $goal->target_value) * 100));
    }

    // ==================== NUTRITION METHODS ====================

    public function addNutrition(Request $request)
    {
        $validated = $request->validate([
            'calories' => 'required|integer|min:0|max:10000',
            'protein_grams' => 'nullable|numeric|min:0|max:500',
            'carbs_grams' => 'nullable|numeric|min:0|max:500',
            'fat_grams' => 'nullable|numeric|min:0|max:200',
        ]);

        $nutrition = NutritionLog::firstOrCreate(['user_id' => Auth::id(), 'date' => today()]);
        $nutrition->update([
            'calories' => ($nutrition->calories ?? 0) + $validated['calories'],
            'protein_grams' => ($nutrition->protein_grams ?? 0) + ($validated['protein_grams'] ?? 0),
            'carbs_grams' => ($nutrition->carbs_grams ?? 0) + ($validated['carbs_grams'] ?? 0),
            'fat_grams' => ($nutrition->fat_grams ?? 0) + ($validated['fat_grams'] ?? 0),
        ]);

        if ($nutrition->calories >= 2000 && $nutrition->calories - $validated['calories'] < 2000) {
            $this->notificationService->sendToUser(Auth::user(), [
                'type' => 'nutrition_milestone',
                'title' => '🍽️ Nutrition Milestone',
                'message' => 'You\'ve reached 2000+ calories today! Keep fueling your fitness journey.',
                'priority' => 'low',
                'action_url' => route('member.dashboard'),
                'data' => ['calories' => $nutrition->calories]
            ]);
        }

        return response()->json(['success' => true, 'nutrition' => $nutrition]);
    }

    public function getTodayNutrition()
    {
        $nutrition = NutritionLog::where('user_id', Auth::id())->whereDate('date', today())->first();
        if (!$nutrition) {
            $nutrition = new NutritionLog(['user_id' => Auth::id(), 'date' => today(), 'calories' => 0, 'protein_grams' => 0, 'carbs_grams' => 0, 'fat_grams' => 0]);
        }
        return response()->json(['success' => true, 'nutrition' => $nutrition]);
    }

    // ==================== MESSAGE METHODS (FULLY FUNCTIONAL) ====================

    /**
     * Get conversation between member and trainer
     */
    public function getConversation($trainerId)
    {
        try {
            $userId = Auth::id();

            // Verify the trainer exists
            $trainer = User::find($trainerId);
            if (!$trainer || $trainer->role !== 'instructor') {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid trainer'
                ], 404);
            }

            // Get messages between the two users
            $messages = Message::where(function($query) use ($userId, $trainerId) {
                $query->where('sender_id', $userId)->where('receiver_id', $trainerId);
            })->orWhere(function($query) use ($userId, $trainerId) {
                $query->where('sender_id', $trainerId)->where('receiver_id', $userId);
            })
            ->where(function($query) use ($userId) {
                // Don't show messages deleted by current user
                $query->where(function($q) use ($userId) {
                    $q->where('is_deleted_by_sender', 0)->orWhere('sender_id', '!=', $userId);
                })->where(function($q) use ($userId) {
                    $q->where('is_deleted_by_receiver', 0)->orWhere('receiver_id', '!=', $userId);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();

            // Mark unread messages as read
            Message::where('receiver_id', $userId)
                ->where('sender_id', $trainerId)
                ->where('read', 0)
                ->update([
                    'read' => 1,
                    'read_at' => Carbon::now()
                ]);

            return response()->json([
                'success' => true,
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading conversation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a message to trainer
     */
    public function sendMessageToTrainer(Request $request)
    {
        try {
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000'
            ]);

            $receiver = User::find($request->receiver_id);

            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'read' => 0,
                'is_edited' => 0,
                'is_pinned' => 0,
                'is_deleted_by_sender' => 0,
                'is_deleted_by_receiver' => 0
            ]);

            // Dispatch event for real-time notification
            if ($receiver) {
                event(new NewMessageSent($message, Auth::user(), $receiver));

                // Send notification to receiver
                $this->notificationService->newMessage(
                    $receiver,
                    Auth::user(),
                    $request->message
                );
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a message (edit)
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
                    'error' => 'Message not found'
                ], 404);
            }

            $message->update([
                'message' => $request->message,
                'is_edited' => 1,
                'edited_at' => Carbon::now()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error updating message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a message (soft delete)
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

            if ($message->sender_id == $userId) {
                $message->update(['is_deleted_by_sender' => 1]);
            } elseif ($message->receiver_id == $userId) {
                $message->update(['is_deleted_by_receiver' => 1]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 403);
            }

            // Permanently delete if both users deleted it
            if ($message->is_deleted_by_sender && $message->is_deleted_by_receiver) {
                $message->forceDelete();
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error deleting message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pin or unpin a message
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
                    'error' => 'Message not found'
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
            Log::error('Error pinning message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get messages with a specific user (legacy method)
     */
    public function getMessages($userId)
    {
        try {
            $messages = Message::where(function($query) use ($userId) {
                    $query->where('sender_id', Auth::id())
                          ->where('receiver_id', $userId);
                })->orWhere(function($query) use ($userId) {
                    $query->where('sender_id', $userId)
                          ->where('receiver_id', Auth::id());
                })
                ->where(function($query) {
                    $query->where(function($q) {
                        $q->where('is_deleted_by_sender', 0)->orWhere('sender_id', '!=', Auth::id());
                    })->where(function($q) {
                        $q->where('is_deleted_by_receiver', 0)->orWhere('receiver_id', '!=', Auth::id());
                    });
                })
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark messages as read
            Message::where('receiver_id', Auth::id())
                ->where('sender_id', $userId)
                ->where('read', false)
                ->update(['read' => true, 'read_at' => now()]);

            return response()->json(['success' => true, 'messages' => $messages]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get chat messages for a specific trainer (AJAX endpoint)
     */
    public function getChatMessages($trainerId)
    {
        return $this->getConversation($trainerId);
    }

    // ==================== TRAINER METHODS ====================

    public function selectTrainer(Request $request)
    {
        $validated = $request->validate(['trainer_id' => 'required|exists:users,id']);
        $trainer = User::find($validated['trainer_id']);

        if ($trainer->role !== 'instructor') {
            return response()->json(['error' => 'Invalid trainer selected'], 400);
        }

        $user = Auth::user();
        $user->instructor_id = $trainer->id;
        $user->save();

        // Send welcome message from trainer
        Message::create([
            'sender_id' => $trainer->id,
            'receiver_id' => $user->id,
            'message' => "Hello {$user->name}! I'm your new personal trainer. I'm excited to help you achieve your fitness goals! 💪",
            'read' => 0,
            'is_edited' => 0,
            'is_pinned' => 0,
            'is_deleted_by_sender' => 0,
            'is_deleted_by_receiver' => 0
        ]);

        $this->notificationService->instructorAssigned($user, $trainer);

        return response()->json(['success' => true]);
    }

    // ==================== AI CHAT METHODS ====================

    public function aiChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = strtolower(trim($request->message));
        $user = Auth::user();

        $response = $this->getEnhancedAIResponse($message, $user);

        return response()->json([
            'success' => true,
            'reply' => $response
        ]);
    }

    private function getEnhancedAIResponse($message, $user)
    {
        // Workout related queries
        if (str_contains($message, 'workout') || str_contains($message, 'exercise') || str_contains($message, 'gym')) {
            if (str_contains($message, 'beginner')) {
                return "🏋️ For beginners, " . ($user->name ?? 'friend') . ", I recommend starting with:\n\n• 3x/week full-body workouts\n• Focus on compound exercises (squats, pushups, rows)\n• 20-30 minute sessions\n• Rest days between workouts\n\nWould you like a sample beginner routine?";
            } elseif (str_contains($message, 'chest') || str_contains($message, 'push')) {
                return "💪 Great chest workout:\n\n• Bench Press: 4x8-10\n• Incline Dumbbell: 3x10-12\n• Push-ups: 3x15\n• Chest Flyes: 3x12\n\nRemember to warm up properly!";
            } elseif (str_contains($message, 'leg') || str_contains($message, 'squat')) {
                return "🦵 Leg day essentials:\n\n• Squats: 4x8-10\n• Lunges: 3x12 each leg\n• Leg Press: 3x10-12\n• Calf Raises: 4x15\n\nDon't skip leg day! 💪";
            } elseif (str_contains($message, 'back')) {
                return "🔥 Back workout routine:\n\n• Pull-ups/Lat Pulldowns: 4x8-10\n• Barbell Rows: 3x10-12\n• Seated Cable Rows: 3x12\n• Deadlifts: 3x5\n\nBuild that V-taper!";
            } elseif (str_contains($message, 'shoulder')) {
                return "💪 Shoulder workout:\n\n• Overhead Press: 4x8-10\n• Lateral Raises: 3x12-15\n• Front Raises: 3x12\n• Face Pulls: 3x15\n\nBuild those boulder shoulders!";
            } elseif (str_contains($message, 'arms') || str_contains($message, 'bicep') || str_contains($message, 'tricep')) {
                return "💪 Arm day workout:\n\nBiceps:\n• Barbell Curls: 4x8-10\n• Dumbbell Curls: 3x10-12\n• Hammer Curls: 3x10\n\nTriceps:\n• Tricep Pushdowns: 4x10-12\n• Skull Crushers: 3x8-10\n• Dips: 3x12\n\nGet those guns! 💪";
            } elseif (str_contains($message, 'abs') || str_contains($message, 'core')) {
                return "🔥 Core strengthening:\n\n• Planks: 3x30-60 sec\n• Russian Twists: 3x15 each side\n• Leg Raises: 3x12\n• Bicycle Crunches: 3x20\n• Mountain Climbers: 3x30 sec\n\nConsistency is key for visible abs!";
            } else {
                return "💪 Here's a balanced weekly workout plan:\n\nMonday: Chest & Triceps\nTuesday: Back & Biceps\nWednesday: Legs & Core\nThursday: Shoulders & Cardio\nFriday: Full Body HIIT\nSaturday: Active Recovery\nSunday: Rest\n\nWant specific exercises for any day? Just ask!";
            }
        }

        // Nutrition related queries
        elseif (str_contains($message, 'nutrition') || str_contains($message, 'diet') || str_contains($message, 'food') || str_contains($message, 'meal') || str_contains($message, 'eat')) {
            if (str_contains($message, 'breakfast')) {
                return "🥣 Healthy breakfast ideas:\n\n• Greek yogurt with berries & granola\n• Oatmeal with banana & nuts\n• Protein smoothie with spinach\n• Eggs with avocado toast\n\nAim for 20-30g protein to start your day!";
            } elseif (str_contains($message, 'lunch')) {
                return "🥗 Nutritious lunch options:\n\n• Grilled chicken quinoa bowl\n• Tuna salad wrap\n• Lentil soup with whole grain bread\n• Salmon with roasted vegetables\n\nBalance protein, complex carbs, and veggies!";
            } elseif (str_contains($message, 'dinner')) {
                return "🍽️ Healthy dinner ideas:\n\n• Lean steak with sweet potato\n• Baked fish with brown rice\n• Turkey meatballs with zucchini noodles\n• Stir-fry tofu with vegetables\n\nEat 2-3 hours before bedtime!";
            } elseif (str_contains($message, 'snack')) {
                return "🍎 Smart snack choices:\n\n• Apple with peanut butter\n• Protein shake\n• Handful of almonds\n• Cottage cheese with berries\n• Hard-boiled eggs\n\nKeep snacks under 200 calories!";
            } elseif (str_contains($message, 'protein')) {
                return "🥩 Best protein sources:\n\n• Chicken breast (31g/100g)\n• Fish (20-25g/100g)\n• Eggs (6g each)\n• Greek yogurt (10g/100g)\n• Lentils (9g/100g)\n• Whey protein (20-25g/scoop)\n\nAim for 1.6-2.2g protein per kg body weight!";
            } elseif (str_contains($message, 'vegan') || str_contains($message, 'vegetarian')) {
                return "🌱 Plant-based protein sources:\n\n• Lentils & beans\n• Tofu & tempeh\n• Quinoa & chickpeas\n• Seitan & edamame\n• Nuts & seeds\n• Plant-based protein powder\n\nCombine different sources for complete protein!";
            } else {
                return "🥗 General nutrition tips:\n\n• Eat protein with every meal\n• Include colorful vegetables\n• Stay hydrated (2-3L water daily)\n• Limit processed foods\n• Don't skip meals\n• Track calories if weight is a goal\n\nWant specific meal plans or recipes? Let me know your goals!";
            }
        }

        // Motivation related queries
        elseif (str_contains($message, 'motivation') || str_contains($message, 'motivate') || str_contains($message, 'tired') || str_contains($message, 'give up') || str_contains($message, 'discouraged')) {
            $motivationalQuotes = [
                "🔥 Remember why you started! Every workout is progress, no matter how small.",
                "💪 You're stronger than you think. The pain you feel today will be the strength you feel tomorrow.",
                "🌟 Success isn't always about greatness. It's about consistency. Consistent hard work leads to success.",
                "🏆 Your body can stand almost anything. It's your mind that you have to convince.",
                "⚡ The only bad workout is the one that didn't happen. You've already won by showing up!"
            ];

            $quote = $motivationalQuotes[array_rand($motivationalQuotes)];

            return $quote . "\n\nYou've got this, " . ($user->name ?? 'champion') . "! 💪 What specific goal are you working toward right now?";
        }

        // Default response
        else {
            return "I'm here to help with your fitness journey, " . ($user->name ?? 'friend') . "! 💪\n\nYou can ask me about:\n• \"Workout for beginners\"\n• \"Healthy meal ideas\" \n• \"How to stay motivated\"\n• \"Track my progress\"\n• \"Cardio routine\"\n• \"Recovery tips\"\n\nWhat specific fitness goal can I help you with today?";
        }
    }
}
