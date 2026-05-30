<?php

namespace App\Mail;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Submission $submission,
        public User $reviewer
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Submission Has Been Rejected',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.submission-rejected',
            text: 'emails.submission-rejected-text',
            with: [
                'submittableTitle' => $this->getSubmittableTitle(),
                'submittableType' => $this->getSubmittableType(),
                'reviewerName' => $this->reviewer->name,
                'reason' => $this->submission->reviewer_notes,
                'dashboardUrl' => route('org-editor.dashboard'),
            ],
        );
    }

    /**
     * Get submittable title
     */
    private function getSubmittableTitle(): string
    {
        $submittable = $this->submission->submittable;
        return $submittable->title ?? $submittable->name ?? 'Your content';
    }

    /**
     * Get submittable type
     */
    private function getSubmittableType(): string
    {
        return class_basename($this->submission->submittable_type);
    }
}
