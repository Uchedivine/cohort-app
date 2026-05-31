<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Submission extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'submittable_type',
        'submittable_id',
        'submitted_by',
        'reviewed_by',
        'status',
        'reviewer_notes',
        'allow_resubmission',
        'parent_submission_id',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'allow_resubmission' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    // Polymorphic relationship
    public function submittable()
    {
        return $this->morphTo();
    }

    // Relationships
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the parent submission (if this is a resubmission)
     */
    public function parentSubmission()
    {
        return $this->belongsTo(Submission::class, 'parent_submission_id');
    }

    /**
     * Get child submissions (resubmissions of this submission)
     */
    public function childSubmissions()
    {
        return $this->hasMany(Submission::class, 'parent_submission_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeNeedsChanges($query)
    {
        return $query->where('status', 'needs_changes');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('submittable_type', $type);
    }
}