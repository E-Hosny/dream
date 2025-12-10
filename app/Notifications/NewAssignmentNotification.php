<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $assignment;
    public $course;

    /**
     * Create a new notification instance.
     */
    public function __construct(Assignment $assignment, Course $course)
    {
        $this->assignment = $assignment;
        $this->course = $course;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $courseTitle = $this->course->title_ar ?? $this->course->title;
        
        return (new MailMessage)
            ->subject('📝 واجب جديد - ' . $courseTitle)
            ->greeting('مرحباً ' . $notifiable->name . '! 👋')
            ->line('**تم إضافة واجب جديد!** 🆕')
            ->line('المعلم أضاف واجباً جديداً في كورس **' . $courseTitle . '**')
            ->line('---')
            ->line('📋 **عنوان الواجب:** ' . $this->assignment->title)
            ->line('📝 **الوصف:** ' . ($this->assignment->description ?? 'لا يوجد وصف'))
            ->line('📅 **تاريخ الإضافة:** ' . $this->assignment->created_at->format('Y-m-d h:i A'))
            ->line('📎 **اسم الملف:** ' . $this->assignment->file_name)
            ->line('---')
            ->action('📄 شاهد الواجب وابدأ الحل', 'https://app.inskola.net/student/dashboard')
            ->line('**نصائح للنجاح:**')
            ->line('• اقرأ التعليمات بعناية')
            ->line('• ابدأ الحل مبكراً، لا تؤجل!')
            ->line('• راجع عملك قبل التسليم')
            ->line('• لا تتردد في سؤال معلمك عن أي استفسار')
            ->line('💡 **تذكير:** الالتزام بالمواعيد مهم جداً لنجاحك الأكاديمي.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_assignment',
            'title' => 'واجب جديد 📝',
            'message' => 'تم إضافة واجب جديد: ' . $this->assignment->title,
            'assignment_id' => $this->assignment->id,
            'course_id' => $this->course->id,
            'course_title' => $this->course->title_ar ?? $this->course->title,
            'assignment_title' => $this->assignment->title,
            'file_name' => $this->assignment->file_name,
            'created_at' => $this->assignment->created_at,
            'icon' => 'document',
            'color' => 'blue',
            'action_url' => 'https://app.inskola.net/student/dashboard',
        ];
    }
}
