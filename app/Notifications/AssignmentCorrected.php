<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\AssignmentSubmission;
use App\Models\User;

class AssignmentCorrected extends Notification
{
    use Queueable;

    public $submission;
    public $teacher;

    /**
     * Create a new notification instance.
     */
    public function __construct(AssignmentSubmission $submission, User $teacher)
    {
        $this->submission = $submission;
        $this->teacher = $teacher;
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
            ->subject('تم تصحيح واجبك: ' . $this->submission->assignment->title)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم تصحيح واجبك: ' . $this->submission->assignment->title)
            ->line('المعلم: ' . $this->teacher->name)
            ->line('التقييم: ' . ($this->submission->rating ?? 0) . ' من 5 نجوم')
            ->line('الكورس: ' . $this->submission->assignment->meeting->course->title_ar)
            ->action('عرض التصحيح', route('student.courses.show', $this->submission->assignment->meeting->course_id))
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
            'type' => 'assignment_corrected',
            'title' => 'تم تصحيح واجبك',
            'message' => 'تم تصحيح واجبك: ' . $this->submission->assignment->title . ' (' . ($this->submission->rating ?? 0) . ' من 5 نجوم)',
            'teacher_name' => $this->teacher->name,
            'course_title' => $this->submission->assignment->meeting->course->title_ar,
            'assignment_id' => $this->submission->assignment->id,
            'submission_id' => $this->submission->id,
            'meeting_id' => $this->submission->assignment->meeting_id,
            'course_id' => $this->submission->assignment->meeting->course_id,
            'assignment_title' => $this->submission->assignment->title,
            'rating' => $this->submission->rating,
            'stars_text' => ($this->submission->rating ?? 0) . ' من 5 نجوم',
            'correction_file_name' => $this->submission->correction_file_name,
            'teacher_notes' => $this->submission->teacher_notes,
            'created_at' => now()->toISOString(),
            'icon' => 'correction',
            'color' => 'purple',
            'action_url' => route('student.courses.show', $this->submission->assignment->meeting->course_id),
        ];
    }
}
