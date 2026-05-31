<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\MessageReply;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SecretaryReply extends Mailable
{
    use Queueable, SerializesModels;

    public Message $message;
    public MessageReply $reply;
    public User $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(Message $message, MessageReply $reply, User $recipient)
    {
        $this->message = $message;
        $this->reply = $reply;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: ' . $this->message->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.secretary-reply',
            with: [
                'message' => $this->message,
                'reply' => $this->reply,
                'recipient' => $this->recipient,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
