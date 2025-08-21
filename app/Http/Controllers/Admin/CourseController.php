<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::with(['instructor', 'enrollments'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('title_ar', 'like', "%{$search}%")
                      ->orWhereHas('instructor', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->level, function ($query, $level) {
                $query->where('level', $level);
            })
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'status', 'level'])
        ]);
    }

    public function create()
    {
        $teachers = User::role('teacher')->get(['id', 'name']);
        
        return Inertia::render('Admin/Courses/Create', [
            'teachers' => $teachers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'description_ar' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_hours' => ['required', 'integer', 'min:1'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
            'status' => ['required', 'in:draft,published,archived'],
            'instructor_id' => ['required', 'exists:users,id'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'requirements' => ['nullable', 'array'],
            'learning_outcomes' => ['nullable', 'array'],
        ]);

        Course::create($validated);

        return redirect()->route('admin.courses.index')->with('success', 'تم إنشاء الكورس بنجاح');
    }

    public function show(Course $course)
    {
        $course->load(['instructor', 'enrollments.student']);
        
        return Inertia::render('Admin/Courses/Show', [
            'course' => $course
        ]);
    }

    public function edit(Course $course)
    {
        $course->load('instructor');
        $teachers = User::role('teacher')->get(['id', 'name']);
        
        return Inertia::render('Admin/Courses/Edit', [
            'course' => $course,
            'teachers' => $teachers
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'description_ar' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_hours' => ['required', 'integer', 'min:1'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
            'status' => ['required', 'in:draft,published,archived'],
            'instructor_id' => ['required', 'exists:users,id'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'requirements' => ['nullable', 'array'],
            'learning_outcomes' => ['nullable', 'array'],
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.index')->with('success', 'تم تحديث الكورس بنجاح');
    }

    public function destroy(Course $course)
    {
        // التحقق من وجود طلاب مسجلين
        if ($course->enrollments()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الكورس لوجود طلاب مسجلين فيه');
        }

        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'تم حذف الكورس بنجاح');
    }
}
