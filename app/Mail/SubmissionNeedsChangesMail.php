<?php

namespace App\Mail;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionNeedsChangesMail extends Mailable
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
            subject: 'Changes Requested on Your Submission',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.submission-needs-changes',
            text: 'emails.submission-needs-changes-text',
            with: [
                'submittableTitle' => $this->getSubmittableTitle(),
                'submittableType' => $this->getSubmittableType(),
                'reviewerName' => $this->reviewer->name,
                'feedback' => $this->submission->reviewer_notes,
                'editUrl' => $this->getEditUrl(),
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

    /**
     * Get edit URL
     */
    private function getEditUrl(): ?string
    {
        $submittable = $this->submission->submittable;
        
        if (!$submittable) {
            return route('org-editor.dashboard');
        }

        return match(class_basename($this->submission->submittable_type)) {
            'Story' => route('org-editor.stories.edit', $submittable->id),
            'Organization' => route('org-editor.organization.edit'),
            default => route('org-editor.dashboard'),
        };
    }
}
