<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesUploadedDocuments;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionFile;
use App\Models\User;
use App\Notifications\AssignmentSubmitted;
use App\Notifications\AssignmentCorrected;
use App\Notifications\AssignmentGradedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AssignmentSubmissionController extends Controller
{
    use ValidatesUploadedDocuments;

    public function store(Request $request, Assignment $assignment)
    {
        $files = $this->collectDocumentFiles($request, 'submission_files', 'submission_file');
        $fileError = $this->validateDocumentFiles($files, true);

        if ($fileError) {
            return response()->json([
                'success' => false,
                'errors' => ['submission_files' => [$fileError]],
            ], 422);
        }

        try {
            $user = Auth::user();

            if (!$user->hasRole('student')) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذه العملية مخصصة للطلاب فقط',
                ], 403);
            }

            $isEnrolled = $user->courseEnrollments()
                ->where('course_id', $assignment->meeting->course_id)
                ->whereIn('status', ['active', 'enrolled', 'completed'])
                ->exists();

            if (!$isEnrolled) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مسموح لك برفع حل لهذا الواجب',
                ], 403);
            }

            $submission = AssignmentSubmission::firstOrNew([
                'assignment_id' => $assignment->id,
                'student_id' => $user->id,
            ]);

            $submission->fill([
                'submitted_at' => now(),
                'correction_file_path' => null,
                'correction_file_name' => null,
                'correction_file_type' => null,
                'correction_file_size' => null,
                'corrected_at' => null,
                'rating' => null,
                'teacher_notes' => null,
            ]);
            $submission->save();

            // استبدال ملفات الحل والتصحيح عند إعادة الرفع
            $submission->deleteFilesByKind(AssignmentSubmissionFile::KIND_SUBMISSION);
            $submission->deleteFilesByKind(AssignmentSubmissionFile::KIND_CORRECTION);

            foreach ($files as $index => $file) {
                AssignmentSubmissionFile::storeUploaded(
                    $submission,
                    $file,
                    AssignmentSubmissionFile::KIND_SUBMISSION,
                    $index
                );
            }

            $submission->syncLegacyColumnsFromChildren();
            $submission->load('files');

            $teacher = User::find($assignment->created_by);
            if ($teacher) {
                $teacher->notify(new AssignmentSubmitted($submission, $user));
                Log::info("Assignment submission notification sent to teacher {$teacher->name} for assignment: {$assignment->title}");
            }

            return response()->json([
                'success' => true,
                'message' => 'تم رفع الحل بنجاح وإرسال إشعار للمعلم',
                'submission' => [
                    'id' => $submission->id,
                    'submission_file_name' => $submission->submission_file_name,
                    'submission_file_size' => $submission->formatted_submission_file_size,
                    'submission_files' => $submission->filesPayload(AssignmentSubmissionFile::KIND_SUBMISSION),
                    'submitted_at' => $submission->submitted_at,
                    'status' => $submission->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع الحل: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(AssignmentSubmission $submission)
    {
        if ($submission->student_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مسموح لك بحذف هذا الحل',
            ], 403);
        }

        try {
            $submission->load('files');
            $submission->deleteAllStoredFiles();
            $submission->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الحل بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الحل: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function correct(Request $request, AssignmentSubmission $submission)
    {
        $files = $this->collectDocumentFiles($request, 'correction_files', 'correction_file');
        $fileError = $this->validateDocumentFiles($files, false);

        if ($fileError) {
            return response()->json([
                'success' => false,
                'errors' => ['correction_files' => [$fileError]],
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'nullable|integer|min:1|max:5',
            'teacher_notes' => 'nullable|string|max:1000',
            'remove_file_ids' => 'nullable|array',
            'remove_file_ids.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $assignment = $submission->assignment;
            if ($assignment->created_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مسموح لك بتصحيح هذا الحل',
                ], 403);
            }

            $removeIds = collect($request->input('remove_file_ids', []))
                ->filter(fn ($id) => is_numeric($id))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($removeIds->isNotEmpty()) {
                $toRemove = $submission->files()
                    ->where('kind', AssignmentSubmissionFile::KIND_CORRECTION)
                    ->whereIn('id', $removeIds)
                    ->get();

                foreach ($toRemove as $file) {
                    $file->deleteFromStorage();
                    $file->delete();
                }
            }

            if (!empty($files)) {
                $existingCount = $submission->files()
                    ->where('kind', AssignmentSubmissionFile::KIND_CORRECTION)
                    ->count();

                foreach ($files as $index => $file) {
                    AssignmentSubmissionFile::storeUploaded(
                        $submission,
                        $file,
                        AssignmentSubmissionFile::KIND_CORRECTION,
                        $existingCount + $index
                    );
                }
            }

            $submission->update([
                'rating' => $request->rating,
                'teacher_notes' => $request->teacher_notes,
                'corrected_at' => now(),
            ]);

            $submission->syncLegacyColumnsFromChildren();
            $submission->load('files');

            $student = User::find($submission->student_id);
            $teacher = Auth::user();
            $course = $assignment->meeting->course;

            if ($student) {
                $student->notify(new AssignmentCorrected($submission, $teacher));
                $student->notify(new AssignmentGradedNotification($submission, $assignment, $course));
                Log::info("Assignment correction notification sent to student {$student->name} for assignment: {$assignment->title}");
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
                    'correction_files' => $submission->filesPayload(AssignmentSubmissionFile::KIND_CORRECTION),
                    'corrected_at' => $submission->corrected_at,
                    'status' => $submission->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ التصحيح: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download(Request $request, $type, AssignmentSubmission $submission)
    {
        return $this->handleSubmissionFileAccess($request, $type, $submission, 'download');
    }

    public function view(Request $request, $type, AssignmentSubmission $submission)
    {
        return $this->handleSubmissionFileAccess($request, $type, $submission, 'view');
    }

    public function downloadFile(AssignmentSubmission $submission, AssignmentSubmissionFile $file)
    {
        return $this->handleChildFileAccess($submission, $file, 'download');
    }

    public function viewFile(AssignmentSubmission $submission, AssignmentSubmissionFile $file)
    {
        return $this->handleChildFileAccess($submission, $file, 'view');
    }

    private function userCanAccessSubmissionFile(AssignmentSubmission $submission, string $kind): bool
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($kind === AssignmentSubmissionFile::KIND_SUBMISSION) {
            if ($user->hasRole('teacher') && $submission->assignment->created_by === $user->id) {
                return true;
            }

            if ($user->hasRole('student') && $submission->student_id === $user->id) {
                return true;
            }
        }

        if ($kind === AssignmentSubmissionFile::KIND_CORRECTION) {
            if (($user->hasRole('teacher') && $submission->assignment->created_by === $user->id)
                || ($user->hasRole('student') && $submission->student_id === $user->id)) {
                return true;
            }
        }

        return false;
    }

    private function handleSubmissionFileAccess(Request $request, $type, AssignmentSubmission $submission, $action = 'download')
    {
        $disk = Storage::disk('spaces');
        $kind = $type === 'correction'
            ? AssignmentSubmissionFile::KIND_CORRECTION
            : AssignmentSubmissionFile::KIND_SUBMISSION;

        if (!$this->userCanAccessSubmissionFile($submission, $kind)) {
            abort(403, 'غير مسموح لك بالوصول لهذا الملف');
        }

        $file = $submission->files()->where('kind', $kind)->orderBy('sort_order')->orderBy('id')->first();

        if ($file) {
            $path = $file->file_path;
            $name = $file->file_name;
        } elseif ($kind === AssignmentSubmissionFile::KIND_SUBMISSION) {
            $path = $submission->submission_file_path;
            $name = $submission->submission_file_name;
        } else {
            $path = $submission->correction_file_path;
            $name = $submission->correction_file_name;
        }

        if (!$path || !$disk->exists($path)) {
            abort(404, $kind === AssignmentSubmissionFile::KIND_CORRECTION ? 'ملف التصحيح غير موجود' : 'ملف الحل غير موجود');
        }

        if ($action === 'view') {
            return redirect($disk->url($path));
        }

        return $disk->download($path, $name);
    }

    private function handleChildFileAccess(AssignmentSubmission $submission, AssignmentSubmissionFile $file, string $action = 'download')
    {
        if ($file->submission_id !== $submission->id) {
            abort(404, 'الملف غير موجود');
        }

        if (!$this->userCanAccessSubmissionFile($submission, $file->kind)) {
            abort(403, 'غير مسموح لك بالوصول لهذا الملف');
        }

        $disk = Storage::disk('spaces');
        if (!$file->file_path || !$disk->exists($file->file_path)) {
            abort(404, 'الملف غير موجود');
        }

        if ($action === 'view') {
            return redirect($disk->url($file->file_path));
        }

        return $disk->download($file->file_path, $file->file_name);
    }

    public function show(Assignment $assignment)
    {
        $user = Auth::user();

        if (!$user->hasRole('student')) {
            abort(403, 'هذه العملية مخصصة للطلاب فقط');
        }

        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $user->id)
            ->with('files')
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
                'submission_files' => $submission->filesPayload(AssignmentSubmissionFile::KIND_SUBMISSION),
                'submitted_at' => $submission->submitted_at,
                'correction_file_name' => $submission->correction_file_name,
                'correction_file_size' => $submission->formatted_correction_file_size,
                'correction_files' => $submission->filesPayload(AssignmentSubmissionFile::KIND_CORRECTION),
                'corrected_at' => $submission->corrected_at,
                'rating' => $submission->rating,
                'stars' => $submission->stars,
                'teacher_notes' => $submission->teacher_notes,
                'status' => $submission->status,
                'submission_download_url' => $submission->submission_download_url,
                'correction_download_url' => $submission->correction_download_url,
            ],
        ]);
    }
}
