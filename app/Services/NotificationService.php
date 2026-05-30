<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Send submission status notification
     *
     * @param Submission $submission
     * @param string $status
     * @return void
     */
    public function sendSubmissionStatusNotification(Submission $submission, string $status): void
    {
        $recipient = $submission->submittedBy;

        if (!$recipient || !$recipient->email) {
            return;
        }

        $data = [
            'submission' => $submission,
            'status' => $status,
            'reviewer_notes' => $submission->reviewer_notes,
            'submittable_title' => $this->getSubmittableTitle($submission),
        ];

        // Queue email based on status
        match($status) {
            'approved' => $this->queueEmail($recipient, 'SubmissionApproved', $data),
            'rejected' => $this->queueEmail($recipient, 'SubmissionRejected', $data),
            'needs_changes' => $this->queueEmail($recipient, 'SubmissionNeedsChanges', $data),
            default => null,
        };
    }

    /**
     * Send event published notification to all org editors
     *
     * @param Event $event
     * @return void
     */
    public function sendEventPublishedNotification(Event $event): void
    {
        $recipients = $this->getOrgEditors();

        if ($recipients->isEmpty()) {
            return;
        }

        $data = [
            'event' => $event,
            'event_url' => route('events.show', $event->slug),
        ];

        foreach ($recipients as $recipient) {
            $this->queueEmail($recipient, 'EventPublished', $data);
        }
    }

    /**
     * Send monthly digest to all org editors
     *
     * @param array $digestData
     * @return void
     */
    public function sendMonthlyDigest(array $digestData): void
    {
        $recipients = $this->getOrgEditors();

        if ($recipients->isEmpty()) {
            return;
        }

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)->queue(
                    new \App\Mail\MonthlyDigestMail(
                        $recipient,
                        $digestData['events'],
                        $digestData['stories'],
                        $digestData['statistics']
                    )
                );
            } catch (\Exception $e) {
                logger()->error('Failed to send monthly digest', [
                    'recipient' => $recipient->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send welcome email to new user
     *
     * @param User $user
     * @param string $temporaryPassword
     * @return void
     */
    public function sendWelcomeEmail(User $user, string $temporaryPassword): void
    {
        try {
            Mail::to($user)->queue(new \App\Mail\WelcomeUserMail($user, $temporaryPassword));
        } catch (\Exception $e) {
            logger()->error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send bulk notification to multiple users
     *
     * @param Collection $users
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function sendBulkNotification(Collection $users, string $subject, string $message): void
    {
        foreach ($users as $user) {
            if (!$user->email) {
                continue;
            }

            $data = [
                'subject' => $subject,
                'message' => $message,
                'user' => $user,
            ];

            $this->queueEmail($user, 'BulkNotification', $data);
        }
    }

    /**
     * Queue an email for sending
     *
     * @param User $recipient
     * @param string $mailableClass
     * @param array $data
     * @return void
     */
    private function queueEmail(User $recipient, string $mailableClass, array $data): void
    {
        $mailClass = "App\\Mail\\{$mailableClass}";
        
        if (!class_exists($mailClass)) {
            logger()->warning("Mail class not found: {$mailClass}");
            return;
        }

        try {
            Mail::to($recipient)->queue(new $mailClass(...array_values($data)));
        } catch (\Exception $e) {
            logger()->error("Failed to queue email: {$mailClass}", [
                'recipient' => $recipient->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get all org editor users
     *
     * @return Collection
     */
    private function getOrgEditors(): Collection
    {
        return User::role('org_editor')
            ->whereNotNull('email')
            ->get();
    }

    /**
     * Get all secretary users
     *
     * @return Collection
     */
    private function getSecretaries(): Collection
    {
        return User::role('secretary')
            ->whereNotNull('email')
            ->get();
    }

    /**
     * Get submittable title for notification
     *
     * @param Submission $submission
     * @return string
     */
    private function getSubmittableTitle(Submission $submission): string
    {
        $submittable = $submission->submittable;

        if (!$submittable) {
            return 'Content';
        }

        // Try common title attributes
        if (isset($submittable->title)) {
            return $submittable->title;
        }

        if (isset($submittable->name)) {
            return $submittable->name;
        }

        return class_basename(get_class($submittable));
    }

    /**
     * Get notification preferences for a user
     *
     * @param User $user
     * @return array
     */
    public function getNotificationPreferences(User $user): array
    {
        // TODO: Implement user notification preferences
        // For now, return default preferences
        return [
            'email_on_submission_status' => true,
            'email_on_new_event' => true,
            'email_monthly_digest' => true,
        ];
    }

    /**
     * Update notification preferences for a user
     *
     * @param User $user
     * @param array $preferences
     * @return bool
     */
    public function updateNotificationPreferences(User $user, array $preferences): bool
    {
        // TODO: Implement user notification preferences storage
        // For now, just return true
        return true;
    }

    /**
     * Check if user should receive notification
     *
     * @param User $user
     * @param string $notificationType
     * @return bool
     */
    private function shouldNotify(User $user, string $notificationType): bool
    {
        $preferences = $this->getNotificationPreferences($user);
        
        return $preferences[$notificationType] ?? true;
    }

    /**
     * Get notification statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        // TODO: Implement notification tracking
        return [
            'total_sent' => 0,
            'pending' => 0,
            'failed' => 0,
        ];
    }
}
