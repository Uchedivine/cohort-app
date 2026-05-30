<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Resource extends Model
{
    use HasFactory, LogsActivity, Searchable;

    protected $fillable = [
        'organization_id',
        'user_id',
        'title',
        'slug',
        'description',
        'resource_type',
        'file_path',
        'external_url',
        'mime_type',
        'file_size',
        'theme',
        'year',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'date',
        'file_size' => 'integer',
        'year' => 'integer',
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
            'resource_type' => $this->resource_type,
            'theme' => $this->theme,
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

    public function scopeOfType($query, $type)
    {
        return $query->where('resource_type', $type);
    }

    public function scopeOfTheme($query, $theme)
    {
        return $query->where('theme', $theme);
    }
}