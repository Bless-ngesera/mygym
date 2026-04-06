<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id', 'plan_name', 'description', 'price', 'start_date',
        'end_date', 'status', 'billing_cycle', 'payment_method'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->end_date->isFuture();
    }

    public function isExpired()
    {
        return $this->end_date->isPast() || $this->status === 'expired';
    }

    public function daysRemaining()
    {
        if (!$this->isActive()) return 0;
        return now()->diffInDays($this->end_date, false);
    }

    public function getProgressPercentage()
    {
        $totalDays = $this->start_date->diffInDays($this->end_date);
        $daysUsed = $this->start_date->diffInDays(now());
        return min(100, round(($daysUsed / $totalDays) * 100));
    }

    public function getTotalPaid()
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }
}
