<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date', 'weight_kg', 'body_fat_percentage',
        'chest_cm', 'waist_cm', 'hips_cm', 'biceps_cm', 'thighs_cm', 'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'weight_kg' => 'decimal:2',
        'body_fat_percentage' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->whereDate('date', '>=', now()->subDays($days));
    }

    public function getWeightTrend()
    {
        $previous = ProgressLog::where('user_id', $this->user_id)
            ->whereDate('date', '<', $this->date)
            ->orderBy('date', 'desc')
            ->first();

        if (!$previous) return 0;
        return $previous->weight_kg - $this->weight_kg;
    }
}
