<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function checkAndUnlockAchievements($user, $action, $value = null)
    {
        $achievements = Achievement::where('type', $action)->get();

        foreach ($achievements as $achievement) {
            if (!$user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                $criteriaMet = $this->checkCriteria($user, $achievement, $value);

                if ($criteriaMet) {
                    $user->achievements()->attach($achievement->id, ['achieved_at' => now()]);

                    // Send notification
                    $this->notificationService->achievementUnlocked($user, $achievement);

                    // Award points if you have a points system
                    $user->increment('points', $achievement->points ?? 10);
                }
            }
        }
    }

    private function checkCriteria($user, $achievement, $value)
    {
        $criteria = json_decode($achievement->criteria, true);

        switch ($achievement->type) {
            case 'workout_count':
                $count = Workout::where('user_id', $user->id)->where('status', 'completed')->count();
                return $count >= ($criteria['count'] ?? 10);

            case 'streak':
                $streak = $this->calculateCurrentStreak($user);
                return $streak >= ($criteria['days'] ?? 7);

            case 'weight_loss':
                $firstWeight = ProgressLog::where('user_id', $user->id)->oldest()->first();
                $currentWeight = ProgressLog::where('user_id', $user->id)->latest()->first();
                if ($firstWeight && $currentWeight) {
                    $lost = $firstWeight->weight_kg - $currentWeight->weight_kg;
                    return $lost >= ($criteria['kg'] ?? 5);
                }
                return false;

            default:
                return false;
        }
    }
}
