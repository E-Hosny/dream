<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\User;
use App\Notifications\AssignmentSubmitted;
use App\Notifications\AssignmentCorrected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class AssignmentSubmissionController extends Controller
{
    /**
     * رفع حل الواجب من قبل الطالب
     */
    public function store(Request $request, Assignment $assignment)
    {
        $validator = Validator::make($request->all(), [
            'submission_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            // تحقق من أن المستخدم طالب ومسجل في الكورس
            if (!$user->hasRole('student')) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذه العملية مخصصة للطلاب فقط'
                ], 403);
            }

            $isEnrolled = $user->courseEnrollments()
                ->where('course_id', $assignment->meeting->course_id)
                ->whereIn('status', ['active', 'enrolled', 'completed'])
                ->exists();

            if (!$isEnrolled) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مسموح لك برفع حل لهذا الواجب'
                ], 403);
            }

            // رفع الملف
            $file = $request->file('submission_file');
            $fileName = time() . '_' . $user->id . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('submissions', $fileName);

            // البحث عن submission موجود أو إنشاء جديد
            $submission = AssignmentSubmission::updateOrCreate(
                [
                    'assignment_id' => $assignment->id,
                    'student_id' => $user->id,
                ],
                [
                    'submission_file_path' => $filePath,
                    'submission_file_name' => $file->getClientOriginalName(),
                    'submission_file_type' => $file->getClientMimeType(),
                    'submission_file_size' => $file->getSize(),
                    'submitted_at' => now(),
                    // إذا تم رفع حل جديد، قم بحذف التصحيح السابق
                    'correction_file_path' => null,
                    'correction_file_name' => null,
                    'correction_file_type' => null,
                    'correction_file_size' => null,
                    'corrected_at' => null,
                    'rating' => null,
                    'teacher_notes' => null,
                ]
            );

            // إرسال إشعار للمعلم صاحب الواجب
            $teacher = User::find($assignment->created_by);
            if ($teacher) {
                $teacher->notify(new AssignmentSubmitted($submission, $user));
                \Log::info("Assignment submission notification sent to teacher {$teacher->name} for assignment: {$assignment->title}");
            }

            return response()->json([
                'success' => true,
                'message' => 'تم رفع الحل بنجاح وإرسال إشعار للمعلم',
                'submission' => [
                    'id' => $submission->id,
                    'submission_file_name' => $submission->submission_file_name,
                    'submission_file_size' => $submission->formatted_submission_file_size,
                    'submitted_at' => $submission->submitted_at,
                    'status' => $submission->status,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع الحل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف حل الطالب
     */
    public function destroy(AssignmentSubmission $submission)
    {
        // تحقق من الصلاحية
        if ($submission->student_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مسموح لك بحذف هذا الحل'
            ], 403);
        }

        try {
            // حذف ملف الحل
            if ($submission->submission_file_path && Storage::exists($submission->submission_file_path)) {
                Storage::delete($submission->submission_file_path);
            }

            // حذف ملف التصحيح (إن وجد)
            if ($submission->correction_file_path && Storage::exists($submission->correction_file_path)) {
                Storage::delete($submission->correction_file_path);
            }

            // حذف السجل من قاعدة البيانات
            $submission->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الحل بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الحل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * رفع ملف التصحيح من قبل المعلم
     */
    public function correct(Request $request, AssignmentSubmission $submission)
    {
        $validator = Validator::make($request->all(), [
            'correction_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'rating' => 'nullable|integer|min:1|max:5',
            'teacher_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // تحقق من أن المستخدم هو معلم الكورس
            $assignment = $submission->assignment;
            if ($assignment->created_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مسموح لك بتصحيح هذا الحل'
                ], 403);
            }

            $updateData = [
                'rating' => $request->rating,
                'teacher_notes' => $request->teacher_notes,
                'corrected_at' => now(),
            ];

            // إذا تم رفع ملف تصحيح
            if ($request->hasFile('correction_file')) {
                // حذف ملف التصحيح السابق
                if ($submission->correction_file_path && Storage::exists($submission->correction_file_path)) {
                    Storage::delete($submission->correction_file_path);
                }

                $file = $request->file('correction_file');
                $fileName = time() . '_correction_' . $submission->student_id . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('corrections', $fileName);

                $updateData = array_merge($updateData, [
                    'correction_file_path' => $filePath,
                    'correction_file_name' => $file->getClientOriginalName(),
                    'correction_file_type' => $file->getClientMimeType(),
                    'correction_file_size' => $file->getSize(),
                ]);
            }

            $submission->update($updateData);

            // إرسال إشعار للطالب بالتصحيح
            $student = User::find($submission->student_id);
            $teacher = Auth::user();
            
            if ($student) {
                $student->notify(new AssignmentCorrected($submission, $teacher));
                \Log::info("Assignment correction notification sent to student {$student->name} for assignment: {$assignment->title}");
            }

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ التصحيح بنجاح وإرسال إشعار للطالب',
                'submission' => [
                    'id' => $submission->id,
                    'rating' => $submission->rating,
                    'stars' => $submission->stars,
                    'teacher_notes' => $submission->teacher_notes,
                    'correction_file_name' => $submission->correction_file_name,
                    'corrected_at' => $submission->corrected_at,
                    'status' => $submission->status,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ التصحيح: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحميل ملفات الحلول والتصحيحات
     */
    public function download(Request $request, $type, AssignmentSubmission $submission)
    {
        return $this->handleSubmissionFileAccess($request, $type, $submission, 'download');
    }

    /**
     * عرض ملفات الحلول والتصحيحات في المتصفح
     */
    public function view(Request $request, $type, AssignmentSubmission $submission)
    {
        return $this->handleSubmissionFileAccess($request, $type, $submission, 'view');
    }

    /**
     * دالة مساعدة للتعامل مع ملفات الحلول والتصحيحات
     */
    private function handleSubmissionFileAccess(Request $request, $type, AssignmentSubmission $submission, $action = 'download')
    {
        $user = Auth::user();
        $filePath = null;
        $fileName = null;
        $hasAccess = false;
        
        // الأدمن يمكنه الوصول لجميع الملفات
        if ($user->hasRole('admin')) {
            $hasAccess = true;
        }
        
        if ($type === 'submission') {
            // ملف الحل
            if (!$submission->submission_file_path || !Storage::exists($submission->submission_file_path)) {
                abort(404, 'ملف الحل غير موجود');
            }

            $filePath = $submission->submission_file_path;
            $fileName = $submission->submission_file_name;

            // المعلم يمكنه الوصول لأي حل في كورساته
            if ($user->hasRole('teacher') && $submission->assignment->created_by === $user->id) {
                $hasAccess = true;
            }
            
            // الطالب يمكنه الوصول لحله فقط
            if ($user->hasRole('student') && $submission->student_id === $user->id) {
                $hasAccess = true;
            }

        } elseif ($type === 'correction') {
            // ملف التصحيح
            if (!$submission->correction_file_path || !Storage::exists($submission->correction_file_path)) {
                abort(404, 'ملف التصحيح غير موجود');
            }

            $filePath = $submission->correction_file_path;
            $fileName = $submission->correction_file_name;

            // المعلم والطالب المعني يمكنهما الوصول لملف التصحيح
            if (($user->hasRole('teacher') && $submission->assignment->created_by === $user->id) ||
                ($user->hasRole('student') && $submission->student_id === $user->id)) {
                $hasAccess = true;
            }
        }

        if (!$hasAccess) {
            abort(403, 'غير مسموح لك بالوصول لهذا الملف');
        }

        // إرجاع الملف حسب نوع العملية المطلوبة
        if ($action === 'view') {
            $mimeType = Storage::mimeType($filePath);
            $fileContents = Storage::get($filePath);
            
            return response($fileContents)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
        } else {
            return Storage::download($filePath, $fileName);
        }
    }

    /**
     * الحصول على حل الطالب لواجب معين
     */
    public function show(Assignment $assignment)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('student')) {
            abort(403, 'هذه العملية مخصصة للطلاب فقط');
        }

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $user->id)
            ->first();

        if (!$submission) {
            return response()->json([
                'success' => true,
                'submission' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'submission' => [
                'id' => $submission->id,
                'submission_file_name' => $submission->submission_file_name,
                'submission_file_size' => $submission->formatted_submission_file_size,
                'submitted_at' => $submission->submitted_at,
                'correction_file_name' => $submission->correction_file_name,
                'correction_file_size' => $submission->formatted_correction_file_size,
                'corrected_at' => $submission->corrected_at,
                'rating' => $submission->rating,
                'stars' => $submission->stars,
                'teacher_notes' => $submission->teacher_notes,
                'status' => $submission->status,
                'submission_download_url' => $submission->submission_download_url,
                'correction_download_url' => $submission->correction_download_url,
            ]
        ]);
    }
}