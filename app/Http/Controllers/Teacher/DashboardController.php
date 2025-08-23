<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // جلب الكورسات التي يدرسها المعلم مع المواعيد
        $courses = Course::with(['schedules', 'enrollments.student'])
            ->where('instructor_id', $user->id)
            ->get()
            ->map(function ($course) {
                $nextSchedule = $course->next_schedule;
                
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
}
