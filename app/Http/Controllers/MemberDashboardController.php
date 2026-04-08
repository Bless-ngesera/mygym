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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MemberDashboardController extends Controller
{
    /**
     * Show the member dashboard with all statistics and data
     */
    public function index()
    {
        try {
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
                $instructor = User::find($user->instructor_id);
            }

            // If no instructor assigned, get the first available instructor
            if (!$instructor) {
                $instructor = User::role('instructor')->first();
            }

            // Get recent messages with instructor (last 50 messages)
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
                    ->limit(50)
                    ->get()
                    ->reverse(); // Reverse to show oldest first
            }

            // Get workout frequency data (last 30 days)
            $workoutFrequency = Workout::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereDate('date', '>=', now()->subDays(30))
                ->select(DB::raw('DATE(date) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            // ========== PAGINATED: Workout History ==========
            $workoutHistory = Workout::where('user_id', $user->id)
                ->where('status', 'completed')
                ->orderBy('date', 'desc')
                ->paginate(10);

            // ========== PAGINATED: Attendance History ==========
            $attendanceHistory = Attendance::where('user_id', $user->id)
                ->whereNotNull('check_out')
                ->orderBy('check_in', 'desc')
                ->paginate(10);

            // ========== PAGINATED: Payment History ==========
            $paymentHistory = Payment::where('member_id', $user->id)
                ->orderBy('paid_at', 'desc')
                ->paginate(10);

            // Get statistics
            $stats = [
                'total_workouts' => Workout::where('user_id', $user->id)->count(),
                'completed_workouts' => Workout::where('user_id', $user->id)->where('status', 'completed')->count(),
                'total_hours' => floor(Attendance::where('user_id', $user->id)->sum('duration_minutes') / 60),
                'current_streak' => $this->calculateCurrentStreak($user->id),
                'total_calories_burned' => Workout::where('user_id', $user->id)->where('status', 'completed')->sum('duration_minutes') * 8,
                'attendance_rate' => $this->calculateAttendanceRate($user->id),
                'average_workout_duration' => $this->calculateAverageWorkoutDuration($user->id),
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
                'stats',
                'workoutHistory'
            ));

        } catch (\Exception $e) {
            Log::error('Member dashboard error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load dashboard. Please try again later.');
        }
    }

    /**
     * Calculate current workout streak
     */
    private function calculateCurrentStreak($userId)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error calculating streak: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate attendance rate for the last 30 days
     */
    private function calculateAttendanceRate($userId)
    {
        try {
            $last30Days = now()->subDays(30);
            $totalDays = 30;

            $attendedDays = Attendance::where('user_id', $userId)
                ->where('check_in', '>=', $last30Days)
                ->whereNotNull('check_out')
                ->distinct(DB::raw('DATE(check_in)'))
                ->count();

            return $totalDays > 0 ? round(($attendedDays / $totalDays) * 100) : 0;
        } catch (\Exception $e) {
            Log::error('Error calculating attendance rate: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate average workout duration
     */
    private function calculateAverageWorkoutDuration($userId)
    {
        try {
            $avgDuration = Workout::where('user_id', $userId)
                ->where('status', 'completed')
                ->whereNotNull('duration_minutes')
                ->avg('duration_minutes');

            return round($avgDuration ?? 0);
        } catch (\Exception $e) {
            Log::error('Error calculating average duration: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Start a workout
     */
    public function startWorkout(Workout $workout)
    {
        try {
            if ($workout->user_id !== Auth::id()) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            if ($workout->status === 'completed') {
                return response()->json(['success' => false, 'error' => 'Workout already completed'], 400);
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

            return response()->json(['success' => true, 'message' => 'Workout started successfully']);

        } catch (\Exception $e) {
            Log::error('Error starting workout: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to start workout'], 500);
        }
    }

    /**
     * Complete a workout
     */
    public function completeWorkout(Workout $workout)
    {
        try {
            if ($workout->user_id !== Auth::id()) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            if ($workout->status === 'completed') {
                return response()->json(['success' => false, 'error' => 'Workout already completed'], 400);
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
                'message' => 'Great job completing "' . $workout->title . '" in ' . ($duration ?? 'unknown') . ' minutes!',
                'data' => ['workout_id' => $workout->id]
            ]);

            return response()->json(['success' => true, 'message' => 'Workout completed successfully']);

        } catch (\Exception $e) {
            Log::error('Error completing workout: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to complete workout'], 500);
        }
    }

    /**
     * Complete a specific exercise in a workout
     */
    public function completeExercise(WorkoutExercise $workoutExercise)
    {
        try {
            if ($workoutExercise->workout->user_id !== Auth::id()) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            if ($workoutExercise->completed) {
                return response()->json(['success' => false, 'error' => 'Exercise already completed'], 400);
            }

            $workoutExercise->update(['completed' => true]);

            // Check if all exercises are completed
            $allCompleted = $workoutExercise->workout->exercises()
                ->wherePivot('completed', false)
                ->count() === 0;

            if ($allCompleted && $workoutExercise->workout->status !== 'completed') {
                return $this->completeWorkout($workoutExercise->workout);
            }

            return response()->json(['success' => true, 'message' => 'Exercise completed']);

        } catch (\Exception $e) {
            Log::error('Error completing exercise: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to complete exercise'], 500);
        }
    }

    /**
     * Check in to the gym
     */
    public function checkIn()
    {
        try {
            $user = Auth::user();

            // Check if already checked in today
            $existing = Attendance::where('user_id', $user->id)
                ->whereDate('check_in', today())
                ->whereNull('check_out')
                ->first();

            if ($existing) {
                return response()->json(['success' => false, 'error' => 'You are already checked in'], 400);
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

        } catch (\Exception $e) {
            Log::error('Error checking in: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to check in'], 500);
        }
    }

    /**
     * Check out from the gym
     */
    public function checkOut()
    {
        try {
            $user = Auth::user();

            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('check_in', today())
                ->whereNull('check_out')
                ->first();

            if (!$attendance) {
                return response()->json(['success' => false, 'error' => 'No active check-in found'], 400);
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

        } catch (\Exception $e) {
            Log::error('Error checking out: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to check out'], 500);
        }
    }

    /**
     * Add nutrition log for today
     */
    public function addNutrition(Request $request)
    {
        try {
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

            return response()->json(['success' => true, 'nutrition' => $nutrition, 'message' => 'Nutrition logged successfully']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error adding nutrition: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to save nutrition'], 500);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markNotificationRead($notificationId)
    {
        try {
            $notification = Notification::where('user_id', Auth::id())
                ->findOrFail($notificationId);

            $notification->update([
                'read' => true,
                'read_at' => now()
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to mark notification as read'], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        try {
            Notification::where('user_id', Auth::id())
                ->where('read', false)
                ->update(['read' => true, 'read_at' => now()]);

            return response()->json(['success' => true, 'message' => 'All notifications marked as read']);

        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to mark notifications as read'], 500);
        }
    }

    /**
     * Send a message to instructor
     */
    public function sendMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000|min:1'
            ]);

            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $validated['receiver_id'],
                'message' => $validated['message'],
                'read' => false,
            ]);

            // Create notification for receiver
            Notification::create([
                'user_id' => $validated['receiver_id'],
                'type' => 'message',
                'title' => 'New Message from ' . Auth::user()->name,
                'message' => substr($validated['message'], 0, 100),
                'data' => ['message_id' => $message->id, 'sender_id' => Auth::id()]
            ]);

            return response()->json(['success' => true, 'message' => $message, 'sent' => 'Message sent successfully']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to send message'], 500);
        }
    }

    /**
     * Get messages with instructor (AJAX)
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
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark messages as read
            Message::where('receiver_id', Auth::id())
                ->where('sender_id', $userId)
                ->where('read', false)
                ->update(['read' => true, 'read_at' => now()]);

            $unreadCount = Message::where('receiver_id', Auth::id())
                ->where('read', false)
                ->count();

            return response()->json(['success' => true, 'messages' => $messages, 'unread_count' => $unreadCount]);

        } catch (\Exception $e) {
            Log::error('Error getting messages: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to load messages'], 500);
        }
    }

    /**
     * Add progress log (weight, measurements)
     */
    public function addProgress(Request $request)
    {
        try {
            $validated = $request->validate([
                'weight_kg' => 'required|numeric|min:20|max:300',
                'body_fat_percentage' => 'nullable|numeric|min:5|max:50',
                'chest_cm' => 'nullable|numeric|min:50|max:200',
                'waist_cm' => 'nullable|numeric|min:50|max:200',
                'hips_cm' => 'nullable|numeric|min:50|max:200',
                'notes' => 'nullable|string|max:500',
            ]);

            // Check if progress already logged today
            $existingProgress = ProgressLog::where('user_id', Auth::id())
                ->whereDate('date', today())
                ->first();

            if ($existingProgress) {
                $existingProgress->update($validated);
                $progress = $existingProgress;
            } else {
                $progress = ProgressLog::create([
                    'user_id' => Auth::id(),
                    'date' => today(),
                    ...$validated
                ]);
            }

            // Update weight goals
            Goal::where('user_id', Auth::id())
                ->where('type', 'weight')
                ->where('status', 'active')
                ->update(['current_value' => $validated['weight_kg']]);

            return response()->json(['success' => true, 'progress' => $progress, 'message' => 'Progress saved successfully']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error adding progress: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to save progress'], 500);
        }
    }

    /**
     * Create a new goal
     */
    public function createGoal(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'required|in:weight,workouts,attendance,strength,nutrition',
                'target_value' => 'required|numeric|min:1',
                'deadline' => 'required|date|after:today',
                'unit' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:500',
            ]);

            $goal = Goal::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'type' => $validated['type'],
                'target_value' => $validated['target_value'],
                'current_value' => 0,
                'deadline' => $validated['deadline'],
                'unit' => $validated['unit'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => 'active',
            ]);

            return response()->json(['success' => true, 'goal' => $goal, 'message' => 'Goal created successfully']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error creating goal: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to create goal'], 500);
        }
    }

    /**
     * Get dashboard statistics (AJAX)
     */
    public function getStats()
    {
        try {
            $user = Auth::user();

            $stats = [
                'total_workouts' => Workout::where('user_id', $user->id)->count(),
                'completed_workouts' => Workout::where('user_id', $user->id)->where('status', 'completed')->count(),
                'current_streak' => $this->calculateCurrentStreak($user->id),
                'attendance_rate' => $this->calculateAttendanceRate($user->id),
                'unread_messages' => Message::where('receiver_id', $user->id)->where('read', false)->count(),
                'unread_notifications' => Notification::where('user_id', $user->id)->where('read', false)->count(),
            ];

            return response()->json(['success' => true, 'stats' => $stats]);

        } catch (\Exception $e) {
            Log::error('Error getting stats: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to load statistics'], 500);
        }
    }
}
