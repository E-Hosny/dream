<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollmentRequest;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        // حساب الإحصائيات مع تطبيق نفس الفلاتر إذا كانت موجودة
        $baseQuery = CourseEnrollment::with(['course', 'student'])
            ->when($request->course_id, function ($query, $courseId) {
                $query->where('course_id', $courseId);
            })
            ->when($request->student_id, function ($query, $studentId) {
                $query->where('student_id', $studentId);
            })
            ->when($request->search, function ($query, $search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('course', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('title_ar', 'like', "%{$search}%");
                });
            });

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'enrolled' => (clone $baseQuery)->where('status', 'enrolled')->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
            'dropped' => (clone $baseQuery)->where('status', 'dropped')->count(),
        ];

        $enrollments = (clone $baseQuery)
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate(15)
            ->withQueryString();

        $courses = Course::published()->get(['id', 'title', 'title_ar']);
        $students = User::role('student')->get(['id', 'name', 'email']);

        return Inertia::render('Admin/Enrollments/Index', [
            'enrollments' => $enrollments,
            'courses' => $courses,
            'students' => $students,
            'stats' => $stats,
            'filters' => $request->only(['course_id', 'student_id', 'status', 'search'])
        ]);
    }

    public function create()
    {
        $courses = Course::published()->get(['id', 'title', 'title_ar']);
        $students = User::role('student')->get(['id', 'name', 'email']);

        return Inertia::render('Admin/Enrollments/Create', [
            'courses' => $courses,
            'students' => $students
        ]);
    }

    public function store(EnrollmentRequest $request)
    {
        $validated = $request->validated();

        // التحقق من عدم وجود تسجيل مسبق
        $existingEnrollment = CourseEnrollment::where('course_id', $validated['course_id'])
            ->where('student_id', $validated['student_id'])
            ->first();

        if ($existingEnrollment) {
            return back()->withErrors(['error' => 'الطالب مسجل بالفعل في هذا الكورس']);
        }

        // التحقق من عدد الطلاب المسموح
        $course = Course::find($validated['course_id']);
        if ($course->max_students && $course->enrollments()->where('status', 'enrolled')->count() >= $course->max_students) {
            return back()->withErrors(['error' => 'الكورس ممتلئ ولا يمكن إضافة المزيد من الطلاب']);
        }

        // تعيين تاريخ التسجيل إذا لم يتم تحديده
        if (!isset($validated['enrolled_at'])) {
            $validated['enrolled_at'] = now();
        }

        // تعيين التقدم إلى 0 إذا لم يتم تحديده
        if (!isset($validated['progress']) || $validated['progress'] === '') {
            $validated['progress'] = 0;
        }

        CourseEnrollment::create($validated);

        return redirect()->route('admin.enrollments.index')->with('success', 'تم تسجيل الطالب في الكورس بنجاح');
    }

    public function edit(CourseEnrollment $enrollment)
    {
        $enrollment->load(['course', 'student']);
        $courses = Course::published()->get(['id', 'title', 'title_ar']);
        $students = User::role('student')->get(['id', 'name', 'email']);

        return Inertia::render('Admin/Enrollments/Edit', [
            'enrollment' => $enrollment,
            'courses' => $courses,
            'students' => $students
        ]);
    }

    public function update(EnrollmentRequest $request, CourseEnrollment $enrollment)
    {
        $validated = $request->validated();

        // التحقق من عدم وجود تسجيل مسبق (إذا تم تغيير الكورس أو الطالب)
        if ($enrollment->course_id != $validated['course_id'] || $enrollment->student_id != $validated['student_id']) {
            $existingEnrollment = CourseEnrollment::where('course_id', $validated['course_id'])
                ->where('student_id', $validated['student_id'])
                ->where('id', '!=', $enrollment->id)
                ->first();

            if ($existingEnrollment) {
                return back()->withErrors(['error' => 'الطالب مسجل بالفعل في هذا الكورس']);
            }
        }

        // التحقق من عدد الطلاب المسموح إذا تم تغيير الكورس
        if ($enrollment->course_id != $validated['course_id']) {
            $course = Course::find($validated['course_id']);
            if ($course->max_students && $course->enrollments()->where('status', 'enrolled')->count() >= $course->max_students) {
                return back()->withErrors(['error' => 'الكورس ممتلئ ولا يمكن إضافة المزيد من الطلاب']);
            }
        }

        $enrollment->update($validated);

        return redirect()->route('admin.enrollments.index')->with('success', 'تم تحديث التسجيل بنجاح');
    }

    public function destroy(CourseEnrollment $enrollment)
    {
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')->with('success', 'تم حذف التسجيل بنجاح');
    }

    public function bulkEnroll(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['exists:users,id'],
            'status' => ['required', 'in:enrolled,completed,dropped'],
        ]);

        $course = Course::find($validated['course_id']);
        $enrolledCount = $course->enrollments()->where('status', 'enrolled')->count();
        $availableSpots = $course->max_students ? $course->max_students - $enrolledCount : null;

        if ($availableSpots !== null && count($validated['student_ids']) > $availableSpots) {
            return back()->withErrors(['error' => "لا يمكن إضافة هذا العدد من الطلاب. المساحات المتاحة: {$availableSpots}"]);
        }

        $enrollments = [];
        $now = now();

        foreach ($validated['student_ids'] as $studentId) {
            // التحقق من عدم وجود تسجيل مسبق
            $existingEnrollment = CourseEnrollment::where('course_id', $validated['course_id'])
                ->where('student_id', $studentId)
                ->first();

            if (!$existingEnrollment) {
                $enrollments[] = [
                    'course_id' => $validated['course_id'],
                    'student_id' => $studentId,
                    'status' => $validated['status'],
                    'enrolled_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($enrollments)) {
            CourseEnrollment::insert($enrollments);
            $count = count($enrollments);
            return redirect()->route('admin.enrollments.index')->with('success', "تم تسجيل {$count} طالب في الكورس بنجاح");
        }

        return redirect()->route('admin.enrollments.index')->with('info', 'جميع الطلاب مسجلون بالفعل في هذا الكورس');
    }
}
