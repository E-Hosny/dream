<?php

namespace App\Models;

use App\Support\UploadedFileCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmissionFile extends Model
{
    public const KIND_SUBMISSION = 'submission';
    public const KIND_CORRECTION = 'correction';

    protected $fillable = [
        'submission_id',
        'kind',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'sort_order',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'sort_order' => 'integer',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AssignmentSubmission::class, 'submission_id');
    }

    public function getFormattedFileSizeAttribute(): string
    {
        return UploadedFileCollection::formatBytes($this->file_size);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('submissions.files.download', [$this->submission_id, $this->id]);
    }

    public function getViewUrlAttribute(): string
    {
        return route('submissions.files.view', [$this->submission_id, $this->id]);
    }

    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'kind' => $this->kind,
            'file_name' => $this->file_name,
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
            'formatted_file_size' => $this->formatted_file_size,
            'download_url' => $this->download_url,
            'view_url' => $this->view_url,
        ];
    }

    public static function storeUploaded(AssignmentSubmission $submission, UploadedFile $file, string $kind, int $sortOrder = 0): self
    {
        $folder = $kind === self::KIND_CORRECTION ? 'corrections' : 'submissions';
        $prefix = $kind === self::KIND_CORRECTION ? 'correction_' : '';
        $storedName = time() . '_' . $prefix . $submission->student_id . '_' . uniqid('', true) . '_' . $file->getClientOriginalName();
        $path = $file->storeAs($folder, $storedName, 'spaces');

        return $submission->files()->create([
            'kind' => $kind,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType() ?: $file->getMimeType(),
            'file_size' => $file->getSize(),
            'sort_order' => $sortOrder,
        ]);
    }

    public function deleteFromStorage(): void
    {
        if ($this->file_path && Storage::disk('spaces')->exists($this->file_path)) {
            Storage::disk('spaces')->delete($this->file_path);
        }
    }
}
