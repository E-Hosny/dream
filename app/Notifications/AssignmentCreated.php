<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Assignment;
use App\Models\User;

class AssignmentCreated extends Notification
{
    use Queueable;

    public $assignment;
    public $teacher;

    /**
     * Create a new notification instance.
     */
    public function __construct(Assignment $assignment, User $teacher)
    {
        $this->assignment = $assignment;
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
            ->subject('واجب جديد: ' . $this->assignment->title)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم رفع واجب جديد بعنوان: ' . $this->assignment->title)
            ->line('المعلم: ' . $this->teacher->name)
            ->line('الكورس: ' . $this->assignment->meeting->course->title_ar)
            ->action('عرض الواجب', 'https://app.inskola.net/student/dashboard')
            ->line('شكراً لاستخدام منصة inskola!')
            ->salutation('');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'assignment_created',
            'title' => 'واجب جديد',
            'message' => 'تم رفع واجب جديد في مادة ' . $this->assignment->meeting->course->title_ar . ' بعنوان: ' . $this->assignment->title,
            'teacher_name' => $this->teacher->name,
            'course_title' => $this->assignment->meeting->course->title_ar,
            'assignment_id' => $this->assignment->id,
            'meeting_id' => $this->assignment->meeting_id,
            'course_id' => $this->assignment->meeting->course_id,
            'assignment_title' => $this->assignment->title,
            'file_name' => $this->assignment->file_name,
            'created_at' => now()->toISOString(),
            'icon' => 'assignment',
            'color' => 'blue',
            'action_url' => '/student/courses/' . $this->assignment->meeting->course_id,
        ];
    }
}
