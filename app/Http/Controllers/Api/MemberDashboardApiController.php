<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workout;
use App\Models\ProgressLog;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MemberDashboardApiController extends Controller
{
    // Remove the constructor completely

    public function getStats()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'total_workouts' => Workout::where('user_id', $user->id)->count(),
                'completed_workouts' => Workout::where('user_id', $user->id)->where('status', 'completed')->count(),
                'total_hours' => floor(Attendance::where('user_id', $user->id)->sum('duration_minutes') / 60),
                'current_streak' => $this->getCurrentStreak($user->id),
            ]
        ]);
    }

    public function getWeightProgress()
    {
        $progressData = ProgressLog::where('user_id', Auth::id())
            ->orderBy('date', 'asc')
            ->take(30)
            ->get();

        return response()->json([
            'success' => true,
            'labels' => $progressData->pluck('date')->map(fn($d) => $d->format('M d')),
            'values' => $progressData->pluck('weight_kg')
        ]);
    }

    public function getWorkoutFrequency()
    {
        $data = Workout::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereDate('date', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(date) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'labels' => $data->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d')),
            'values' => $data->pluck('count')
        ]);
    }

    private function getCurrentStreak($userId)
    {
        $streak = 0;
        $currentDate = now()->startOfDay();

        while (true) {
            $hasWorkout = Workout::where('user_id', $userId)
                ->whereDate('date', $currentDate)
                ->where('status', 'completed')
                ->exists();

            if (!$hasWorkout) break;
            $streak++;
            $currentDate->subDay();
        }

        return $streak;
    }
}
