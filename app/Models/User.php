<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'organization_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'submitted_by');
    }

    public function reviews()
    {
        return $this->hasMany(Submission::class, 'reviewed_by');
    }

    // Helper methods
    public function isSecretary()
    {
        return $this->hasRole('secretary');
    }

    public function isOrgEditor()
    {
        return $this->hasRole('org_editor');
    }
}