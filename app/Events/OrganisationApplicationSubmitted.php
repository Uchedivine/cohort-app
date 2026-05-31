<?php

namespace App\Events;

use App\Models\Organization;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrganisationApplicationSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Organization $organization) {}
}