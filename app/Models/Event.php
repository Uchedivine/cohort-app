<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Event extends Model
{
    use HasFactory, LogsActivity, Searchable;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'banner_image',
        'description',
        'start_date',
        'end_date',
        'location',
        'virtual_link',
        'rsvp_link',
        'sdgs',
        'status',
        'published_at',
    ];

    protected $casts = [
        'sdgs' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'published_at' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
        ];
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return $this->status === 'published';
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(EventMedia::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('start_date', '<', now());
    }
}