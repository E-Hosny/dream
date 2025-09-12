<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\AssignmentSubmission;
use App\Models\User;

class AssignmentSubmitted extends Notification
{
    use Queueable;

    public $submission;
    public $student;

    /**
     * Create a new notification instance.
     */
    public function __construct(AssignmentSubmission $submission, User $student)
    {
        $this->submission = $submission;
        $this->student = $student;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('حل جديد للواجب: ' . $this->submission->assignment->title)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم رفع حل جديد للواجب: ' . $this->submission->assignment->title)
            ->line('الطالب: ' . $this->student->name)
            ->line('الكورس: ' . $this->submission->assignment->meeting->course->title_ar)
            ->action('عرض الحلول', route('assignments.submissions', $this->submission->assignment->id))
            ->line('شكراً لاستخدام منصة EduDream!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'assignment_submitted',
            'title' => 'حل جديد',
            'message' => 'تم رفع حل جديد للواجب: ' . $this->submission->assignment->title,
            'student_name' => $this->student->name,
            'course_title' => $this->submission->assignment->meeting->course->title_ar,
            'assignment_id' => $this->submission->assignment->id,
            'submission_id' => $this->submission->id,
            'meeting_id' => $this->submission->assignment->meeting_id,
            'course_id' => $this->submission->assignment->meeting->course_id,
            'assignment_title' => $this->submission->assignment->title,
            'submission_file_name' => $this->submission->submission_file_name,
            'created_at' => now()->toISOString(),
            'icon' => 'submission',
            'color' => 'green',
            'action_url' => route('assignments.submissions', $this->submission->assignment->id),
        ];
    }
}
