<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $temporaryPassword
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Cohort Web App',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-user',
            text: 'emails.welcome-user-text',
            with: [
                'userName' => $this->user->name,
                'userEmail' => $this->user->email,
                'temporaryPassword' => $this->temporaryPassword,
                'loginUrl' => route('login'),
                'organizationName' => $this->user->organization->name ?? null,
                'role' => $this->user->roles->first()->name ?? 'user',
            ],
        );
    }
}
