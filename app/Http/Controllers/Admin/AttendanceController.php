<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeetingAttendance;
use App\Models\ZoomMeeting;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * عرض صفحة تقارير الحضور الرئيسية
     */
    public function index(Request $request)
    {
        // بناء الاستعلام الأساسي
        $query = MeetingAttendance::with(['meeting.course', 'user']);

        // فلترة حسب الكورس
        if ($request->filled('course_id')) {
            $query->whereHas('meeting', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        // فلترة حسب نوع المستخدم
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->where('action_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('action_time', '<=', $request->date_to . ' 23:59:59');
        }

        // فلترة حسب المستخدم
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // جلب البيانات وتجميعها
        $attendances = $query->orderBy('action_time', 'desc')->get();
        
        // تجميع البيانات حسب (meeting_id, user_id)
        $groupedAttendances = $attendances->groupBy(function($item) {
            return $item->meeting_id . '_' . $item->user_id;
        })->map(function($group) {
            $first = $group->first();
            $joinAction = $group->where('action_type', MeetingAttendance::ACTION_JOIN)
                ->merge($group->where('action_type', MeetingAttendance::ACTION_MEETING_START))
                ->sortBy('action_time')
                ->first();
                
            $leaveAction = $group->where('action_type', MeetingAttendance::ACTION_LEAVE)
                ->merge($group->where('action_type', MeetingAttendance::ACTION_MEETING_END))
                ->sortBy('action_time')
                ->last();

            return [
                'id' => $first->id,
                'user' => $first->user,
                'user_type' => $first->user_type,
                'meeting' => $first->meeting,
                'join_time' => $joinAction ? $joinAction->action_time : null,
                'leave_time' => $leaveAction ? $leaveAction->action_time : null,
                'duration_seconds' => $leaveAction ? $leaveAction->duration_seconds : null,
                'ip_address' => $joinAction ? $joinAction->ip_address : null,
                'is_active' => !$leaveAction, // لا يزال متصلاً إذا لم يغادر
                'actions_count' => $group->count(),
                'last_action' => $group->sortByDesc('action_time')->first()->action_time
            ];
        })->sortByDesc('last_action')->values();

        // تطبيق pagination يدوياً
        $perPage = 15;
        $currentPage = $request->input('page', 1);
        $total = $groupedAttendances->count();
        $items = $groupedAttendances->forPage($currentPage, $perPage);

        $paginatedResults = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // الحصول على الكورسات للفلاتر
        $courses = Course::select('id', 'title', 'title_ar')->get();
        
        // الحصول على المعلمين والطلاب للفلاتر
        $teachers = User::role('teacher')->select('id', 'name', 'email')->get();
        $students = User::role('student')->select('id', 'name', 'email')->get();

        return Inertia::render('Admin/Attendance/Index', [
            'attendances' => $paginatedResults,
            'filters' => $request->only(['course_id', 'user_type', 'date_from', 'date_to', 'user_id']),
            'courses' => $courses,
            'teachers' => $teachers,
            'students' => $students
        ]);
    }

    /**
     * عرض تقرير حضور اجتماع محدد
     */
    public function meetingReport($meetingId)
    {
        $meeting = ZoomMeeting::with(['course', 'creator'])->findOrFail($meetingId);
        
        $attendances = MeetingAttendance::with('user')
            ->where('meeting_id', $meetingId)
            ->orderBy('action_time')
            ->get();

        // تجميع البيانات حسب المستخدم
        $userSummary = [];
        foreach ($attendances as $attendance) {
            $userId = $attendance->user_id;
            
            if (!isset($userSummary[$userId])) {
                $userSummary[$userId] = [
                    'user' => $attendance->user,
                    'user_type' => $attendance->user_type,
                    'join_time' => null,
                    'leave_time' => null,
                    'duration_seconds' => 0,
                    'actions' => []
                ];
            }
            
            $userSummary[$userId]['actions'][] = $attendance;
            
            // تحديث أوقات الدخول والخروج
            if (in_array($attendance->action_type, [MeetingAttendance::ACTION_JOIN, MeetingAttendance::ACTION_MEETING_START])) {
                if (!$userSummary[$userId]['join_time'] || $attendance->action_time > $userSummary[$userId]['join_time']) {
                    $userSummary[$userId]['join_time'] = $attendance->action_time;
                }
            }
            
            if (in_array($attendance->action_type, [MeetingAttendance::ACTION_LEAVE, MeetingAttendance::ACTION_MEETING_END])) {
                if (!$userSummary[$userId]['leave_time'] || $attendance->action_time > $userSummary[$userId]['leave_time']) {
                    $userSummary[$userId]['leave_time'] = $attendance->action_time;
                    $userSummary[$userId]['duration_seconds'] = $attendance->duration_seconds ?: 0;
                }
            }
        }

        return Inertia::render('Admin/Attendance/MeetingReport', [
            'meeting' => $meeting,
            'attendances' => $attendances,
            'userSummary' => array_values($userSummary)
        ]);
    }

    /**
     * عرض تقرير حضور كورس محدد
     */
    public function courseReport($courseId, Request $request)
    {
        $course = Course::findOrFail($courseId);
        
        // الحصول على جميع اجتماعات الكورس
        $meetingsQuery = ZoomMeeting::where('course_id', $courseId)
            ->orderBy('start_time', 'desc');

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $meetingsQuery->where('start_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $meetingsQuery->where('start_time', '<=', $request->date_to . ' 23:59:59');
        }

        $meetings = $meetingsQuery->get();

        // إحصائيات الحضور لكل اجتماع
        $attendanceStats = [];
        foreach ($meetings as $meeting) {
            $stats = $this->getMeetingAttendanceStats($meeting->id);
            $attendanceStats[$meeting->id] = $stats;
        }

        return Inertia::render('Admin/Attendance/CourseReport', [
            'course' => $course,
            'meetings' => $meetings,
            'attendanceStats' => $attendanceStats,
            'filters' => $request->only(['date_from', 'date_to'])
        ]);
    }

    /**
     * عرض تقرير حضور مستخدم محدد
     */
    public function userReport($userId, Request $request)
    {
        $user = User::findOrFail($userId);
        
        $query = MeetingAttendance::with(['meeting.course'])
            ->where('user_id', $userId)
            ->orderBy('action_time', 'desc');

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->where('action_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('action_time', '<=', $request->date_to . ' 23:59:59');
        }

        // فلترة حسب الكورس
        if ($request->filled('course_id')) {
            $query->whereHas('meeting', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        $attendances = $query->paginate(20);

        // حساب إحصائيات المستخدم
        $totalMeetings = MeetingAttendance::where('user_id', $userId)
            ->whereIn('action_type', [MeetingAttendance::ACTION_JOIN, MeetingAttendance::ACTION_MEETING_START])
            ->distinct('meeting_id')
            ->count();

        $totalDuration = MeetingAttendance::where('user_id', $userId)
            ->whereNotNull('duration_seconds')
            ->sum('duration_seconds');

        $averageDuration = $totalMeetings > 0 ? $totalDuration / $totalMeetings : 0;

        return Inertia::render('Admin/Attendance/UserReport', [
            'user' => $user,
            'attendances' => $attendances,
            'stats' => [
                'total_meetings' => $totalMeetings,
                'total_duration' => $totalDuration,
                'average_duration' => $averageDuration
            ],
            'filters' => $request->only(['date_from', 'date_to', 'course_id'])
        ]);
    }

    /**
     * إحصائيات الحضور العامة
     */
    public function dashboard(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->endOfMonth()->toDateString());

        // إجمالي الاجتماعات
        $totalMeetings = ZoomMeeting::whereBetween('start_time', [$dateFrom, $dateTo . ' 23:59:59'])->count();

        // إجمالي الحضور
        $totalAttendances = MeetingAttendance::whereBetween('action_time', [$dateFrom, $dateTo . ' 23:59:59'])
            ->whereIn('action_type', [MeetingAttendance::ACTION_JOIN, MeetingAttendance::ACTION_MEETING_START])
            ->count();

        // حضور المعلمين
        $teacherAttendances = MeetingAttendance::whereBetween('action_time', [$dateFrom, $dateTo . ' 23:59:59'])
            ->where('user_type', MeetingAttendance::USER_TYPE_TEACHER)
            ->whereIn('action_type', [MeetingAttendance::ACTION_JOIN, MeetingAttendance::ACTION_MEETING_START])
            ->count();

        // حضور الطلاب
        $studentAttendances = MeetingAttendance::whereBetween('action_time', [$dateFrom, $dateTo . ' 23:59:59'])
            ->where('user_type', MeetingAttendance::USER_TYPE_STUDENT)
            ->whereIn('action_type', [MeetingAttendance::ACTION_JOIN, MeetingAttendance::ACTION_MEETING_START])
            ->count();

        // متوسط مدة الحضور
        $averageDuration = MeetingAttendance::whereBetween('action_time', [$dateFrom, $dateTo . ' 23:59:59'])
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds');

        // الكورسات الأكثر نشاطاً
        $topCourses = DB::table('meeting_attendance')
            ->join('zoom_meetings', 'meeting_attendance.meeting_id', '=', 'zoom_meetings.id')
            ->join('courses', 'zoom_meetings.course_id', '=', 'courses.id')
            ->whereBetween('meeting_attendance.action_time', [$dateFrom, $dateTo . ' 23:59:59'])
            ->whereIn('meeting_attendance.action_type', [MeetingAttendance::ACTION_JOIN, MeetingAttendance::ACTION_MEETING_START])
            ->groupBy('courses.id', 'courses.title', 'courses.title_ar')
            ->select('courses.id', 'courses.title', 'courses.title_ar', DB::raw('COUNT(*) as attendance_count'))
            ->orderBy('attendance_count', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Attendance/Dashboard', [
            'stats' => [
                'total_meetings' => $totalMeetings,
                'total_attendances' => $totalAttendances,
                'teacher_attendances' => $teacherAttendances,
                'student_attendances' => $studentAttendances,
                'average_duration' => $averageDuration
            ],
            'top_courses' => $topCourses,
            'filters' => $request->only(['date_from', 'date_to'])
        ]);
    }

    /**
     * تصدير تقارير الحضور
     */
    public function export(Request $request)
    {
        $query = MeetingAttendance::with(['meeting.course', 'user'])
            ->orderBy('action_time', 'desc');

        // تطبيق نفس الفلاتر
        if ($request->filled('course_id')) {
            $query->whereHas('meeting', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('date_from')) {
            $query->where('action_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('action_time', '<=', $request->date_to . ' 23:59:59');
        }

        $attendances = $query->get();

        // تحويل البيانات لتصديرها
        $exportData = $attendances->map(function ($attendance) {
            return [
                'اسم المستخدم' => $attendance->user->name,
                'البريد الإلكتروني' => $attendance->user->email,
                'نوع المستخدم' => $attendance->user_type_text,
                'الكورس' => $attendance->meeting->course->title_ar ?? $attendance->meeting->course->title,
                'موضوع الاجتماع' => $attendance->meeting->topic,
                'نوع الإجراء' => $attendance->action_type_text,
                'وقت الإجراء' => $attendance->action_time->format('Y-m-d H:i:s'),
                'المدة (دقائق)' => $attendance->duration_in_minutes,
                'عنوان IP' => $attendance->ip_address
            ];
        });

        // هنا يمكن إضافة منطق التصدير إلى Excel أو CSV
        return response()->json([
            'success' => true,
            'data' => $exportData
        ]);
    }

    /**
     * الحصول على إحصائيات حضور اجتماع محدد
     */
    private function getMeetingAttendanceStats($meetingId)
    {
        $totalJoins = MeetingAttendance::where('meeting_id', $meetingId)
            ->whereIn('action_type', [MeetingAttendance::ACTION_JOIN, MeetingAttendance::ACTION_MEETING_START])
            ->count();

        $teacherJoins = MeetingAttendance::where('meeting_id', $meetingId)
            ->where('user_type', MeetingAttendance::USER_TYPE_TEACHER)
            ->whereIn('action_type', [MeetingAttendance::ACTION_JOIN, MeetingAttendance::ACTION_MEETING_START])
            ->count();

        $studentJoins = MeetingAttendance::where('meeting_id', $meetingId)
            ->where('user_type', MeetingAttendance::USER_TYPE_STUDENT)
            ->whereIn('action_type', [MeetingAttendance::ACTION_JOIN, MeetingAttendance::ACTION_MEETING_START])
            ->count();

        $averageDuration = MeetingAttendance::where('meeting_id', $meetingId)
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds');

        return [
            'total_joins' => $totalJoins,
            'teacher_joins' => $teacherJoins,
            'student_joins' => $studentJoins,
            'average_duration' => $averageDuration
        ];
    }
}