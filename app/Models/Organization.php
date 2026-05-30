<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Organization extends Model
{
    use HasFactory, LogsActivity, Searchable;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'logo',
        'short_description',
        'full_profile',
        'location',
        'thematic_focus',
        'sdgs',
        'website',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'contact_email',
        'contact_phone',
        'programs',
        'highlights',
        'status',
    ];

    protected $casts = [
        'sdgs' => 'array',
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
            'name' => $this->name,
            'short_description' => $this->short_description,
            'full_profile' => $this->full_profile,
            'location' => $this->location,
            'thematic_focus' => $this->thematic_focus,
            'programs' => $this->programs,
            'highlights' => $this->highlights,
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

    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
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