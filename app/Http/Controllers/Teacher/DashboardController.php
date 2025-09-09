<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Course;
use App\Models\ZoomMeeting;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // تنظيف الاجتماعات القديمة أولاً
        ZoomMeeting::cleanupOldMeetings();
        
        // جلب الكورسات التي يدرسها المعلم مع المواعيد والاجتماعات النشطة
        $courses = Course::with(['schedules', 'enrollments.student'])
            ->where('instructor_id', $user->id)
            ->get()
            ->map(function ($course) {
                $nextSchedule = $course->next_schedule;
                
                // البحث عن اجتماع نشط للكورس
                $activeMeeting = ZoomMeeting::where('course_id', $course->id)
                    ->activeAndValid()
                    ->first();
                
                return [
                    'id' => $course->id,
                    'title' => $course->title_ar,
                    'titleEn' => $course->title,
                    'description' => $course->description_ar,
                    'descriptionEn' => $course->description,
                    'status' => $course->status,
                    'level' => $course->level,
                    'price' => $course->price,
                    'duration_hours' => $course->duration_hours,
                    'start_date' => $course->start_date,
                    'end_date' => $course->end_date,
                    'requirements' => $course->requirements,
                    'learning_outcomes' => $course->learning_outcomes,
                    'enrollments_count' => $course->enrollments->count(),
                    'enrollments' => $course->enrollments->map(function ($enrollment) {
                        return [
                            'id' => $enrollment->id,
                            'student' => [
                                'name' => $enrollment->student->name,
                                'email' => $enrollment->student->email,
                            ],
                            'status' => $enrollment->status,
                            'progress' => $enrollment->progress,
                        ];
                    }),
                    'schedules' => $course->schedules->map(function ($schedule) {
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
                    'activeMeeting' => $activeMeeting ? [
                        'id' => $activeMeeting->id,
                        'topic' => $activeMeeting->topic,
                        'start_time' => $activeMeeting->start_time->format('Y-m-d H:i:s'),
                        'duration' => $activeMeeting->duration,
                        'status' => $activeMeeting->status,
                        'zoom_meeting_id' => $activeMeeting->zoom_meeting_id,
                    ] : null,
                    'hasActiveMeeting' => $activeMeeting !== null,
                    'created_at' => $course->created_at,
                ];
            });

        // حساب الإحصائيات
        $stats = [
            'activeCourses' => $courses->where('status', 'published')->count(),
            'totalStudents' => $courses->sum('enrollments_count'),
            'completedStudents' => $courses->sum(function($course) {
                return $course['enrollments']->where('status', 'completed')->count();
            }),
        ];

        return Inertia::render('Teacher/Dashboard', [
            'courses' => $courses,
            'stats' => $stats,
            'hasData' => $courses->count() > 0,
            'teacherName' => $user->name,
            'teacherEmail' => $user->email,
            'locale' => app()->getLocale(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'zoom_account_id' => $user->zoom_account_id,
            ],
        ]);
    }
    
    /**
     * إنهاء اجتماع نشط لكورس معين
     */
    public function endMeeting(Request $request, $courseId)
    {
        $user = Auth::user();
        
        try {
            // التحقق من أن المعلم يملك هذا الكورس
            $course = Course::where('id', $courseId)
                ->where('instructor_id', $user->id)
                ->first();
                
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بإنهاء اجتماع هذا الكورس'
                ], 403);
            }
            
            // البحث عن الاجتماع النشط للكورس
            $activeMeeting = ZoomMeeting::where('course_id', $courseId)
                ->activeAndValid()
                ->first();
                
            if (!$activeMeeting) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يوجد اجتماع نشط لهذا الكورس'
                ], 404);
            }
            
            // إنهاء الاجتماع
            $activeMeeting->update([
                'status' => 'ended',
                'updated_by' => $user->id,
                'updated_at' => now()
            ]);
            
            \Log::info("Meeting ended by teacher. Meeting ID: {$activeMeeting->id}, Course ID: {$courseId}, Teacher ID: {$user->id}");
            
            return response()->json([
                'success' => true,
                'message' => 'تم إنهاء الاجتماع بنجاح',
                'meeting' => [
                    'id' => $activeMeeting->id,
                    'topic' => $activeMeeting->topic,
                    'status' => 'ended'
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error ending meeting: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنهاء الاجتماع'
            ], 500);
        }
    }
}
