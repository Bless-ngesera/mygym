<?php
// routes/channels.php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// Private channel for user notifications
Broadcast::channel('user.{userId}', function (User $user, int $userId) {
    return $user->id === $userId;
});

// Private channel for user-specific notifications
Broadcast::channel('notifications.{userId}', function (User $user, int $userId) {
    return $user->id === $userId;
});

// Private channel for admin notifications
Broadcast::channel('admin.notifications', function (User $user) {
    return $user->role === 'admin';
});
