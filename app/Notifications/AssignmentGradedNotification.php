<?php

namespace App\Notifications;

use App\Models\AssignmentSubmission;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentGradedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $submission;
    public $assignment;
    public $course;

    /**
     * Create a new notification instance.
     */
    public function __construct(AssignmentSubmission $submission, Assignment $assignment, Course $course)
    {
        $this->submission = $submission;
        $this->assignment = $assignment;
        $this->course = $course;
    }
    
    /**
     * Get rating emoji based on stars
     */
    private function getRatingEmoji($rating)
    {
        if (!$rating) return '⭐';
        if ($rating >= 5) return '🏆';
        if ($rating >= 4) return '⭐⭐⭐';
        if ($rating >= 3) return '👍';
        return '💪';
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
        $rating = $this->submission->rating ?? 0;
        $gradeEmoji = $this->getRatingEmoji($rating);
        
        $message = (new MailMessage)
            ->subject($gradeEmoji . ' تم تصحيح واجبك - ' . $courseTitle)
            ->greeting('مرحباً ' . $notifiable->name . '! 👋')
            ->line('**تم تصحيح واجبك!** ' . $gradeEmoji)
            ->line('المعلم قام بتصحيح واجبك في كورس **' . $courseTitle . '**')
            ->line('---')
            ->line('📋 **الواجب:** ' . $this->assignment->title)
            ->line('⭐ **التقييم:** ' . str_repeat('⭐', $rating) . ' (' . $rating . ' من 5)')
            ->line('📅 **تاريخ التسليم:** ' . $this->submission->submitted_at->format('Y-m-d h:i A'))
            ->line('📅 **تاريخ التصحيح:** ' . $this->submission->corrected_at->format('Y-m-d h:i A'));

        // إضافة ملاحظات المعلم إن وجدت
        if ($this->submission->teacher_notes) {
            $message->line('---')
                ->line('💬 **ملاحظات المعلم:**')
                ->line($this->submission->teacher_notes);
        }

        $message->line('---');

        // رسالة تشجيعية بناءً على التقييم
        if ($rating >= 5) {
            $message->line('🎉 **ممتاز جداً!** أداء رائع، واصل التميز!')
                ->line('أنت من الأوائل، استمر على هذا المستوى! 🌟');
        } elseif ($rating >= 4) {
            $message->line('👏 **أحسنت!** أداء جيد جداً')
                ->line('مع القليل من المجهود ستصل للامتياز! 💪');
        } elseif ($rating >= 3) {
            $message->line('👍 **جيد!** أداء مقبول')
                ->line('يمكنك تحسين أدائك في الواجبات القادمة 📈');
        } else {
            $message->line('💪 **لا تيأس!** هذه فرصة للتعلم')
                ->line('راجع المادة جيداً واسأل معلمك عن أي استفسار 📚');
        }

        $message->action('📄 شاهد التفاصيل والملاحظات', route('student.courses.show', $this->assignment->meeting->course_id))
            ->salutation('بالتوفيق دائماً! 🌟' . PHP_EOL . 'فريق ' . config('app.name'));

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $rating = $this->submission->rating ?? 0;
        $emoji = $this->getRatingEmoji($rating);
        
        return [
            'type' => 'assignment_graded',
            'title' => 'تم تصحيح واجبك ' . $emoji,
            'message' => 'تم تصحيح واجب: ' . $this->assignment->title . ' - التقييم: ' . str_repeat('⭐', $rating),
            'submission_id' => $this->submission->id,
            'assignment_id' => $this->assignment->id,
            'course_id' => $this->course->id,
            'course_title' => $this->course->title_ar ?? $this->course->title,
            'assignment_title' => $this->assignment->title,
            'rating' => $rating,
            'teacher_notes' => $this->submission->teacher_notes,
            'corrected_at' => $this->submission->corrected_at,
            'icon' => 'star',
            'color' => $rating >= 4 ? 'green' : ($rating >= 3 ? 'blue' : 'orange'),
            'action_url' => route('student.courses.show', $this->course->id),
        ];
    }
}
