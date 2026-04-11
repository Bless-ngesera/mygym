<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
        'user_id',
        'chat_session_id',
        'role',
        'message',
        'tokens_used',
        'response_time_ms',
        'context_type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the chat session that owns the message
     */
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }

    /**
     * Scope for user messages
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for user role messages
     */
    public function scopeUserMessages($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Scope for assistant messages
     */
    public function scopeAssistantMessages($query)
    {
        return $query->where('role', 'assistant');
    }

    /**
     * Scope for specific session
     */
    public function scopeFromSession($query, $sessionId)
    {
        return $query->where('chat_session_id', $sessionId);
    }

    /**
     * Get short preview of message
     */
    public function getPreviewAttribute(): string
    {
        $preview = substr($this->message, 0, 100);
        return strlen($this->message) > 100 ? $preview . '...' : $preview;
    }

    /**
     * Get formatted message with markdown
     */
    public function getFormattedMessageAttribute(): string
    {
        $message = $this->message;

        // Convert markdown-like syntax to HTML
        $message = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $message);
        $message = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $message);
        $message = nl2br($message);

        return $message;
    }

    /**
     * Check if message is from user
     */
    public function isFromUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if message is from AI
     */
    public function isFromAssistant(): bool
    {
        return $this->role === 'assistant';
    }
}
