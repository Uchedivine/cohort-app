<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ContentRevision extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'revisable_type',
        'revisable_id',
        'user_id',
        'old_data',
        'new_data',
        'status',
        'reviewer_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    // Polymorphic relationship
    public function revisable()
    {
        return $this->morphTo();
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}