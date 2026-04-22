<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'read',
        'read_at',
        'is_deleted_by_sender',
        'is_deleted_by_receiver',
        'is_edited',
        'edited_at',
        'is_pinned',
        'pinned_at'
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_deleted_by_sender' => 'boolean',
        'is_deleted_by_receiver' => 'boolean',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'is_pinned' => 'boolean',
        'pinned_at' => 'datetime',
    ];

    protected $attributes = [
        'read' => false,
        'is_deleted_by_sender' => false,
        'is_deleted_by_receiver' => false,
        'is_edited' => false,
        'is_pinned' => false,
    ];

    /**
     * Get the sender of the message
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the other participant in the conversation
     */
    public function getOtherParticipant($userId)
    {
        if ($this->sender_id == $userId) {
            return $this->receiver;
        }
        if ($this->receiver_id == $userId) {
            return $this->sender;
        }
        return null;
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        if (!$this->read) {
            $this->update(['read' => true, 'read_at' => now()]);
        }
        return $this;
    }

    /**
     * Soft delete for sender
     */
    public function deleteForSender()
    {
        $this->update(['is_deleted_by_sender' => true]);

        if ($this->is_deleted_by_sender && $this->is_deleted_by_receiver) {
            $this->forceDelete();
        }

        return $this;
    }

    /**
     * Soft delete for receiver
     */
    public function deleteForReceiver()
    {
        $this->update(['is_deleted_by_receiver' => true]);

        if ($this->is_deleted_by_sender && $this->is_deleted_by_receiver) {
            $this->forceDelete();
        }

        return $this;
    }

    /**
     * Get formatted created at time
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('h:i A');
    }

    /**
     * Get formatted created at date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y');
    }

    /**
     * Get relative time
     */
    public function getRelativeTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Check if message is from a specific user
     */
    public function isFromUser($userId)
    {
        return $this->sender_id == $userId;
    }

    /**
     * Check if message is to a specific user
     */
    public function isToUser($userId)
    {
        return $this->receiver_id == $userId;
    }

    /**
     * Scope for messages between two users
     */
    public function scopeBetween(Builder $query, $user1Id, $user2Id)
    {
        return $query->where(function($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user1Id)->where('receiver_id', $user2Id);
        })->orWhere(function($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user2Id)->where('receiver_id', $user1Id);
        });
    }

    /**
     * Scope for messages not deleted for a specific user
     */
    public function scopeNotDeletedForUser(Builder $query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('sender_id', '!=', $userId)
              ->orWhere('is_deleted_by_sender', false);
        })->where(function($q) use ($userId) {
            $q->where('receiver_id', '!=', $userId)
              ->orWhere('is_deleted_by_receiver', false);
        });
    }

    /**
     * Get unread count for a user
     */
    public static function getUnreadCountForUser($userId)
    {
        try {
            return static::where('receiver_id', $userId)
                ->where('read', false)
                ->where('is_deleted_by_receiver', false)
                ->count();
        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Send a new message
     */
    public static function send($senderId, $receiverId, $message)
    {
        try {
            return static::create([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'message' => $message,
                'read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return null;
        }
    }
}
