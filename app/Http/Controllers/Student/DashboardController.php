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
                    'course_id' => $enrollment->course->id,
                    'hasActiveMeeting' => $this->hasActiveMeeting($enrollment->course->id),
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

    /**
     * فحص وجود اجتماع نشط للكورس
     */
    private function hasActiveMeeting($courseId)
    {
        // تنظيف الاجتماعات القديمة أولاً
        ZoomMeeting::cleanupOldMeetings();
        
        // فحص الاجتماعات النشطة والصالحة للكورس
        $hasActive = ZoomMeeting::where('course_id', $courseId)
            ->activeAndValid()
            ->exists();
            
        // إضافة log للتتبع
        \Log::info("Checking active meeting for course {$courseId}: " . ($hasActive ? 'YES' : 'NO'));
        
        return $hasActive;
    }

    /**
     * جلب حالة الاجتماع النشط للكورس
     */
    public function getActiveMeetingStatus($courseId)
    {
        \Log::info("Getting active meeting status for course: {$courseId}");
        
        // تنظيف الاجتماعات القديمة أولاً
        ZoomMeeting::cleanupOldMeetings();
        
        // فحص وجود اجتماعات نشطة للكورس
        $activeMeetings = ZoomMeeting::where('course_id', $courseId)
            ->activeAndValid()
            ->get();
            
        \Log::info("Found {$activeMeetings->count()} valid active meetings for course {$courseId}");
        
        $hasActiveMeeting = $activeMeetings->isNotEmpty();
        
        return response()->json([
            'success' => true,
            'hasActiveMeeting' => $hasActiveMeeting,
            'debug' => [
                'course_id' => $courseId,
                'meetings_count' => $activeMeetings->count(),
                'meetings' => $activeMeetings->map(function($meeting) {
                    return [
                        'id' => $meeting->id,
                        'topic' => $meeting->topic,
                        'created_at' => $meeting->created_at->format('Y-m-d H:i:s'),
                        'start_time' => $meeting->start_time->format('Y-m-d H:i:s')
                    ];
                })->toArray()
            ]
        ]);
    }

    /**
     * جلب الاجتماع النشط للكورس
     */
    public function getActiveMeetingForCourse($courseId)
    {
        $user = Auth::user();
        \Log::info("Student {$user->id} requesting active meeting for course {$courseId}");
        
        // التأكد من أن الطالب مسجل في هذا الكورس
        $isEnrolled = CourseEnrollment::where('student_id', $user->id)
            ->where('course_id', $courseId)
            ->exists();
            
        if (!$isEnrolled) {
            \Log::warning("Student {$user->id} is not enrolled in course {$courseId}");
            return response()->json([
                'success' => false,
                'message' => 'غير مسجل في هذا الكورس'
            ]);
        }
        
        // تنظيف الاجتماعات القديمة أولاً
        ZoomMeeting::cleanupOldMeetings();
        
        $meeting = ZoomMeeting::with(['course'])
            ->where('course_id', $courseId)
            ->activeAndValid()
            ->first();
            
        if ($meeting) {
            \Log::info("Found active meeting for course {$courseId}: Meeting ID {$meeting->id}, Zoom Meeting ID: {$meeting->zoom_meeting_id}");
            
            return response()->json([
                'success' => true,
                'meeting' => [
                    'id' => $meeting->id,
                    'zoom_meeting_id' => $meeting->zoom_meeting_id,
                    'topic' => $meeting->topic,
                    'course_title' => $meeting->course->title_ar ?? $meeting->course->title,
                    'start_time' => $meeting->start_time->format('Y-m-d H:i'),
                    'duration' => $meeting->duration,
                    'password' => $meeting->password,
                    'status' => $meeting->status,
                    'join_url' => $meeting->join_url
                ]
            ]);
        } else {
            \Log::info("No active meeting found for course {$courseId}");
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد اجتماع نشط لهذا الكورس'
            ]);
        }
    }
}
