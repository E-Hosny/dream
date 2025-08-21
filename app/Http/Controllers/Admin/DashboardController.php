<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalTeachers' => User::role('teacher')->count(),
            'totalStudents' => User::role('student')->count(),
            'totalCourses' => Course::count(),
            'publishedCourses' => Course::where('status', 'published')->count(),
            'totalEnrollments' => CourseEnrollment::count(),
            'activeEnrollments' => CourseEnrollment::where('status', 'enrolled')->count(),
            'completedCourses' => CourseEnrollment::where('status', 'completed')->count(),
        ];

        // آخر النشاطات
        $recentActivities = collect([
            // آخر المستخدمين المسجلين
            ...User::latest()->take(3)->get()->map(function ($user) {
                return [
                    'type' => 'user_registered',
                    'message' => "مستخدم جديد: {$user->name}",
                    'time' => $user->created_at->diffForHumans(),
                    'icon' => 'user-plus'
                ];
            }),
            
            // آخر الكورسات المنشورة
            ...Course::where('status', 'published')->latest()->take(2)->get()->map(function ($course) {
                return [
                    'type' => 'course_published',
                    'message' => "تم نشر كورس: {$course->title}",
                    'time' => $course->updated_at->diffForHumans(),
                    'icon' => 'book'
                ];
            }),
            
            // آخر التسجيلات
            ...CourseEnrollment::with(['student', 'course'])->latest()->take(2)->get()->map(function ($enrollment) {
                return [
                    'type' => 'enrollment',
                    'message' => "تسجيل جديد: {$enrollment->student->name} في {$enrollment->course->title}",
                    'time' => $enrollment->created_at->diffForHumans(),
                    'icon' => 'users'
                ];
            })
        ])->sortByDesc('time')->take(6)->values();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities
        ]);
    }
}
