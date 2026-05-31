<?php

namespace App\Listeners;

use App\Events\OrganisationApplicationSubmitted;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotifySecretaryOfNewApplication implements ShouldQueue
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
            Mail::send(
                'emails.new-application',
                ['organization' => $event->organization, 'secretary' => $secretary],
                function ($msg) use ($secretary, $event) {
                    $msg->to($secretary->email)
                        ->subject('New Organisation Application — ' . $event->organization->name);
                }
            );
        }
    }

    public function failed(OrganisationApplicationSubmitted $event, \Throwable $exception): void
    {
        logger()->error('Failed to notify secretary of new application', [
            'organization_id' => $event->organization->id,
            'error'           => $exception->getMessage(),
        ]);
    }
}