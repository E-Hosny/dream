<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ValidatesUploadedDocuments;
use App\Models\Assignment;
use App\Models\AssignmentFile;
use App\Models\AssignmentSubmissionFile;
use App\Models\ZoomMeeting;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    use ValidatesUploadedDocuments;

    private function checkPhpUploadSettings(): array
    {
        $uploadMax = ini_get('upload_max_filesize');
        $postMax = ini_get('post_max_size');
        $uploadMaxBytes = $this->convertToBytes($uploadMax);
        $postMaxBytes = $this->convertToBytes($postMax);
        $maxAllowed = min($uploadMaxBytes, $postMaxBytes);

        return [
            'upload_max_filesize' => $uploadMax,
            'post_max_size' => $postMax,
            'max_allowed_bytes' => $maxAllowed,
            'max_allowed_mb' => round($maxAllowed / (1024 * 1024), 2),
        ];
    }

    private function convertToBytes($val): int
    {
        $val = trim((string) $val);
        $last = strtolower($val[strlen($val) - 1] ?? '');
        $num = (int) $val;

        switch ($last) {
            case 'g':
                $num *= 1024;
            case 'm':
                $num *= 1024;
            case 'k':
                $num *= 1024;
        }

        return $num;
    }

    public function store(Request $request)
    {
        $phpSettings = $this->checkPhpUploadSettings();
        $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
        $files = $this->collectDocumentFiles($request, 'assignment_files', 'assignment_file');

        Log::info('Assignment upload attempt started', [
            'user_id' => Auth::id(),
            'meeting_id' => $request->meeting_id,
            'files_count' => count($files),
            'php_post_max' => $phpSettings['post_max_size'],
            'content_length_mb' => $contentLength > 0 ? round($contentLength / (1024 * 1024), 2) : 0,
        ]);

        if ($contentLength > 0 && $contentLength > $phpSettings['max_allowed_bytes']) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'assignment_files' => [
                        'حجم البيانات المرسلة يتجاوز الحد المسموح به في إعدادات السيرفر (' . $phpSettings['post_max_size'] . ').',
                    ],
                ],
            ], 422);
        }

        $fileError = $this->validateDocumentFiles($files, true);
        if ($fileError) {
            return response()->json([
                'success' => false,
                'errors' => ['assignment_files' => [$fileError]],
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'meeting_id' => 'required|exists:zoom_meetings,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $meeting = ZoomMeeting::findOrFail($request->meeting_id);

            if ($meeting->created_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مسموح لك برفع واجب لهذا الاجتماع',
                ], 403);
            }

            $first = $files[0];
            $assignment = Assignment::create([
                'meeting_id' => $request->meeting_id,
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => '',
                'file_name' => $first->getClientOriginalName(),
                'file_type' => $first->getMimeType() ?: $first->getClientMimeType(),
                'file_size' => $first->getSize(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            foreach ($files as $index => $file) {
                AssignmentFile::storeUploaded($assignment, $file, $index);
            }

            $assignment->syncPrimaryFileFromChildren();

            $course = $meeting->course;
            $enrolledStudents = $course->enrollments()
                ->whereIn('status', ['active', 'enrolled', 'completed'])
                ->with('student')
                ->get()
                ->pluck('student');

            Notification::send($enrolledStudents, new NewAssignmentNotification($assignment->fresh('files'), $course));

            return response()->json([
                'success' => true,
                'message' => 'تم رفع الواجب بنجاح وإرسال إشعارات للطلاب',
                'assignment' => $assignment->fresh()->load(['meeting', 'creator', 'files']),
            ]);
        } catch (\Exception $e) {
            Log::error('Assignment upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع الواجب: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Assignment $assignment)
    {
        if ($assignment->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مسموح لك بتعديل هذا الواجب',
            ], 403);
        }

        $files = $this->collectDocumentFiles($request, 'assignment_files', 'assignment_file');
        $removeIds = collect($request->input('remove_file_ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $fileError = $this->validateDocumentFiles($files, false);
        if ($fileError) {
            return response()->json([
                'success' => false,
                'errors' => ['assignment_files' => [$fileError]],
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
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
            $assignment->update([
                'title' => $request->title,
                'description' => $request->description,
                'updated_by' => Auth::id(),
            ]);

            if ($removeIds->isNotEmpty()) {
                $toRemove = $assignment->files()->whereIn('id', $removeIds)->get();
                foreach ($toRemove as $file) {
                    $file->deleteFromStorage();
                    $file->delete();
                }
            }

            $existingCount = $assignment->files()->count();
            foreach ($files as $index => $file) {
                AssignmentFile::storeUploaded($assignment, $file, $existingCount + $index);
            }

            if ($assignment->files()->count() === 0) {
                return response()->json([
                    'success' => false,
                    'errors' => ['assignment_files' => ['يجب الإبقاء على ملف واحد على الأقل للواجب']],
                ], 422);
            }

            $assignment->syncPrimaryFileFromChildren();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الواجب بنجاح',
                'assignment' => $assignment->fresh()->load(['meeting', 'creator', 'files']),
            ]);
        } catch (\Exception $e) {
            Log::error('Assignment update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الواجب: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Assignment $assignment)
    {
        if ($assignment->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مسموح لك بحذف هذا الواجب',
            ], 403);
        }

        try {
            $assignment->load('files');
            $assignment->deleteAllStoredFiles();
            $assignment->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الواجب بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الواجب: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download(Assignment $assignment)
    {
        return $this->handleFileAccess($assignment, 'download');
    }

    public function view(Assignment $assignment)
    {
        return $this->handleFileAccess($assignment, 'view');
    }

    public function downloadFile(Assignment $assignment, AssignmentFile $file)
    {
        return $this->handleChildFileAccess($assignment, $file, 'download');
    }

    public function viewFile(Assignment $assignment, AssignmentFile $file)
    {
        return $this->handleChildFileAccess($assignment, $file, 'view');
    }

    private function userCanAccessAssignment(Assignment $assignment): bool
    {
        $user = Auth::user();
        $meeting = $assignment->meeting;

        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teacher') && $meeting->created_by === $user->id) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $user->courseEnrollments()
                ->where('course_id', $meeting->course_id)
                ->whereIn('status', ['active', 'enrolled', 'completed'])
                ->exists();
        }

        return false;
    }

    private function handleFileAccess(Assignment $assignment, string $action = 'download')
    {
        if (!$this->userCanAccessAssignment($assignment)) {
            abort(403, 'غير مسموح لك بالوصول لهذا الملف');
        }

        $disk = Storage::disk('spaces');
        $file = $assignment->files()->orderBy('sort_order')->orderBy('id')->first();
        $path = $file?->file_path ?: $assignment->file_path;
        $name = $file?->file_name ?: $assignment->file_name;

        if (!$path || !$disk->exists($path)) {
            abort(404, 'الملف غير موجود');
        }

        if ($action === 'view') {
            return redirect($disk->url($path));
        }

        return $disk->download($path, $name);
    }

    private function handleChildFileAccess(Assignment $assignment, AssignmentFile $file, string $action = 'download')
    {
        if ($file->assignment_id !== $assignment->id) {
            abort(404, 'الملف غير موجود');
        }

        if (!$this->userCanAccessAssignment($assignment)) {
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

    public function showSubmissions(Assignment $assignment)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $assignment->created_by !== $user->id) {
            abort(403, 'غير مسموح لك بعرض حلول هذا الواجب');
        }

        $submissions = $assignment->submissions()
            ->with(['student', 'files'])
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->map(function ($submission) {
                $submissionFiles = $submission->filesPayload(AssignmentSubmissionFile::KIND_SUBMISSION);
                $correctionFiles = $submission->filesPayload(AssignmentSubmissionFile::KIND_CORRECTION);

                return [
                    'id' => $submission->id,
                    'student' => [
                        'id' => $submission->student->id,
                        'name' => $submission->student->name,
                        'email' => $submission->student->email,
                    ],
                    'submission_file_name' => $submission->submission_file_name,
                    'submission_file_size' => $submission->formatted_submission_file_size,
                    'submission_files' => $submissionFiles,
                    'submitted_at' => $submission->submitted_at,
                    'correction_file_name' => $submission->correction_file_name,
                    'correction_files' => $correctionFiles,
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
                'files' => $assignment->loadMissing('files')->filesPayload(),
                'meeting' => [
                    'id' => $assignment->meeting->id,
                    'topic' => $assignment->meeting->topic,
                    'course' => [
                        'id' => $assignment->meeting->course->id,
                        'title' => $assignment->meeting->course->title,
                    ],
                ],
            ],
            'submissions' => $submissions,
            'stats' => [
                'total_students' => $assignment->meeting->course->enrollments()->whereIn('status', ['active', 'enrolled', 'completed'])->count(),
                'submitted_count' => $assignment->submissions_count,
                'corrected_count' => $assignment->corrected_submissions_count,
            ],
        ]);
    }
}
