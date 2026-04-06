<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutExercise extends Model
{
    use HasFactory;

    protected $table = 'workout_exercises';

    protected $fillable = [
        'workout_id', 'exercise_id', 'sets', 'reps', 'rest_seconds', 'weight_kg', 'completed'
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function markAsComplete()
    {
        $this->update(['completed' => true]);
    }
}
