<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatSession extends Model
{
    use HasFactory;

    protected $table = 'chat_sessions';

    protected $fillable = [
        'user_id',
        'title',
        'context_data',
        'last_message_at',
        'message_count',
        'is_active',
    ];

    protected $casts = [
        'context_data' => 'array',
        'last_message_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the messages for this session
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'chat_session_id');
    }

    /**
     * Get the latest message for preview
     */
    public function getPreviewAttribute(): string
    {
        $firstMessage = $this->messages()
            ->where('role', 'user')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($firstMessage) {
            $preview = substr($firstMessage->message, 0, 60);
            return strlen($firstMessage->message) > 60 ? $preview . '...' : $preview;
        }

        return 'New conversation';
    }

    /**
     * Get formatted last activity time
     */
    public function getLastActivityAttribute(): string
    {
        if ($this->last_message_at) {
            return $this->last_message_at->diffForHumans();
        }
        return $this->created_at->diffForHumans();
    }

    /**
     * Update message count and last message time
     */
    public function updateStats(): void
    {
        $this->update([
            'message_count' => $this->messages()->count(),
            'last_message_at' => now()
        ]);
    }

    /**
     * Increment message count
     */
    public function incrementMessageCount(): void
    {
        $this->increment('message_count');
        $this->update(['last_message_at' => now()]);
    }

    /**
     * Decrement message count
     */
    public function decrementMessageCount(): void
    {
        $this->decrement('message_count');
    }

    /**
     * Scope for active sessions only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for recent sessions
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('updated_at', 'desc')->limit($limit);
    }
}
