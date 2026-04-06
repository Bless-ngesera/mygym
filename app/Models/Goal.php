<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'type', 'target_value',
        'current_value', 'unit', 'deadline', 'status', 'achieved_at'
    ];

    protected $casts = [
        'deadline' => 'date',
        'achieved_at' => 'datetime',
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function progressPercentage()
    {
        if ($this->target_value == 0) return 0;
        return min(100, round(($this->current_value / $this->target_value) * 100));
    }

    public function isAchieved()
    {
        return $this->status === 'achieved';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function daysRemaining()
    {
        if ($this->isAchieved()) return 0;
        return now()->diffInDays($this->deadline, false);
    }

    public function incrementProgress($value)
    {
        $this->increment('current_value', $value);

        if ($this->current_value >= $this->target_value && !$this->isAchieved()) {
            $this->update([
                'status' => 'achieved',
                'achieved_at' => now()
            ]);
        }
    }
}
