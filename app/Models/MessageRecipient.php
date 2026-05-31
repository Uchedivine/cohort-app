<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'organization_id',
        'user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the message this recipient belongs to
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Get the organization this recipient belongs to
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user this recipient is for
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this message has been read
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Mark this message as read
     */
    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }
}
