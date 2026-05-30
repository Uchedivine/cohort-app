<?php

namespace App\Mail;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionApprovedMail extends Mailable
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
            subject: 'Your Submission Has Been Approved',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.submission-approved',
            text: 'emails.submission-approved-text',
            with: [
                'submittableTitle' => $this->getSubmittableTitle(),
                'submittableType' => $this->getSubmittableType(),
                'reviewerName' => $this->reviewer->name,
                'reviewerNotes' => $this->submission->reviewer_notes,
                'viewUrl' => $this->getViewUrl(),
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

    /**
     * Get view URL
     */
    private function getViewUrl(): ?string
    {
        $submittable = $this->submission->submittable;
        
        if (!$submittable || !isset($submittable->slug)) {
            return null;
        }

        return match(class_basename($this->submission->submittable_type)) {
            'Story' => route('stories.show', $submittable->slug),
            'Resource' => route('resources.show', $submittable->slug),
            'Event' => route('events.show', $submittable->slug),
            'Organization' => route('organizations.show', $submittable->slug),
            default => null,
        };
    }
}
