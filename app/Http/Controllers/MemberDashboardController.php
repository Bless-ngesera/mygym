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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MemberDashboardController extends Controller
{
    // REMOVE the constructor completely - it's not needed in Laravel 12
    // The middleware is already applied in routes/web.php

    /**
     * Show the member dashboard with all statistics and data
     */
    public function index()
    {
        $user = Auth::user();

        // Get today's workout
        $todayWorkout = Workout::with(['exercises' => function($query) {
                $query->withPivot('id', 'sets', 'reps', 'rest_seconds', 'weight_kg', 'completed');
            }])
            ->where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        // Get upcoming workouts
        $upcomingWorkouts = Workout::where('user_id', $user->id)
            ->whereDate('date', '>', today())
            ->orderBy('date', 'asc')
            ->limit(3)
            ->get();

        // Get current attendance status (checked in today without check out)
        $currentAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in', today())
            ->whereNull('check_out')
            ->first();

        // Get active subscription
        $subscription = MemberSubscription::where('member_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', today())
            ->first();

        // Get progress data for charts (last 30 days)
        $progressData = ProgressLog::where('user_id', $user->id)
            ->orderBy('date', 'asc')
            ->take(30)
            ->get();

        // Get active goals
        $goals = Goal::where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        // Get today's nutrition
        $nutrition = NutritionLog::firstOrCreate([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        // Get unread notifications count
        $unreadNotificationsCount = Notification::where('user_id', $user->id)
            ->where('read', false)
            ->count();

        // Get recent notifications (last 5)
        $recentNotifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get assigned instructor
        $instructor = null;
        if ($user->instructor_id) {
            $instructor = \App\Models\User::find($user->instructor_id);
        }

        // Get recent messages with instructor
        $recentMessages = collect();
        if ($instructor) {
            $recentMessages = Message::where(function($query) use ($user, $instructor) {
                    $query->where('sender_id', $user->id)
                          ->where('receiver_id', $instructor->id);
                })->orWhere(function($query) use ($user, $instructor) {
                    $query->where('sender_id', $instructor->id)
                          ->where('receiver_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        // Get workout frequency data (last 30 days)
        $workoutFrequency = Workout::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereDate('date', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(date) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Get attendance history (last 10 records)
        $attendanceHistory = Attendance::where('user_id', $user->id)
            ->whereNotNull('check_out')
            ->orderBy('check_in', 'desc')
            ->limit(10)
            ->get();

        // Get payment history (from class bookings)
        $paymentHistory = \App\Models\Payment::where('member_id', $user->id)
            ->orderBy('paid_at', 'desc')
            ->limit(5)
            ->get();

        // Get statistics
        $stats = [
            'total_workouts' => Workout::where('user_id', $user->id)->count(),
            'completed_workouts' => Workout::where('user_id', $user->id)->where('status', 'completed')->count(),
            'total_hours' => floor(Attendance::where('user_id', $user->id)->sum('duration_minutes') / 60),
            'current_streak' => $this->calculateCurrentStreak($user->id),
            'total_calories_burned' => Workout::where('user_id', $user->id)->where('status', 'completed')->sum('duration_minutes') * 8,
        ];

        return view('member.dashboard', compact(
            'todayWorkout',
            'upcomingWorkouts',
            'currentAttendance',
            'subscription',
            'progressData',
            'goals',
            'nutrition',
            'unreadNotificationsCount',
            'recentNotifications',
            'instructor',
            'recentMessages',
            'workoutFrequency',
            'attendanceHistory',
            'paymentHistory',
            'stats'
        ));
    }

    /**
     * Calculate current workout streak
     */
    private function calculateCurrentStreak($userId)
    {
        $streak = 0;
        $currentDate = now()->startOfDay();

        while (true) {
            $hasWorkout = Workout::where('user_id', $userId)
                ->whereDate('date', $currentDate)
                ->where('status', 'completed')
                ->exists();

            if (!$hasWorkout) {
                break;
            }

            $streak++;
            $currentDate->subDay();
        }

        return $streak;
    }

    /**
     * Start a workout
     */
    public function startWorkout(Workout $workout)
    {
        if ($workout->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $workout->update([
            'status' => 'in_progress',
            'started_at' => now()
        ]);

        // Create notification
        Notification::create([
            'user_id' => Auth::id(),
            'type' => 'workout',
            'title' => 'Workout Started',
            'message' => 'You started "' . $workout->title . '". Good luck! 💪',
            'data' => ['workout_id' => $workout->id]
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Complete a workout
     */
    public function completeWorkout(Workout $workout)
    {
        if ($workout->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $duration = $workout->started_at ? now()->diffInMinutes($workout->started_at) : null;

        $workout->update([
            'status' => 'completed',
            'completed_at' => now(),
            'duration_minutes' => $duration
        ]);

        // Update goals
        Goal::where('user_id', Auth::id())
            ->where('type', 'workouts')
            ->where('status', 'active')
            ->increment('current_value', 1);

        // Create notification
        Notification::create([
            'user_id' => Auth::id(),
            'type' => 'workout',
            'title' => 'Workout Completed! 🎉',
            'message' => 'Great job completing "' . $workout->title . '" in ' . $duration . ' minutes!',
            'data' => ['workout_id' => $workout->id]
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Complete a specific exercise in a workout
     */
    public function completeExercise(WorkoutExercise $workoutExercise)
    {
        if ($workoutExercise->workout->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $workoutExercise->update(['completed' => true]);

        // Check if all exercises are completed
        $allCompleted = $workoutExercise->workout->exercises()
            ->wherePivot('completed', false)
            ->count() === 0;

        if ($allCompleted && $workoutExercise->workout->status !== 'completed') {
            $this->completeWorkout($workoutExercise->workout);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Check in to the gym
     */
    public function checkIn()
    {
        $user = Auth::user();

        // Check if already checked in today
        $existing = Attendance::where('user_id', $user->id)
            ->whereDate('check_in', today())
            ->whereNull('check_out')
            ->first();

        if ($existing) {
            return response()->json(['error' => 'You are already checked in'], 400);
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'check_in' => now(),
            'status' => 'checked_in'
        ]);

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'attendance',
            'title' => 'Checked In Successfully',
            'message' => 'You have checked in at ' . now()->format('h:i A') . '. Have a great workout!',
            'data' => ['attendance_id' => $attendance->id]
        ]);

        return response()->json(['success' => true, 'message' => 'Checked in successfully']);
    }

    /**
     * Check out from the gym
     */
    public function checkOut()
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in', today())
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            return response()->json(['error' => 'No active check-in found'], 400);
        }

        $duration = now()->diffInMinutes($attendance->check_in);

        $attendance->update([
            'check_out' => now(),
            'duration_minutes' => $duration,
            'status' => 'checked_out'
        ]);

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'attendance',
            'title' => 'Checked Out Successfully',
            'message' => 'Great workout! You were here for ' . $duration . ' minutes.',
            'data' => ['attendance_id' => $attendance->id]
        ]);

        return response()->json(['success' => true, 'message' => 'Checked out successfully']);
    }

    /**
     * Add nutrition log for today
     */
    public function addNutrition(Request $request)
    {
        $validated = $request->validate([
            'calories' => 'required|integer|min:0|max:10000',
            'protein_grams' => 'required|integer|min:0|max:500',
            'carbs_grams' => 'required|integer|min:0|max:500',
            'fat_grams' => 'required|integer|min:0|max:200',
        ]);

        $nutrition = NutritionLog::firstOrCreate([
            'user_id' => Auth::id(),
            'date' => today(),
        ]);

        $nutrition->update($validated);

        return response()->json(['success' => true, 'nutrition' => $nutrition]);
    }

    /**
     * Mark a notification as read
     */
    public function markNotificationRead($notificationId)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($notificationId);

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Send a message to instructor
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message']
        ]);

        // Create notification for receiver
        Notification::create([
            'user_id' => $validated['receiver_id'],
            'type' => 'message',
            'title' => 'New Message',
            'message' => Auth::user()->name . ' sent you a message',
            'data' => ['message_id' => $message->id]
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }

    /**
     * Get messages with instructor (AJAX)
     */
    public function getMessages($userId)
    {
        $messages = Message::where(function($query) use ($userId) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $userId);
            })->orWhere(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('receiver_id', Auth::id())
            ->where('sender_id', $userId)
            ->where('read', false)
            ->update(['read' => true, 'read_at' => now()]);

        return response()->json(['success' => true, 'messages' => $messages]);
    }

    /**
     * Add progress log (weight, measurements)
     */
    public function addProgress(Request $request)
    {
        $validated = $request->validate([
            'weight_kg' => 'required|numeric|min:20|max:300',
            'body_fat_percentage' => 'nullable|numeric|min:5|max:50',
            'chest_cm' => 'nullable|numeric|min:50|max:200',
            'waist_cm' => 'nullable|numeric|min:50|max:200',
            'hips_cm' => 'nullable|numeric|min:50|max:200',
            'notes' => 'nullable|string|max:500',
        ]);

        $progress = ProgressLog::create([
            'user_id' => Auth::id(),
            'date' => today(),
            ...$validated
        ]);

        // Update goals
        Goal::where('user_id', Auth::id())
            ->where('type', 'weight')
            ->where('status', 'active')
            ->update(['current_value' => $validated['weight_kg']]);

        return response()->json(['success' => true, 'progress' => $progress]);
    }

    /**
     * Create a new goal
     */
    public function createGoal(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:weight,workouts,attendance,strength,nutrition',
            'target_value' => 'required|numeric|min:1',
            'deadline' => 'required|date|after:today',
            'unit' => 'nullable|string|max:50',
        ]);

        $goal = Goal::create([
            'user_id' => Auth::id(),
            'status' => 'active',
            'current_value' => 0,
            ...$validated
        ]);

        return response()->json(['success' => true, 'goal' => $goal]);
    }
}
