<?php

namespace App\Notifications;

use App\Models\ZoomMeeting;
use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingStartedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $meeting;
    public $course;

    /**
     * Create a new notification instance.
     */
    public function __construct(ZoomMeeting $meeting, Course $course)
    {
        $this->meeting = $meeting;
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
        $teacherName = $this->meeting->creator->name ?? 'المعلم';
        
        return (new MailMessage)
            ->subject('🎓 بدأ الاجتماع الآن - ' . $courseTitle)
            ->greeting('مرحباً ' . $notifiable->name . '! 👋')
            ->line('**بدأ الاجتماع الآن!** 🚀')
            ->line('المعلم ' . $teacherName . ' بدأ الاجتماع المباشر لكورس **' . $courseTitle . '**')
            ->line('📅 **الوقت:** ' . $this->meeting->actual_start_time->format('Y-m-d h:i A'))
            ->line('⏱️ **المدة المتوقعة:** ' . $this->meeting->duration . ' دقيقة')
            ->line('📝 **الموضوع:** ' . $this->meeting->topic)
            ->action('🎥 انضم للاجتماع الآن', 'https://app.inskola.net/student/dashboard')
            ->line('**مهم:** لا تتأخر! الاجتماع بدأ بالفعل.')
            ->line('💡 **نصيحة:** تأكد من اتصال الإنترنت والكاميرا والمايك قبل الانضمام.')
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
            'type' => 'meeting_started',
            'title' => 'بدأ الاجتماع الآن! 🚀',
            'message' => 'بدأ اجتماع كورس ' . ($this->course->title_ar ?? $this->course->title),
            'meeting_id' => $this->meeting->id,
            'course_id' => $this->course->id,
            'course_title' => $this->course->title_ar ?? $this->course->title,
            'topic' => $this->meeting->topic,
            'start_time' => $this->meeting->actual_start_time,
            'icon' => 'video',
            'color' => 'green',
            'action_url' => 'https://app.inskola.net/student/dashboard',
        ];
    }
}
