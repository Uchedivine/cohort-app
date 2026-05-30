<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Event $event,
        public User $recipient
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Event: ' . $this->event->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.event-published',
            text: 'emails.event-published-text',
            with: [
                'eventTitle' => $this->event->title,
                'eventDescription' => $this->event->description,
                'startDate' => $this->event->start_date,
                'endDate' => $this->event->end_date,
                'location' => $this->event->location,
                'virtualLink' => $this->event->virtual_link,
                'rsvpLink' => $this->event->rsvp_link,
                'eventUrl' => route('events.show', $this->event->slug),
            ],
        );
    }
}
