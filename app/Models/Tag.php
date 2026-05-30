<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    // Relationships
    public function organizations()
    {
        return $this->morphedByMany(Organization::class, 'taggable');
    }

    public function stories()
    {
        return $this->morphedByMany(Story::class, 'taggable');
    }

    public function resources()
    {
        return $this->morphedByMany(Resource::class, 'taggable');
    }

    public function events()
    {
        return $this->morphedByMany(Event::class, 'taggable');
    }

    // Scopes
    public function scopeGeneral($query)
    {
        return $query->where('type', 'general');
    }

    public function scopeSdg($query)
    {
        return $query->where('type', 'sdg');
    }

    public function scopeThematic($query)
    {
        return $query->where('type', 'thematic');
    }
}