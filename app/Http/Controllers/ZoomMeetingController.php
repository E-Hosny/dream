<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\ZoomMeeting;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\ZoomAccount;
use App\Models\CourseEnrollment;

class ZoomMeetingController extends Controller
{
    protected $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    /**
     * عرض قائمة الاجتماعات
     */
    public function index(Request $request)
    {
        $query = ZoomMeeting::with(['course', 'courseSchedule', 'creator'])
            ->orderBy('start_time', 'desc');

        // فلترة حسب الدور
        if (Auth::user()->hasRole('teacher')) {
            $query->byInstructor(Auth::id());
        } elseif (Auth::user()->hasRole('student')) {
            $query->whereHas('course.enrollments', function ($q) {
                $q->where('student_id', Auth::id());
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الكورس
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $meetings = $query->paginate(15);

        return Inertia::render('ZoomMeetings/Index', [
            'meetings' => $meetings,
            'filters' => $request->only(['status', 'course_id']),
            'courses' => Course::select('id', 'title', 'title_ar')->get()
        ]);
    }

    /**
     * عرض صفحة إنشاء اجتماع جديد
     */
    public function create(Request $request)
    {
        $courses = Course::select('id', 'title', 'title_ar')->get();
        $schedules = collect();

        if ($request->filled('course_id')) {
            $schedules = CourseSchedule::where('course_id', $request->course_id)
                ->where('is_active', true)
                ->get();
        }

        return Inertia::render('ZoomMeetings/Create', [
            'courses' => $courses,
            'schedules' => $schedules,
            'selectedCourseId' => $request->course_id
        ]);
    }

    /**
     * حفظ اجتماع جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'course_schedule_id' => 'nullable|exists:course_schedules,id',
            'topic' => 'required|string|max:255',
            'start_time' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:480', // 15 دقيقة إلى 8 ساعات
            'timezone' => 'nullable|string',
            'password' => 'nullable|string|max:10'
        ]);

        try {
            DB::beginTransaction();

            // إنشاء الاجتماع في Zoom
            $zoomData = $this->zoomService->createMeeting([
                'topic' => $request->topic,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                'timezone' => $request->timezone ?? 'Asia/Riyadh',
                'password' => $request->password
            ]);

            // حفظ الاجتماع في قاعدة البيانات
            $meeting = ZoomMeeting::create([
                'course_id' => $request->course_id,
                'course_schedule_id' => $request->course_schedule_id,
                'zoom_meeting_id' => $zoomData['zoom_meeting_id'],
                'topic' => $request->topic,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                'join_url' => $zoomData['join_url'],
                'start_url' => $zoomData['start_url'],
                'password' => $zoomData['password'],
                'status' => 'scheduled',
                'host_email' => $zoomData['host_email'],
                'timezone' => $request->timezone ?? 'Asia/Riyadh',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('zoom-meetings.index')
                ->with('success', 'تم إنشاء الاجتماع بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Zoom Meeting Creation Error: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء إنشاء الاجتماع: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * عرض اجتماع معين
     */
    public function show(ZoomMeeting $meeting)
    {
        $meeting->load(['course', 'courseSchedule', 'creator', 'participants.user']);

        return Inertia::render('ZoomMeetings/Show', [
            'meeting' => $meeting
        ]);
    }

    /**
     * عرض صفحة تعديل اجتماع
     */
    public function edit(ZoomMeeting $meeting)
    {
        $courses = Course::select('id', 'title', 'title_ar')->get();
        $schedules = CourseSchedule::where('course_id', $meeting->course_id)
            ->where('is_active', true)
            ->get();

        return Inertia::render('ZoomMeetings/Edit', [
            'meeting' => $meeting,
            'courses' => $courses,
            'schedules' => $schedules
        ]);
    }

    /**
     * تحديث اجتماع
     */
    public function update(Request $request, ZoomMeeting $meeting)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'course_schedule_id' => 'nullable|exists:course_schedules,id',
            'topic' => 'required|string|max:255',
            'start_time' => 'required|date',
            'duration' => 'required|integer|min:15|max:480',
            'timezone' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // تحديث الاجتماع في Zoom
            $this->zoomService->updateMeeting($meeting->zoom_meeting_id, [
                'topic' => $request->topic,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                'timezone' => $request->timezone ?? 'Asia/Riyadh'
            ]);

            // تحديث الاجتماع في قاعدة البيانات
            $meeting->update([
                'course_id' => $request->course_id,
                'course_schedule_id' => $request->course_schedule_id,
                'topic' => $request->topic,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                'timezone' => $request->timezone ?? 'Asia/Riyadh',
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('zoom-meetings.index')
                ->with('success', 'تم تحديث الاجتماع بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Zoom Meeting Update Error: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء تحديث الاجتماع: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * حذف اجتماع
     */
    public function destroy(ZoomMeeting $meeting)
    {
        try {
            DB::beginTransaction();

            // حذف الاجتماع من Zoom
            $this->zoomService->deleteMeeting($meeting->zoom_meeting_id);

            // حذف الاجتماع من قاعدة البيانات
            $meeting->delete();

            DB::commit();

            return redirect()->route('zoom-meetings.index')
                ->with('success', 'تم حذف الاجتماع بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Zoom Meeting Deletion Error: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء حذف الاجتماع: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * بدء اجتماع
     */
    public function start(ZoomMeeting $meeting)
    {
        try {
            // التحقق من أن المستخدم هو المعلم المسؤول عن الكورس
            if (!Auth::user()->hasRole('teacher') || 
                $meeting->course->instructor_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك ببدء هذا الاجتماع'
                ], 403);
            }

            $meeting->update([
                'status' => 'started',
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم بدء الاجتماع بنجاح',
                'start_url' => $meeting->start_url
            ]);

        } catch (\Exception $e) {
            Log::error('Zoom Meeting Start Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء بدء الاجتماع'
            ], 500);
        }
    }

    /**
     * إنهاء اجتماع
     */
    public function end(ZoomMeeting $meeting)
    {
        try {
            $meeting->update([
                'status' => 'ended',
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنهاء الاجتماع بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Zoom Meeting End Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنهاء الاجتماع'
            ], 500);
        }
    }

    /**
     * الحصول على اجتماعات قادمة
     */
    public function upcoming()
    {
        $meetings = ZoomMeeting::with(['course', 'courseSchedule'])
            ->upcoming(7)
            ->get();

        return response()->json($meetings);
    }

    /**
     * توليد كلمة مرور عشوائية للاجتماع
     */
    private function generatePassword($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $password;
    }

    /**
     * بدء اجتماع فوري للمعلم
     */
    public function startInstantMeeting(Request $request)
    {
        try {
            // التحقق من أن المستخدم معلم
            if (!Auth::user()->hasRole('teacher')) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك ببدء اجتماعات Zoom'
                ], 403);
            }

            $teacher = Auth::user();
            
            // التحقق من وجود حساب Zoom مرتبط
            if (!$teacher->zoom_account_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يوجد حساب Zoom مرتبط'
                ], 400);
            }

            // التحقق من أن حساب Zoom مفعل
            $zoomAccount = ZoomAccount::find($teacher->zoom_account_id);
            if (!$zoomAccount || !$zoomAccount->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'حساب Zoom غير مفعل'
                ], 400);
            }

            // إنشاء بيانات الاجتماع
            $meetingData = [
                'topic' => $request->input('topic', 'اجتماع فوري'),
                'type' => 1, // Instant meeting
                'start_time' => now()->format('Y-m-d\TH:i:s'),
                'duration' => $request->input('duration', 30),
                'timezone' => 'Asia/Riyadh'
            ];

            Log::info('Creating instant meeting with data:', $meetingData);

            // إنشاء الاجتماع عبر Zoom Service
            $meeting = $this->zoomService->createMeeting($teacher->email, $meetingData);
            
            Log::info('Meeting created successfully:', $meeting);

            // إنهاء أي اجتماعات قديمة نشطة لنفس الكورس قبل بدء اجتماع جديد
            if ($request->input('course_id')) {
                ZoomMeeting::where('course_id', $request->input('course_id'))
                    ->where('status', 'started')
                    ->where('created_at', '<', now()->subHours(2))
                    ->update(['status' => 'ended', 'updated_at' => now()]);
            }

            // حفظ بيانات الاجتماع في قاعدة البيانات
            $zoomMeeting = ZoomMeeting::create([
                'course_id' => $request->input('course_id'), // إضافة course_id
                'course_schedule_id' => $request->input('course_schedule_id'), // إضافة course_schedule_id
                'zoom_meeting_id' => $meeting['zoom_meeting_id'],
                'zoom_account_id' => $teacher->zoom_account_id,
                'topic' => $meetingData['topic'],
                'start_time' => now(),
                'actual_start_time' => now(), // الوقت الفعلي لبداية الاجتماع
                'duration' => $meetingData['duration'],
                'join_url' => $meeting['join_url'],
                'start_url' => $meeting['start_url'],
                'password' => $meeting['password'],
                'status' => 'started', // تغيير من 'created' إلى 'started'
                'host_email' => $teacher->email, // إضافة host_email
                'created_by' => $teacher->id, // إضافة created_by
                'updated_by' => $teacher->id  // إضافة updated_by
            ]);

            Log::info('Zoom meeting saved to database:', $zoomMeeting->toArray());

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الاجتماع بنجاح',
                'data' => [
                    'meeting_id' => $zoomMeeting->id,
                    'start_url' => $meeting['start_url'],
                    'join_url' => $meeting['join_url'],
                    'password' => $meeting['password']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating instant meeting: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء بدء الاجتماع: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إنشاء رابط انضمام للضيف (الطالب)
     */
    public function generateGuestJoinUrl(ZoomMeeting $meeting)
    {
        try {
            Log::info('generateGuestJoinUrl called for meeting ID: ' . $meeting->id);
            Log::info('Meeting data: ' . json_encode([
                'id' => $meeting->id,
                'zoom_meeting_id' => $meeting->zoom_meeting_id,
                'course_id' => $meeting->course_id,
                'topic' => $meeting->topic,
                'password' => $meeting->password,
                'status' => $meeting->status,
                'join_url' => $meeting->join_url
            ]));
            
            // التحقق من حالة الاجتماع
            if ($meeting->status !== 'started') {
                Log::warning('Meeting is not started. Current status: ' . $meeting->status);
                return response()->json([
                    'success' => false,
                    'message' => 'الاجتماع ليس نشطاً حالياً'
                ], 400);
            }
            
            // التحقق من أن المستخدم طالب
            if (!Auth::user()->hasRole('student')) {
                Log::warning('User is not a student: ' . Auth::id());
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بالوصول لهذا الرابط'
                ], 403);
            }

            // التحقق من أن الطالب مسجل في الكورس
            $enrollment = CourseEnrollment::where('student_id', Auth::id())
                ->where('course_id', $meeting->course_id)
                ->first();

            if (!$enrollment) {
                Log::warning('Student not enrolled in course. Student ID: ' . Auth::id() . ', Course ID: ' . $meeting->course_id);
                return response()->json([
                    'success' => false,
                    'message' => 'أنت غير مسجل في هذا الكورس'
                ], 403);
            }

            Log::info('Student enrollment verified successfully');

            // إنشاء رابط انضمام للضيف
            Log::info('Calling ZoomService::generateGuestJoinUrl with meeting ID: ' . $meeting->zoom_meeting_id);
            
            $guestUrl = $this->zoomService->generateGuestJoinUrl(
                $meeting->zoom_meeting_id, // استخدام zoom_meeting_id بدلاً من id
                $meeting->password
            );

            Log::info('ZoomService response: ' . json_encode($guestUrl));

            if ($guestUrl['success']) {
                $response = [
                    'success' => true,
                    'guest_join_url' => $guestUrl['guest_join_url'],
                    'meeting_id' => $meeting->id,
                    'password' => $meeting->password
                ];
                
                Log::info('Successfully generated guest join URL: ' . json_encode($response));
                return response()->json($response);
            }

            Log::error('Failed to generate guest join URL: ' . $guestUrl['message']);
            return response()->json([
                'success' => false,
                'message' => $guestUrl['message']
            ], 400);

        } catch (\Exception $e) {
            Log::error('Guest Join URL Generation Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء رابط الانضمام: ' . $e->getMessage()
            ], 500);
        }
    }
}
