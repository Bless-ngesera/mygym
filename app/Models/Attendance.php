<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'user_id', 'check_in', 'check_out', 'duration_minutes', 'status'
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('check_in', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereDate('check_in', '>=', now()->startOfWeek());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereDate('check_in', '>=', now()->startOfMonth());
    }

    public function isCheckedIn()
    {
        return $this->status === 'checked_in' && !$this->check_out;
    }

    public function checkOut()
    {
        $this->update([
            'check_out' => now(),
            'duration_minutes' => now()->diffInMinutes($this->check_in),
            'status' => 'checked_out'
        ]);
    }
}
