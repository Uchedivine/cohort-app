<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'body',
        'sent_by',
        'recipient_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who sent the message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Get all recipients of this message
     */
    public function recipients(): HasMany
    {
        return $this->hasMany(MessageRecipient::class);
    }

    /**
     * Get all replies to this message
     */
    public function replies(): HasMany
    {
        return $this->hasMany(MessageReply::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the recipient record for a specific user
     */
    public function recipientFor(User $user)
    {
        return $this->recipients()->where('user_id', $user->id)->first();
    }

    /**
     * Check if message has been read by a specific user
     */
    public function isReadBy(User $user): bool
    {
        $recipient = $this->recipientFor($user);
        return $recipient && $recipient->read_at !== null;
    }

    /**
     * Mark message as read by a specific user
     */
    public function markAsReadBy(User $user): void
    {
        $this->recipients()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get count of unread recipients
     */
    public function unreadCount(): int
    {
        return $this->recipients()->whereNull('read_at')->count();
    }

    /**
     * Get count of total recipients
     */
    public function recipientCount(): int
    {
        return $this->recipients()->count();
    }
}
