// app/Models/NotificationAnalytic.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationAnalytic extends Model
{
    protected $fillable = [
        'notification_id', 'user_id', 'type', 'priority',
        'sent_at', 'delivered_at', 'read_at', 'time_to_read_seconds'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
