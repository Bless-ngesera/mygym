<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'scheduled_class_id',
    ];

    public $timestamps = true;

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to ScheduledClass
    public function scheduledClass()
    {
        return $this->belongsTo(ScheduledClass::class, 'scheduled_class_id');
    }
}
