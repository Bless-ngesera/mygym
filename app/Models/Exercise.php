<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'muscle_group', 'difficulty', 'video_url', 'image_url'
    ];

    public function workouts()
    {
        return $this->belongsToMany(Workout::class, 'workout_exercises')
                    ->withPivot('id', 'sets', 'reps', 'rest_seconds', 'weight_kg', 'completed')
                    ->withTimestamps();
    }

    public function scopeByMuscleGroup($query, $group)
    {
        return $query->where('muscle_group', $group);
    }

    public function scopeBeginner($query)
    {
        return $query->where('difficulty', 'beginner');
    }

    public function scopeIntermediate($query)
    {
        return $query->where('difficulty', 'intermediate');
    }

    public function scopeAdvanced($query)
    {
        return $query->where('difficulty', 'advanced');
    }
}
