<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'instructor_id',
        'amount',
        'paid_at',
        'status',
        'reference'
    ];

    protected $dates = ['paid_at'];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->reference)) {
                $payment->reference = 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            }
            if (empty($payment->status)) {
                $payment->status = 'pending';
            }
        });
    }
}
