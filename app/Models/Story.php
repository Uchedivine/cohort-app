<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Story extends Model
{
    use HasFactory, LogsActivity, Searchable;

    protected $fillable = [
        'organization_id',
        'user_id',
        'title',
        'slug',
        'featured_image',
        'summary',
        'full_story',
        'author',
        'sdgs',
        'problem',
        'approach',
        'outcome',
        'lessons',
        'status',
        'published_at',
    ];

    protected $casts = [
        'sdgs' => 'array',
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
            'summary' => $this->summary,
            'full_story' => $this->full_story,
            'author' => $this->author,
            'problem' => $this->problem,
            'approach' => $this->approach,
            'outcome' => $this->outcome,
            'lessons' => $this->lessons,
            'organization_name' => $this->organization->name ?? null,
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
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function submissions()
    {
        return $this->morphMany(Submission::class, 'submittable');
    }

    public function revisions()
    {
        return $this->morphMany(ContentRevision::class, 'revisable');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}