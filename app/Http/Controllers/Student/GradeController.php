<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GradeController extends Controller
{
    /**
     * عرض جميع درجات الطالب من كل الكورسات
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // جلب جميع الكورسات المسجل فيها الطالب
        $enrolledCourseIds = $user->courseEnrollments()
            ->whereIn('status', ['active', 'enrolled', 'completed'])
            ->pluck('course_id');
        
        // بناء الاستعلام - فقط الواجبات المصححة
        $query = AssignmentSubmission::with([
            'assignment.meeting.course',
            'assignment.creator',
            'student'
        ])
        ->where('student_id', $user->id)
        ->whereNotNull('rating') // فقط المصححة
        ->whereHas('assignment.meeting.course', function ($query) use ($enrolledCourseIds) {
            $query->whereIn('id', $enrolledCourseIds);
        })
        ->orderBy('corrected_at', 'desc');
        
        // فلترة حسب الكورس
        if ($request->filled('course_id') && $request->course_id !== 'all') {
            $query->whereHas('assignment.meeting.course', function ($q) use ($request) {
                $q->where('id', $request->course_id);
            });
        }
        
        // فلترة حسب التقييم
        if ($request->filled('rating') && $request->rating !== 'all') {
            $query->where('rating', $request->rating);
        }
        
        $submissions = $query->paginate(12)->through(function ($submission) {
            return [
                'id' => $submission->id,
                'assignment' => [
                    'id' => $submission->assignment->id,
                    'title' => $submission->assignment->title,
                    'description' => $submission->assignment->description,
                ],
                'course' => [
                    'id' => $submission->assignment->meeting->course->id,
                    'title' => $submission->assignment->meeting->course->title,
                    'title_ar' => $submission->assignment->meeting->course->title_ar,
                ],
                'teacher' => [
                    'name' => $submission->assignment->creator->name,
                ],
                'rating' => $submission->rating,
                'teacher_notes' => $submission->teacher_notes,
                'submitted_at' => $submission->submitted_at?->format('Y-m-d h:i A'),
                'submitted_at_human' => $submission->submitted_at?->diffForHumans(),
                'corrected_at' => $submission->corrected_at?->format('Y-m-d h:i A'),
                'corrected_at_human' => $submission->corrected_at?->diffForHumans(),
                'submission_file_name' => $submission->submission_file_name,
                'correction_file_name' => $submission->correction_file_name,
                'status' => $submission->status,
            ];
        });
        
        // جلب قائمة الكورسات للفلترة
        $courses = Course::whereIn('id', $enrolledCourseIds)
            ->select('id', 'title', 'title_ar')
            ->get();
        
        // إحصائيات
        $allSubmissions = AssignmentSubmission::where('student_id', $user->id)
            ->whereNotNull('rating')
            ->get();
        
        $stats = [
            'total_graded' => $allSubmissions->count(),
            'average_rating' => $allSubmissions->avg('rating') ? round($allSubmissions->avg('rating'), 2) : 0,
            'five_stars' => $allSubmissions->where('rating', 5)->count(),
            'four_stars' => $allSubmissions->where('rating', 4)->count(),
            'three_stars' => $allSubmissions->where('rating', 3)->count(),
            'two_stars' => $allSubmissions->where('rating', 2)->count(),
            'one_star' => $allSubmissions->where('rating', 1)->count(),
        ];
        
        return Inertia::render('Student/Grades/Index', [
            'submissions' => $submissions,
            'courses' => $courses,
            'filters' => $request->only(['course_id', 'rating']),
            'stats' => $stats,
        ]);
    }
}
