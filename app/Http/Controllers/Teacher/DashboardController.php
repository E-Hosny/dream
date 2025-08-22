<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        
        // جلب الكورسات التي يدرسها المعلم
        $courses = $teacher->teachingCourses()
            ->with(['enrollments' => function($query) {
                $query->with('student'); // إضافة بيانات الطالب
            }])
            ->get();
        
        // حساب الإحصائيات - فقط من الكورسات الموجودة
        $stats = [
            'activeCourses' => $courses->where('status', 'published')->count(),
            'totalStudents' => $courses->sum(function($course) {
                return $course->enrollments->count();
            }),
            'enrolledStudents' => $courses->sum(function($course) {
                return $course->enrollments->where('status', 'enrolled')->count();
            }),
            'completedStudents' => $courses->sum(function($course) {
                return $course->enrollments->where('status', 'completed')->count();
            }),
        ];
        
        return Inertia::render('Teacher/Dashboard', [
            'stats' => $stats,
            'courses' => $courses,
            'hasData' => $courses->count() > 0,
            'teacherName' => $teacher->name,
            'teacherEmail' => $teacher->email,
        ]);
    }
}
