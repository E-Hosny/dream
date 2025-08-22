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
                $query->where('status', 'enrolled');
            }])
            ->get();
        
        // حساب الإحصائيات
        $stats = [
            'activeCourses' => $courses->where('status', 'published')->count(),
            'totalStudents' => $courses->sum(function($course) {
                return $course->enrollments->count();
            }),
            'pendingAssignments' => 0, // سيتم إضافته لاحقاً
            'monthlyEarnings' => $courses->sum('price') * 0.7, // افتراض أن المعلم يحصل على 70%
        ];
        
        // جلب النشاط الحديث للطلاب
        $recentActivity = $this->getRecentStudentActivity($teacher->id);
        
        // جلب أفضل الكورسات أداءً
        $topCourses = $this->getTopPerformingCourses($teacher->id);
        
        // جلب الفصول القادمة
        $upcomingClasses = $this->getUpcomingClasses($teacher->id);
        
        // رسائل للمعلم عندما لا توجد بيانات
        $messages = [
            'noCourses' => 'لا توجد كورسات منشورة لك بعد. قم بإنشاء كورس جديد من لوحة الإدارة.',
            'noStudents' => 'لا يوجد طلاب مسجلين في كورساتك بعد.',
            'noActivity' => 'لا يوجد نشاط حديث للطلاب.',
            'noClasses' => 'لا توجد فصول مجدولة قادمة.',
            'instructions' => 'لإضافة كورس جديد: 1) اذهب إلى لوحة الإدارة 2) أضف كورس جديد 3) اختر نفسك كمعلم 4) اضبط الحالة على "منشور"'
        ];
        
        return Inertia::render('Teacher/Dashboard', [
            'stats' => $stats,
            'courses' => $courses,
            'recentActivity' => $recentActivity,
            'topCourses' => $topCourses,
            'upcomingClasses' => $upcomingClasses,
            'messages' => $messages,
            'hasData' => $courses->count() > 0,
        ]);
    }
    
    private function getRecentStudentActivity($teacherId)
    {
        // جلب النشاط الحديث من تسجيلات الكورسات
        return CourseEnrollment::whereHas('course', function($query) use ($teacherId) {
                $query->where('instructor_id', $teacherId);
            })
            ->with(['student', 'course'])
            ->latest('enrolled_at')
            ->take(5)
            ->get()
            ->map(function($enrollment) {
                return [
                    'type' => 'enrollment',
                    'student' => $enrollment->student->name,
                    'action' => 'enrolled_in_course',
                    'course' => $enrollment->course->localized_title,
                    'time' => $enrollment->enrolled_at->diffForHumans(),
                    'avatar' => strtoupper(substr($enrollment->student->name, 0, 2)),
                ];
            });
    }
    
    private function getTopPerformingCourses($teacherId)
    {
        return Course::where('instructor_id', $teacherId)
            ->with(['enrollments' => function($query) {
                $query->where('status', 'enrolled');
            }])
            ->get()
            ->map(function($course) {
                $enrolledCount = $course->enrollments->count();
                $completionRate = $enrolledCount > 0 ? 
                    ($course->enrollments->where('status', 'completed')->count() / $enrolledCount) * 100 : 0;
                
                return [
                    'title' => $course->localized_title,
                    'students' => $enrolledCount,
                    'completion' => round($completionRate, 1),
                    'rating' => 0, // سيتم إضافته لاحقاً
                    'earnings' => '$' . number_format($course->price * 0.7, 0),
                ];
            })
            ->sortByDesc('students')
            ->take(3)
            ->values();
    }
    
    private function getUpcomingClasses($teacherId)
    {
        // افتراض أن الفصول تبدأ في الأسبوع القادم
        $nextWeek = now()->addWeek();
        
        return Course::where('instructor_id', $teacherId)
            ->where('status', 'published')
            ->where('start_date', '>=', $nextWeek)
            ->get()
            ->map(function($course, $index) use ($nextWeek) {
                return [
                    'time' => $nextWeek->copy()->addDays($index)->format('g:i A'),
                    'course' => $course->localized_title,
                    'students' => $course->enrollments()->where('status', 'enrolled')->count(),
                    'duration' => $course->duration_hours . 'h',
                ];
            })
            ->take(3);
    }
}
