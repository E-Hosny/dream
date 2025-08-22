<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // جلب الكورسات المسجلة للطالب مع معلومات الكورس والمدرب
        $enrollments = CourseEnrollment::with(['course.instructor'])
            ->where('student_id', $user->id)
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->id,
                    'title' => $enrollment->course->title_ar,
                    'titleEn' => $enrollment->course->title,
                    'progress' => (float) $enrollment->progress,
                    'lastAccessed' => $enrollment->updated_at ? $enrollment->updated_at->diffForHumans() : 'لم يبدأ بعد',
                    'nextLesson' => 'الدرس التالي', // يمكن تحديثه لاحقاً
                    'nextLessonEn' => 'Next Lesson',
                    'instructor' => $enrollment->course->instructor->name,
                    'status' => $this->getStatusFromProgress($enrollment->progress),
                    'color' => $this->getColorFromProgress($enrollment->progress),
                    'enrolled_at' => $enrollment->enrolled_at,
                    'completed_at' => $enrollment->completed_at,
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
