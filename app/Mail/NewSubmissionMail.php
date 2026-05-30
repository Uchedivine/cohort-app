<?php

namespace App\Mail;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Submission $submission,
        public User $secretary
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Submission Awaiting Review',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-submission',
            text: 'emails.new-submission-text',
            with: [
                'submittableTitle' => $this->getSubmittableTitle(),
                'submittableType' => $this->getSubmittableType(),
                'submitterName' => $this->submission->submittedBy->name,
                'organizationName' => $this->submission->submittedBy->organization->name ?? 'N/A',
                'submittedAt' => $this->submission->submitted_at,
                'reviewUrl' => route('secretary.submissions.show', $this->submission->id),
                'queueUrl' => route('secretary.submissions.index'),
            ],
        );
    }

    /**
     * Get submittable title
     */
    private function getSubmittableTitle(): string
    {
        $submittable = $this->submission->submittable;
        return $submittable->title ?? $submittable->name ?? 'Content';
    }

    /**
     * Get submittable type
     */
    private function getSubmittableType(): string
    {
        return class_basename($this->submission->submittable_type);
    }
}
