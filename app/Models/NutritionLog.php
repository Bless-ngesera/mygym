<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutritionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date', 'calories', 'protein_grams', 'carbs_grams',
        'fat_grams', 'fiber_grams', 'sugar_grams', 'water_ml', 'notes'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function today($userId)
    {
        return static::firstOrCreate([
            'user_id' => $userId,
            'date' => today(),
        ]);
    }

    public function addMeal($calories, $protein, $carbs, $fat)
    {
        $this->increment('calories', $calories);
        $this->increment('protein_grams', $protein);
        $this->increment('carbs_grams', $carbs);
        $this->increment('fat_grams', $fat);
    }

    public function getCaloriesRemaining($dailyGoal = 2000)
    {
        return max(0, $dailyGoal - $this->calories);
    }
}
