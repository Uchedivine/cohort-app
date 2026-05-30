<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EventMedia extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'event_id',
        'user_id',
        'media_type',
        'file_path',
        'video_url',
        'caption',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeImages($query)
    {
        return $query->where('media_type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('media_type', 'video');
    }
}