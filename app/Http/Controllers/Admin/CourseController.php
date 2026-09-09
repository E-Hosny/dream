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

        $statsMonth = $request->input('stats_month');

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'status', 'level', 'stats_month']),
            'sessionStats' => ZoomMeeting::monthlyPaymentStats($statsMonth),
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
            'status' => ['required', 'in:draft,published,archived,completed'],
            'instructor_id' => ['required', 'exists:users,id'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'requirements' => ['nullable', 'array'],
            'learning_outcomes' => ['nullable', 'array'],
            'student_message' => ['nullable', 'string'],
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
            'status' => ['required', 'in:draft,published,archived,completed'],
            'instructor_id' => ['required', 'exists:users,id'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'requirements' => ['nullable', 'array'],
            'learning_outcomes' => ['nullable', 'array'],
            'student_message' => ['nullable', 'string'],
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

    public function showMeetings(Request $request, Course $course)
    {
        // تنظيف الاجتماعات القديمة
        ZoomMeeting::cleanupOldMeetings();

        // دمج جلسات نفس اليوم المكررة والإبقاء على الأطول
        ZoomMeeting::consolidateAllDaysForCourse($course->id);
        
        // جلب الاجتماعات المرتبطة بهذا الكورس مع الواجبات
        $sessionPrice = (float) ($course->price ?? 0);

        $meetings = ZoomMeeting::with('assignments')
            ->where('course_id', $course->id)
            ->orderBy('start_time', 'desc')
            ->get()
            ->map(function ($meeting) use ($sessionPrice) {
                $assignment = $meeting->assignments->first(); // واجب واحد فقط لكل اجتماع
                $price = $meeting->session_price !== null
                    ? (float) $meeting->session_price
                    : $sessionPrice;
                
                return [
                    'id' => $meeting->id,
                    'topic' => $meeting->topic,
                    'start_time' => $meeting->start_time ? $meeting->start_time->format('Y-m-d H:i:s') : null,
                    'end_time' => $meeting->end_time ? $meeting->end_time->format('Y-m-d H:i:s') : null,
                    'actual_start_time' => $meeting->actual_start_time ? $meeting->actual_start_time->format('Y-m-d H:i:s') : null,
                    'actual_end_time' => $meeting->actual_end_time ? $meeting->actual_end_time->format('Y-m-d H:i:s') : null,
                    'duration' => $meeting->duration,
                    'session_price' => $price,
                    'session_price_format' => number_format($price, 2) . ' ر.س',
                    'is_paid' => (bool) $meeting->is_paid,
                    'is_prepaid' => (bool) $meeting->is_prepaid,
                    'due_notice' => (bool) $meeting->due_notice,
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

        $sessionStats = ZoomMeeting::monthlyPaymentStats(
            $request->input('stats_month'),
            [$course->id]
        );
            
        $courseData = [
            'id' => $course->id,
            'title' => $course->title_ar,
            'titleEn' => $course->title,
            'session_price' => $sessionPrice,
            'session_price_format' => number_format($sessionPrice, 2) . ' ر.س',
            'prepaid' => $course->prepaidSummary(),
            'due_notice_summary' => ZoomMeeting::dueNoticeSummary($course->id, $sessionPrice),
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
            'sessionStats' => $sessionStats,
            'locale' => app()->getLocale(),
        ]);
    }

    public function toggleMeetingPayment(Course $course, ZoomMeeting $meeting)
    {
        if ($meeting->course_id !== $course->id) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الاجتماع غير مرتبط بهذا الكورس',
            ], 422);
        }

        if ($meeting->session_price === null) {
            $meeting->session_price = $course->price;
        }

        $wasPrepaid = (bool) $meeting->is_prepaid;
        $meeting->is_paid = !$meeting->is_paid;
        if ($meeting->is_paid) {
            $meeting->due_notice = false;
        } else {
            if ($wasPrepaid) {
                $course->restorePrepaidFromMeeting($meeting);
            }
            $meeting->is_prepaid = false;
        }
        $meeting->save();

        return response()->json([
            'success' => true,
            'message' => $meeting->is_paid ? 'تم تعليم الحصة كمدفوعة' : 'تم إلغاء تعليم الحصة كمدفوعة',
            'is_paid' => (bool) $meeting->is_paid,
            'is_prepaid' => (bool) $meeting->is_prepaid,
            'due_notice' => (bool) $meeting->due_notice,
            'session_price' => (float) ($meeting->session_price ?? $course->price ?? 0),
            'session_price_format' => number_format((float) ($meeting->session_price ?? $course->price ?? 0), 2) . ' ر.س',
            'due_notice_summary' => ZoomMeeting::dueNoticeSummary($course->id, (float) ($course->price ?? 0)),
            'prepaid' => $course->fresh()->prepaidSummary(),
        ]);
    }

    public function bulkUpdateMeetingPayment(Request $request, Course $course)
    {
        $validated = $request->validate([
            'meeting_ids' => ['required', 'array', 'min:1'],
            'meeting_ids.*' => ['integer'],
            'is_paid' => ['required', 'boolean'],
        ]);

        $meetings = ZoomMeeting::where('course_id', $course->id)
            ->whereIn('id', $validated['meeting_ids'])
            ->get();

        if ($meetings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على الحصص المحددة',
            ], 422);
        }

        $isPaid = (bool) $validated['is_paid'];
        $defaultPrice = (float) ($course->price ?? 0);

        foreach ($meetings as $meeting) {
            if ($meeting->session_price === null) {
                $meeting->session_price = $defaultPrice;
            }

            if (!$isPaid && $meeting->is_prepaid) {
                $course->restorePrepaidFromMeeting($meeting);
                $meeting->is_prepaid = false;
            }

            $meeting->is_paid = $isPaid;
            if ($isPaid) {
                $meeting->due_notice = false;
            } else {
                $meeting->is_prepaid = false;
            }
            $meeting->save();
        }

        $updated = $meetings->map(function (ZoomMeeting $meeting) use ($defaultPrice) {
            $price = (float) ($meeting->session_price ?? $defaultPrice);

            return [
                'id' => $meeting->id,
                'is_paid' => (bool) $meeting->is_paid,
                'is_prepaid' => (bool) $meeting->is_prepaid,
                'due_notice' => (bool) $meeting->due_notice,
                'session_price' => $price,
                'session_price_format' => number_format($price, 2) . ' ر.س',
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => $isPaid ? 'تم تعليم الحصص المحددة كمدفوعة' : 'تم تعليم الحصص المحددة كغير مدفوعة',
            'meetings' => $updated,
            'due_notice_summary' => ZoomMeeting::dueNoticeSummary($course->id, $defaultPrice),
            'prepaid' => $course->fresh()->prepaidSummary(),
        ]);
    }

    public function addPrepaidSessions(Request $request, Course $course)
    {
        $validated = $request->validate([
            'sessions' => ['required', 'integer', 'min:1', 'max:200'],
        ]);

        $result = $course->addPrepaidSessions((int) $validated['sessions']);
        $course->refresh();
        $defaultPrice = (float) ($course->price ?? 0);

        $updated = ZoomMeeting::where('course_id', $course->id)
            ->get(['id', 'is_paid', 'is_prepaid', 'due_notice', 'session_price'])
            ->map(function (ZoomMeeting $meeting) use ($defaultPrice) {
                $price = (float) ($meeting->session_price ?? $defaultPrice);

                return [
                    'id' => $meeting->id,
                    'is_paid' => (bool) $meeting->is_paid,
                    'is_prepaid' => (bool) $meeting->is_prepaid,
                    'due_notice' => (bool) $meeting->due_notice,
                    'session_price' => $price,
                    'session_price_format' => number_format($price, 2) . ' ر.س',
                ];
            })
            ->values();

        $parts = [];
        if ($result['applied'] > 0) {
            $parts[] = "تم تعليم {$result['applied']} حصة حالية كمدفوعة مقدماً";
        }
        if ($result['leftover_added'] > 0) {
            $parts[] = "أُضيفت {$result['leftover_added']} حصة لرصيد الحصص القادمة";
        }

        return response()->json([
            'success' => true,
            'message' => $parts ? implode('، ', $parts) : 'تم تحديث الدفع المقدم',
            'meetings' => $updated,
            'prepaid' => $course->prepaidSummary(),
            'due_notice_summary' => ZoomMeeting::dueNoticeSummary($course->id, $defaultPrice),
        ]);
    }

    public function clearPrepaidSessions(Course $course)
    {
        $course->update(['prepaid_sessions' => 0]);

        return response()->json([
            'success' => true,
            'message' => 'تم مسح رصيد الحصص المدفوعة مقدماً المتبقية',
            'prepaid' => $course->fresh()->prepaidSummary(),
        ]);
    }

    public function updateMeetingsDueNotice(Request $request, Course $course)
    {
        $validated = $request->validate([
            'meeting_ids' => ['nullable', 'array'],
            'meeting_ids.*' => ['integer'],
            'due_notice' => ['required', 'boolean'],
        ]);

        $dueNotice = (bool) $validated['due_notice'];
        $meetingIds = collect($validated['meeting_ids'] ?? []);
        $defaultPrice = (float) ($course->price ?? 0);

        if ($dueNotice) {
            if ($meetingIds->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'اختر حصصاً غير مدفوعة لإظهار إشعار المستحقات',
                ], 422);
            }

            // استبدال مجموعة الإشعار الحالية بالحصص المحددة فقط
            ZoomMeeting::where('course_id', $course->id)
                ->where('due_notice', true)
                ->update(['due_notice' => false]);

            ZoomMeeting::where('course_id', $course->id)
                ->whereIn('id', $meetingIds)
                ->where('is_paid', false)
                ->update(['due_notice' => true]);
        } elseif ($meetingIds->isNotEmpty()) {
            ZoomMeeting::where('course_id', $course->id)
                ->whereIn('id', $meetingIds)
                ->update(['due_notice' => false]);
        } else {
            ZoomMeeting::where('course_id', $course->id)
                ->where('due_notice', true)
                ->update(['due_notice' => false]);
        }

        $updated = ZoomMeeting::where('course_id', $course->id)
            ->get(['id', 'is_paid', 'due_notice', 'session_price'])
            ->map(function (ZoomMeeting $meeting) use ($defaultPrice) {
                $price = (float) ($meeting->session_price ?? $defaultPrice);

                return [
                    'id' => $meeting->id,
                    'is_paid' => (bool) $meeting->is_paid,
                    'is_prepaid' => (bool) $meeting->is_prepaid,
                    'due_notice' => (bool) $meeting->due_notice,
                    'session_price' => $price,
                    'session_price_format' => number_format($price, 2) . ' ر.س',
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'message' => $dueNotice
                ? 'تم تفعيل إشعار المستحقات للطالب'
                : 'تم إزالة إشعار المستحقات',
            'meetings' => $updated,
            'due_notice_summary' => ZoomMeeting::dueNoticeSummary($course->id, $defaultPrice),
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
            if ($meeting->is_prepaid) {
                $course->restorePrepaidFromMeeting($meeting);
            }
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
