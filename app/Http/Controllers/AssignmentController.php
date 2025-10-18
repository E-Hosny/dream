<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\ZoomMeeting;
use App\Models\User;
use App\Notifications\AssignmentCreated;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    /**
     * رفع واجب جديد للاجتماع
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meeting_id' => 'required|exists:zoom_meetings,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assignment_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $meeting = ZoomMeeting::findOrFail($request->meeting_id);
            
            // تحقق من أن المستخدم هو منشئ الاجتماع
            if ($meeting->created_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مسموح لك برفع واجب لهذا الاجتماع'
                ], 403);
            }

            // رفع الملف
            $file = $request->file('assignment_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('assignments', $fileName);

            // إنشاء الواجب
            $assignment = Assignment::create([
                'meeting_id' => $request->meeting_id,
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // إرسال إشعارات للطلاب المسجلين في الكورس
            $teacher = Auth::user();
            $course = $meeting->course;
            $enrolledStudents = $course->enrollments()
                ->whereIn('status', ['active', 'enrolled', 'completed'])
                ->with('student')
                ->get()
                ->pluck('student');

            // إرسال الإشعار لكل طالب مسجل (إشعار قاعدة بيانات + بريد إلكتروني)
            Notification::send($enrolledStudents, new AssignmentCreated($assignment, $teacher)); // للقاعدة
            Notification::send($enrolledStudents, new NewAssignmentNotification($assignment, $course)); // للبريد

            \Log::info("Assignment created notification sent to {$enrolledStudents->count()} students for assignment: {$assignment->title}");

            return response()->json([
                'success' => true,
                'message' => 'تم رفع الواجب بنجاح وإرسال إشعارات للطلاب',
                'assignment' => $assignment->load(['meeting', 'creator'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع الواجب: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث واجب موجود
     */
    public function update(Request $request, Assignment $assignment)
    {
        // تحقق من الصلاحية
        if ($assignment->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مسموح لك بتعديل هذا الواجب'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assignment_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'updated_by' => Auth::id(),
            ];

            // إذا تم رفع ملف جديد
            if ($request->hasFile('assignment_file')) {
                // حذف الملف القديم
                if ($assignment->file_path && Storage::exists($assignment->file_path)) {
                    Storage::delete($assignment->file_path);
                }

                // رفع الملف الجديد
                $file = $request->file('assignment_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('assignments', $fileName);

                $updateData = array_merge($updateData, [
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }

            $assignment->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الواجب بنجاح',
                'assignment' => $assignment->fresh()->load(['meeting', 'creator'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الواجب: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف واجب
     */
    public function destroy(Assignment $assignment)
    {
        // تحقق من الصلاحية
        if ($assignment->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مسموح لك بحذف هذا الواجب'
            ], 403);
        }

        try {
            // حذف الملف من التخزين
            if ($assignment->file_path && Storage::exists($assignment->file_path)) {
                Storage::delete($assignment->file_path);
            }

            // حذف الواجب من قاعدة البيانات
            $assignment->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الواجب بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الواجب: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحميل ملف الواجب
     */
    public function download(Assignment $assignment)
    {
        return $this->handleFileAccess($assignment, 'download');
    }

    /**
     * عرض ملف الواجب في المتصفح
     */
    public function view(Assignment $assignment)
    {
        return $this->handleFileAccess($assignment, 'view');
    }

    /**
     * دالة مساعدة للتعامل مع الملفات
     */
    private function handleFileAccess(Assignment $assignment, $action = 'download')
    {
        // تحقق من وجود الملف
        if (!$assignment->file_path || !Storage::exists($assignment->file_path)) {
            abort(404, 'الملف غير موجود');
        }

        // تحقق من صلاحية الوصول
        $user = Auth::user();
        $meeting = $assignment->meeting;
        $hasAccess = false;
        
        // الأدمن يمكنه الوصول لجميع الملفات
        if ($user->hasRole('admin')) {
            $hasAccess = true;
        }
        
        // المعلم يمكنه الوصول لأي واجب في كورساته
        if ($user->hasRole('teacher') && $meeting->created_by === $user->id) {
            $hasAccess = true;
        }
        
        // الطالب يمكنه الوصول لواجبات الكورسات المسجل فيها
        if ($user->hasRole('student')) {
            $isEnrolled = $user->courseEnrollments()
                ->where('course_id', $meeting->course_id)
                ->whereIn('status', ['active', 'enrolled', 'completed'])
                ->exists();
                
            if ($isEnrolled) {
                $hasAccess = true;
            }
        }

        if (!$hasAccess) {
            abort(403, 'غير مسموح لك بالوصول لهذا الملف');
        }

        // إرجاع الملف حسب نوع العملية المطلوبة
        if ($action === 'view') {
            $mimeType = Storage::mimeType($assignment->file_path);
            $fileContents = Storage::get($assignment->file_path);
            
            return response($fileContents)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . $assignment->file_name . '"');
        } else {
            return Storage::download($assignment->file_path, $assignment->file_name);
        }
    }

    /**
     * عرض حلول الطلاب للمعلم
     */
    public function showSubmissions(Assignment $assignment)
    {
        // تحقق من الصلاحية
        if ($assignment->created_by !== Auth::id()) {
            abort(403, 'غير مسموح لك بعرض حلول هذا الواجب');
        }

        $submissions = $assignment->submissions()
            ->with(['student'])
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'student' => [
                        'id' => $submission->student->id,
                        'name' => $submission->student->name,
                        'email' => $submission->student->email,
                    ],
                    'submission_file_name' => $submission->submission_file_name,
                    'submission_file_size' => $submission->formatted_submission_file_size,
                    'submitted_at' => $submission->submitted_at,
                    'correction_file_name' => $submission->correction_file_name,
                    'corrected_at' => $submission->corrected_at,
                    'rating' => $submission->rating,
                    'stars' => $submission->stars,
                    'teacher_notes' => $submission->teacher_notes,
                    'status' => $submission->status,
                    'submission_download_url' => $submission->submission_download_url,
                    'correction_download_url' => $submission->correction_download_url,
                ];
            });

        return Inertia::render('Teacher/Assignments/Submissions', [
            'assignment' => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'meeting' => [
                    'id' => $assignment->meeting->id,
                    'topic' => $assignment->meeting->topic,
                    'course' => [
                        'id' => $assignment->meeting->course->id,
                        'title' => $assignment->meeting->course->title,
                    ]
                ]
            ],
            'submissions' => $submissions,
            'stats' => [
                'total_students' => $assignment->meeting->course->enrollments()->whereIn('status', ['active', 'enrolled', 'completed'])->count(),
                'submitted_count' => $assignment->submissions_count,
                'corrected_count' => $assignment->corrected_submissions_count,
            ]
        ]);
    }
}