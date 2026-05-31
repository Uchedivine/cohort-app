<?php

namespace App\Mail;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrganisationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Organization $organization) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update on Your Organisation Application — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.organisation-rejected',
        );
    }
}