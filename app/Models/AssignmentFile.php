<?php

namespace App\Models;

use App\Support\UploadedFileCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AssignmentFile extends Model
{
    protected $fillable = [
        'assignment_id',
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

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        return UploadedFileCollection::formatBytes($this->file_size);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('assignments.files.download', [$this->assignment_id, $this->id]);
    }

    public function getViewUrlAttribute(): string
    {
        return route('assignments.files.view', [$this->assignment_id, $this->id]);
    }

    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'file_name' => $this->file_name,
            'file_type' => $this->file_type,
            'file_size' => $this->file_size,
            'formatted_file_size' => $this->formatted_file_size,
            'download_url' => $this->download_url,
            'view_url' => $this->view_url,
        ];
    }

    public static function storeUploaded(Assignment $assignment, UploadedFile $file, int $sortOrder = 0): self
    {
        $storedName = time() . '_' . uniqid('', true) . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('assignments', $storedName, 'spaces');

        return $assignment->files()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType() ?: $file->getClientMimeType(),
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
