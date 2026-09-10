<?php

namespace App\Models;

use App\Support\UploadedFileCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'submission_file_path',
        'submission_file_name',
        'submission_file_type',
        'submission_file_size',
        'submitted_at',
        'correction_file_path',
        'correction_file_name',
        'correction_file_type',
        'correction_file_size',
        'corrected_at',
        'rating',
        'teacher_notes',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'corrected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(AssignmentSubmissionFile::class, 'submission_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function submissionFiles(): HasMany
    {
        return $this->files()->where('kind', AssignmentSubmissionFile::KIND_SUBMISSION);
    }

    public function correctionFiles(): HasMany
    {
        return $this->files()->where('kind', AssignmentSubmissionFile::KIND_CORRECTION);
    }

    public function hasSubmissionFile(): bool
    {
        if ($this->relationLoaded('files')) {
            if ($this->files->where('kind', AssignmentSubmissionFile::KIND_SUBMISSION)->isNotEmpty()) {
                return true;
            }
        } elseif ($this->submissionFiles()->exists()) {
            return true;
        }

        return !empty($this->submission_file_path) && Storage::disk('spaces')->exists($this->submission_file_path);
    }

    public function hasCorrectionFile(): bool
    {
        if ($this->relationLoaded('files')) {
            if ($this->files->where('kind', AssignmentSubmissionFile::KIND_CORRECTION)->isNotEmpty()) {
                return true;
            }
        } elseif ($this->correctionFiles()->exists()) {
            return true;
        }

        return !empty($this->correction_file_path) && Storage::disk('spaces')->exists($this->correction_file_path);
    }

    public function getSubmissionDownloadUrlAttribute()
    {
        return $this->hasSubmissionFile()
            ? route('submissions.download', ['type' => 'submission', 'submission' => $this->id])
            : null;
    }

    public function getCorrectionDownloadUrlAttribute()
    {
        return $this->hasCorrectionFile()
            ? route('submissions.download', ['type' => 'correction', 'submission' => $this->id])
            : null;
    }

    public function getFormattedSubmissionFileSizeAttribute()
    {
        return UploadedFileCollection::formatBytes($this->submission_file_size);
    }

    public function getFormattedCorrectionFileSizeAttribute()
    {
        return UploadedFileCollection::formatBytes($this->correction_file_size);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function filesPayload(string $kind): array
    {
        $files = $this->relationLoaded('files')
            ? $this->files->where('kind', $kind)->values()
            : $this->files()->where('kind', $kind)->get();

        if ($files->isNotEmpty()) {
            return $files->map(fn (AssignmentSubmissionFile $file) => $file->toFrontendArray())->values()->all();
        }

        if ($kind === AssignmentSubmissionFile::KIND_SUBMISSION && !empty($this->submission_file_path)) {
            return [[
                'id' => null,
                'kind' => $kind,
                'file_name' => $this->submission_file_name,
                'file_type' => $this->submission_file_type,
                'file_size' => $this->submission_file_size,
                'formatted_file_size' => $this->formatted_submission_file_size,
                'download_url' => route('submissions.download', ['type' => 'submission', 'submission' => $this->id]),
                'view_url' => route('submissions.view', ['type' => 'submission', 'submission' => $this->id]),
            ]];
        }

        if ($kind === AssignmentSubmissionFile::KIND_CORRECTION && !empty($this->correction_file_path)) {
            return [[
                'id' => null,
                'kind' => $kind,
                'file_name' => $this->correction_file_name,
                'file_type' => $this->correction_file_type,
                'file_size' => $this->correction_file_size,
                'formatted_file_size' => $this->formatted_correction_file_size,
                'download_url' => route('submissions.download', ['type' => 'correction', 'submission' => $this->id]),
                'view_url' => route('submissions.view', ['type' => 'correction', 'submission' => $this->id]),
            ]];
        }

        return [];
    }

    public function syncLegacyColumnsFromChildren(): void
    {
        $firstSubmission = $this->files()
            ->where('kind', AssignmentSubmissionFile::KIND_SUBMISSION)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        $firstCorrection = $this->files()
            ->where('kind', AssignmentSubmissionFile::KIND_CORRECTION)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        $this->forceFill([
            'submission_file_path' => $firstSubmission?->file_path,
            'submission_file_name' => $firstSubmission?->file_name,
            'submission_file_type' => $firstSubmission?->file_type,
            'submission_file_size' => $firstSubmission?->file_size,
            'correction_file_path' => $firstCorrection?->file_path,
            'correction_file_name' => $firstCorrection?->file_name,
            'correction_file_type' => $firstCorrection?->file_type,
            'correction_file_size' => $firstCorrection?->file_size,
        ])->save();
    }

    public function deleteFilesByKind(string $kind): void
    {
        $files = $this->files()->where('kind', $kind)->get();
        foreach ($files as $file) {
            $file->deleteFromStorage();
            $file->delete();
        }
    }

    public function deleteAllStoredFiles(): void
    {
        foreach ($this->files as $file) {
            $file->deleteFromStorage();
        }

        foreach ([$this->submission_file_path, $this->correction_file_path] as $path) {
            if ($path && Storage::disk('spaces')->exists($path)) {
                $stillReferenced = $this->files()->where('file_path', $path)->exists();
                if (!$stillReferenced) {
                    Storage::disk('spaces')->delete($path);
                }
            }
        }
    }

    public function getStarsAttribute()
    {
        $stars = [];
        for ($i = 1; $i <= 5; $i++) {
            $stars[] = $i <= ($this->rating ?? 0);
        }
        return $stars;
    }

    public function getStatusAttribute()
    {
        if ($this->corrected_at) {
            return 'corrected';
        }
        if ($this->submitted_at) {
            return 'submitted';
        }

        return 'not_submitted';
    }
}
