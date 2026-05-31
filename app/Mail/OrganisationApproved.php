<?php

namespace App\Mail;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrganisationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Organization $organization) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Organisation Has Been Approved — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.organisation-approved',
        );
    }
}