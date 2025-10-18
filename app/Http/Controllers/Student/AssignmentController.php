<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    /**
     * عرض جميع الواجبات للطالب من كل الكورسات
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // جلب جميع الكورسات المسجل فيها الطالب
        $enrolledCourseIds = $user->courseEnrollments()
            ->whereIn('status', ['active', 'enrolled', 'completed'])
            ->pluck('course_id');
        
        // بناء الاستعلام
        $query = Assignment::with([
            'meeting.course',
            'creator',
            'submissions' => function ($query) use ($user) {
                $query->where('student_id', $user->id);
            }
        ])
        ->whereHas('meeting.course', function ($query) use ($enrolledCourseIds) {
            $query->whereIn('id', $enrolledCourseIds);
        })
        ->orderBy('created_at', 'desc');
        
        // فلترة حسب الكورس
        if ($request->filled('course_id') && $request->course_id !== 'all') {
            $query->whereHas('meeting.course', function ($q) use ($request) {
                $q->where('id', $request->course_id);
            });
        }
        
        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'submitted') {
                $query->whereHas('submissions', function ($q) use ($user) {
                    $q->where('student_id', $user->id);
                });
            } elseif ($request->status === 'not_submitted') {
                $query->whereDoesntHave('submissions', function ($q) use ($user) {
                    $q->where('student_id', $user->id);
                });
            } elseif ($request->status === 'graded') {
                $query->whereHas('submissions', function ($q) use ($user) {
                    $q->where('student_id', $user->id)
                      ->whereNotNull('rating');
                });
            }
        }
        
        $assignments = $query->paginate(12)->through(function ($assignment) use ($user) {
            $submission = $assignment->submissions->first();
            
            return [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'file_name' => $assignment->file_name,
                'file_size' => $assignment->file_size,
                'created_at' => $assignment->created_at->format('Y-m-d h:i A'),
                'created_at_human' => $assignment->created_at->diffForHumans(),
                'course' => [
                    'id' => $assignment->meeting->course->id,
                    'title' => $assignment->meeting->course->title,
                    'title_ar' => $assignment->meeting->course->title_ar,
                ],
                'teacher' => [
                    'name' => $assignment->creator->name,
                ],
                'submission' => $submission ? [
                    'id' => $submission->id,
                    'submitted_at' => $submission->submitted_at?->format('Y-m-d h:i A'),
                    'submitted_at_human' => $submission->submitted_at?->diffForHumans(),
                    'rating' => $submission->rating,
                    'teacher_notes' => $submission->teacher_notes,
                    'corrected_at' => $submission->corrected_at?->format('Y-m-d h:i A'),
                    'status' => $submission->status,
                    'submission_file_name' => $submission->submission_file_name,
                    'correction_file_name' => $submission->correction_file_name,
                ] : null,
            ];
        });
        
        // جلب قائمة الكورسات للفلترة
        $courses = Course::whereIn('id', $enrolledCourseIds)
            ->select('id', 'title', 'title_ar')
            ->get();
        
        // إحصائيات
        $stats = [
            'total' => Assignment::whereHas('meeting.course', function ($query) use ($enrolledCourseIds) {
                $query->whereIn('id', $enrolledCourseIds);
            })->count(),
            'submitted' => AssignmentSubmission::where('student_id', $user->id)->count(),
            'graded' => AssignmentSubmission::where('student_id', $user->id)
                ->whereNotNull('rating')
                ->count(),
            'pending' => Assignment::whereHas('meeting.course', function ($query) use ($enrolledCourseIds) {
                $query->whereIn('id', $enrolledCourseIds);
            })
            ->whereDoesntHave('submissions', function ($q) use ($user) {
                $q->where('student_id', $user->id);
            })
            ->count(),
        ];
        
        return Inertia::render('Student/Assignments/Index', [
            'assignments' => $assignments,
            'courses' => $courses,
            'filters' => $request->only(['course_id', 'status']),
            'stats' => $stats,
        ]);
    }
}
