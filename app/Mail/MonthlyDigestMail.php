<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class MonthlyDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $recipient,
        public Collection $events,
        public Collection $stories,
        public array $statistics
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $month = now()->format('F Y');
        
        return new Envelope(
            subject: "Cohort Monthly Digest - {$month}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.monthly-digest',
            text: 'emails.monthly-digest-text',
            with: [
                'userName' => $this->recipient->name,
                'month' => now()->format('F Y'),
                'events' => $this->events,
                'stories' => $this->stories,
                'statistics' => $this->statistics,
                'homeUrl' => route('home'),
            ],
        );
    }
}
