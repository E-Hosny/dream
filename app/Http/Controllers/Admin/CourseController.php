<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\CourseSchedule;
use App\Models\ZoomMeeting;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            'schedules' => ['nullable', 'array'],
            'schedules.*.day_of_week' => ['required', 'in:saturday,sunday,monday,tuesday,wednesday,thursday,friday'],
            'schedules.*.start_time' => ['required', 'date_format:H:i'],
            'schedules.*.end_time' => ['required', 'date_format:H:i', 'after:schedules.*.start_time'],
        ]);

        $schedules = $validated['schedules'] ?? [];
        unset($validated['schedules']);

        $course = Course::create($validated);

        // إنشاء مواعيد الكورس
        foreach ($schedules as $schedule) {
            CourseSchedule::create([
                'course_id' => $course->id,
                'day_of_week' => $schedule['day_of_week'],
                'start_time' => $schedule['start_time'],
                'end_time' => $schedule['end_time'],
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.courses.index')->with('success', 'تم إنشاء الكورس بنجاح');
    }

    public function show(Course $course)
    {
        $course->load(['instructor', 'enrollments.student', 'schedules']);
        
        return Inertia::render('Admin/Courses/Show', [
            'course' => $course
        ]);
    }

    public function edit(Course $course)
    {
        $course->load(['instructor', 'schedules']);
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
            'schedules' => ['nullable', 'array'],
            'schedules.*.day_of_week' => ['required', 'in:saturday,sunday,monday,tuesday,wednesday,thursday,friday'],
            'schedules.*.start_time' => ['required', 'date_format:H:i'],
            'schedules.*.end_time' => ['required', 'date_format:H:i', 'after:schedules.*.start_time'],
        ]);

        $schedules = $validated['schedules'] ?? [];
        unset($validated['schedules']);

        $course->update($validated);

        // تحديث مواعيد الكورس
        $course->schedules()->delete(); // حذف المواعيد القديمة
        
        foreach ($schedules as $schedule) {
            CourseSchedule::create([
                'course_id' => $course->id,
                'day_of_week' => $schedule['day_of_week'],
                'start_time' => $schedule['start_time'],
                'end_time' => $schedule['end_time'],
                'is_active' => true,
            ]);
        }

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

    public function showMeetings(Course $course)
    {
        // تنظيف الاجتماعات القديمة
        ZoomMeeting::cleanupOldMeetings();
        
        // جلب الاجتماعات المرتبطة بهذا الكورس مع الواجبات
        $meetings = ZoomMeeting::with('assignments')
            ->where('course_id', $course->id)
            ->orderBy('start_time', 'desc')
            ->get()
            ->map(function ($meeting) {
                $assignment = $meeting->assignments->first(); // واجب واحد فقط لكل اجتماع
                
                return [
                    'id' => $meeting->id,
                    'topic' => $meeting->topic,
                    'start_time' => $meeting->start_time ? $meeting->start_time->format('Y-m-d H:i:s') : null,
                    'end_time' => $meeting->end_time ? $meeting->end_time->format('Y-m-d H:i:s') : null,
                    'actual_start_time' => $meeting->actual_start_time ? $meeting->actual_start_time->format('Y-m-d H:i:s') : null,
                    'actual_end_time' => $meeting->actual_end_time ? $meeting->actual_end_time->format('Y-m-d H:i:s') : null,
                    'duration' => $meeting->duration,
                    'status' => $meeting->status,
                    'status_text' => $meeting->status_text,
                    'status_color' => $meeting->status_color,
                    'join_url' => $meeting->join_url,
                    'start_url' => $meeting->start_url,
                    'password' => $meeting->password,
                    'created_at' => $meeting->created_at->format('Y-m-d H:i:s'),
                    'zoom_meeting_id' => $meeting->zoom_meeting_id,
                    'assignment' => $assignment ? [
                        'id' => $assignment->id,
                        'title' => $assignment->title,
                        'description' => $assignment->description,
                        'file_name' => $assignment->file_name,
                        'file_type' => $assignment->file_type,
                        'file_size' => $assignment->file_size,
                        'formatted_file_size' => $assignment->formatted_file_size,
                        'created_at' => $assignment->created_at->format('Y-m-d H:i:s'),
                        'submissions_count' => $assignment->submissions_count,
                        'corrected_submissions_count' => $assignment->corrected_submissions_count,
                    ] : null,
                ];
            });
            
        // البحث عن اجتماع نشط
        $activeMeeting = ZoomMeeting::where('course_id', $course->id)
            ->activeAndValid()
            ->first();
            
        $courseData = [
            'id' => $course->id,
            'title' => $course->title_ar,
            'titleEn' => $course->title,
            'activeMeeting' => $activeMeeting ? [
                'id' => $activeMeeting->id,
                'topic' => $activeMeeting->topic,
                'start_time' => $activeMeeting->actual_start_time ? $activeMeeting->actual_start_time->format('Y-m-d H:i:s') : ($activeMeeting->start_time ? $activeMeeting->start_time->format('Y-m-d H:i:s') : null),
                'duration' => $activeMeeting->duration,
                'status' => $activeMeeting->status,
            ] : null,
            'hasActiveMeeting' => $activeMeeting !== null,
        ];
        
        return Inertia::render('Admin/Courses/Meetings', [
            'course' => $courseData,
            'meetings' => $meetings,
            'locale' => app()->getLocale(),
        ]);
    }

    public function deleteMeeting(Course $course, ZoomMeeting $meeting)
    {
        // التحقق من أن الاجتماع مرتبط بهذا الكورس
        if ($meeting->course_id !== $course->id) {
            return back()->withErrors(['error' => 'هذا الاجتماع غير مرتبط بهذا الكورس']);
        }

        try {
            DB::beginTransaction();

            // الحصول على ZoomService للمستخدم المالك للاجتماع
            $user = User::find($meeting->created_by);
            $zoomService = null;
            
            if ($user && $user->zoom_account_id) {
                $zoomAccount = \App\Models\ZoomAccount::find($user->zoom_account_id);
                if ($zoomAccount && $zoomAccount->is_active) {
                    $zoomService = new ZoomService($zoomAccount);
                }
            }
            
            // إذا لم يكن هناك حساب Zoom محدد، استخدم الحساب الافتراضي
            if (!$zoomService) {
                $zoomService = new ZoomService();
            }

            // محاولة حذف الاجتماع من Zoom (قد يفشل إذا كان الاجتماع غير موجود في Zoom)
            try {
                $zoomService->deleteMeeting($meeting->zoom_meeting_id);
            } catch (\Exception $e) {
                // إذا فشل الحذف من Zoom، نستمر في حذف السجل من قاعدة البيانات
                Log::warning('Failed to delete meeting from Zoom: ' . $e->getMessage());
            }

            // حذف الاجتماع من قاعدة البيانات
            $meeting->delete();

            DB::commit();

            // إرجاع JSON response عند الطلب من AJAX
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف الاجتماع بنجاح'
                ]);
            }

            return redirect()->route('admin.courses.meetings', $course->id)
                ->with('success', 'تم حذف الاجتماع بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Meeting Deletion Error: ' . $e->getMessage());

            // إرجاع JSON response عند الطلب من AJAX
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حذف الاجتماع: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء حذف الاجتماع: ' . $e->getMessage()
            ]);
        }
    }
}
