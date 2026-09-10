<?php

namespace App\Models;

use App\Support\UploadedFileCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Assignment extends Model
{
    protected $fillable = [
        'meeting_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(ZoomMeeting::class, 'meeting_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(AssignmentFile::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getSubmissionsCountAttribute()
    {
        return $this->submissions()->whereNotNull('submitted_at')->count();
    }

    public function getCorrectedSubmissionsCountAttribute()
    {
        return $this->submissions()->whereNotNull('corrected_at')->count();
    }

    public function hasFile(): bool
    {
        if ($this->relationLoaded('files') ? $this->files->isNotEmpty() : $this->files()->exists()) {
            return true;
        }

        return !empty($this->file_path) && Storage::disk('spaces')->exists($this->file_path);
    }

    public function getDownloadUrlAttribute()
    {
        return route('assignments.download', $this->id);
    }

    public function getFormattedFileSizeAttribute()
    {
        return UploadedFileCollection::formatBytes($this->file_size);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function filesPayload(): array
    {
        $files = $this->relationLoaded('files')
            ? $this->files
            : $this->files()->get();

        if ($files->isNotEmpty()) {
            return $files->map(fn (AssignmentFile $file) => $file->toFrontendArray())->values()->all();
        }

        if (!empty($this->file_path)) {
            return [[
                'id' => null,
                'file_name' => $this->file_name,
                'file_type' => $this->file_type,
                'file_size' => $this->file_size,
                'formatted_file_size' => $this->formatted_file_size,
                'download_url' => route('assignments.download', $this->id),
                'view_url' => route('assignments.view', $this->id),
            ]];
        }

        return [];
    }

    public function syncPrimaryFileFromChildren(): void
    {
        $first = $this->files()->orderBy('sort_order')->orderBy('id')->first();

        if ($first) {
            $this->forceFill([
                'file_path' => $first->file_path,
                'file_name' => $first->file_name,
                'file_type' => $first->file_type,
                'file_size' => $first->file_size,
            ])->save();

            return;
        }

        $this->forceFill([
            'file_path' => '',
            'file_name' => '',
            'file_type' => '',
            'file_size' => 0,
        ])->save();
    }

    public function deleteAllStoredFiles(): void
    {
        foreach ($this->files as $file) {
            $file->deleteFromStorage();
        }

        if ($this->file_path && Storage::disk('spaces')->exists($this->file_path)) {
            $stillReferenced = $this->files()->where('file_path', $this->file_path)->exists();
            if (!$stillReferenced) {
                Storage::disk('spaces')->delete($this->file_path);
            }
        }
    }
}
