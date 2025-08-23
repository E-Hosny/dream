<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Auth;
use App\Models\ZoomMeeting;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // جلب الكورسات المسجلة للطالب مع معلومات الكورس والمدرب
        $enrollments = CourseEnrollment::with(['course.instructor', 'course.schedules'])
            ->where('student_id', $user->id)
            ->get()
            ->map(function ($enrollment) {
                $nextSchedule = $enrollment->course->next_schedule;
                
                return [
                    'id' => $enrollment->id,
                    'title' => $enrollment->course->title_ar,
                    'titleEn' => $enrollment->course->title,
                    'progress' => (float) $enrollment->progress,
                    'lastAccessed' => $enrollment->updated_at ? $enrollment->updated_at->diffForHumans() : 'لم يبدأ بعد',
                    'instructor' => $enrollment->course->instructor->name,
                    'status' => $this->getStatusFromProgress($enrollment->progress),
                    'color' => $this->getColorFromProgress($enrollment->progress),
                    'enrolled_at' => $enrollment->enrolled_at,
                    'completed_at' => $enrollment->completed_at,
                    'schedules' => $enrollment->course->schedules->map(function ($schedule) {
                        return [
                            'id' => $schedule->id,
                            'day' => $schedule->localized_day_name,
                            'start_time' => $schedule->start_time->format('H:i'),
                            'end_time' => $schedule->end_time->format('H:i'),
                            'is_active' => $schedule->is_active,
                        ];
                    }),
                    'nextSchedule' => $nextSchedule ? [
                        'day' => $nextSchedule->localized_day_name,
                        'time' => $nextSchedule->start_time->format('H:i'),
                        'nextOccurrence' => $nextSchedule->next_occurrence->diffForHumans()
                    ] : null,
                    'course' => [
                        'title' => $enrollment->course->title_ar,
                        'titleEn' => $enrollment->course->title,
                        'instructor' => $enrollment->course->instructor->name,
                    ]
                ];
            });

        return Inertia::render('Student/Dashboard', [
            'enrollments' => $enrollments,
            'locale' => app()->getLocale(),
        ]);
    }

    /**
     * الحصول على الاجتماعات النشطة للطالب
     */
    public function getActiveMeetings()
    {
        $user = Auth::user();
        
        // جلب الاجتماعات النشطة للكورسات المسجل فيها الطالب
        $meetings = ZoomMeeting::with(['course', 'zoomAccount'])
            ->whereHas('course.enrollments', function ($query) use ($user) {
                $query->where('student_id', $user->id);
            })
            ->whereIn('status', ['started', 'created']) // تحديث الفلتر ليشمل الاجتماعات المنشأة والنشطة
            ->orderBy('start_time', 'desc')
            ->get()
            ->map(function ($meeting) {
                return [
                    'id' => $meeting->id,
                    'course_title' => $meeting->course->title_ar ?? $meeting->course->title,
                    'topic' => $meeting->topic,
                    'start_time' => $meeting->start_time->format('Y-m-d H:i'),
                    'duration' => $meeting->duration,
                    'join_url' => $meeting->join_url,
                    'password' => $meeting->password,
                    'status' => $meeting->status,
                    'status_text' => $meeting->status === 'started' ? 'نشط' : 'مجدول'
                ];
            });

        return response()->json([
            'success' => true,
            'meetings' => $meetings
        ]);
    }

    private function getStatusFromProgress($progress)
    {
        if ($progress >= 100) {
            return 'completed';
        } elseif ($progress > 0) {
            return 'in_progress';
        } else {
            return 'not_started';
        }
    }

    private function getColorFromProgress($progress)
    {
        if ($progress >= 100) {
            return 'from-green-500 to-emerald-600';
        } elseif ($progress > 0) {
            return 'from-blue-500 to-indigo-600';
        } else {
            return 'from-gray-500 to-gray-600';
        }
    }
}
