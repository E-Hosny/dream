<?php

namespace App\Notifications;

use App\Models\ZoomMeeting;
use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingEndedMissedNotification extends Notification implements ShouldQueue
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
            ->subject('⚠️ فاتك الاجتماع - ' . $courseTitle)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('**للأسف، لم تحضر الاجتماع** 😔')
            ->line('انتهى اجتماع كورس **' . $courseTitle . '** ولم نسجل حضورك.')
            ->line('📅 **وقت الاجتماع:** ' . $this->meeting->actual_start_time->format('Y-m-d h:i A'))
            ->line('⏱️ **المدة:** ' . $this->meeting->duration . ' دقيقة')
            ->line('📝 **الموضوع:** ' . $this->meeting->topic)
            ->line('👨‍🏫 **المعلم:** ' . $teacherName)
            ->line('---')
            ->line('**ماذا تفعل الآن؟**')
            ->line('• راجع تسجيل الاجتماع إن وُجد')
            ->line('• تواصل مع زملائك لمعرفة ما فاتك')
            ->line('• احرص على الحضور في المرة القادمة')
            ->action('📚 اذهب للكورس', 'https://app.inskola.net/student/dashboard')
            ->line('**تذكير:** الحضور المنتظم مهم جداً لنجاحك الأكاديمي! 💪')
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
            'type' => 'meeting_missed',
            'title' => 'فاتك الاجتماع ⚠️',
            'message' => 'لم تحضر اجتماع كورس ' . ($this->course->title_ar ?? $this->course->title),
            'meeting_id' => $this->meeting->id,
            'course_id' => $this->course->id,
            'course_title' => $this->course->title_ar ?? $this->course->title,
            'topic' => $this->meeting->topic,
            'start_time' => $this->meeting->actual_start_time,
            'end_time' => $this->meeting->actual_end_time,
            'icon' => 'warning',
            'color' => 'orange',
            'action_url' => 'https://app.inskola.net/student/dashboard',
        ];
    }
}
