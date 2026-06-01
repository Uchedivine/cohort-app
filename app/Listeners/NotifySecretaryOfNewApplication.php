<?php

namespace App\Listeners;

use App\Events\OrganisationApplicationSubmitted;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotifySecretaryOfNewApplication
{
    public function handle(OrganisationApplicationSubmitted $event): void
    {
        $secretaries = User::role('secretary')
            ->whereNotNull('email')
            ->get();

        if ($secretaries->isEmpty()) {
            return;
        }

        foreach ($secretaries as $secretary) {
            try {
                Mail::send(
                    'emails.new-application',
                    ['organization' => $event->organization, 'secretary' => $secretary],
                    function ($msg) use ($secretary, $event) {
                        $msg->to($secretary->email)
                            ->subject('New Organisation Application — ' . $event->organization->name);
                    }
                );
            } catch (\Exception $e) {
                logger()->error('Failed to notify secretary of new application', [
                    'organization_id' => $event->organization->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}