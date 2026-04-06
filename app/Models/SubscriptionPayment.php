<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id', 'subscription_id', 'transaction_id', 'amount',
        'payment_method', 'payment_date', 'status', 'notes'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function subscription()
    {
        return $this->belongsTo(MemberSubscription::class);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}
