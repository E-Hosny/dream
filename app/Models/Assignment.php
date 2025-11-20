<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // العلاقة مع الاجتماع
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(ZoomMeeting::class, 'meeting_id');
    }

    // العلاقة مع المنشئ
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // العلاقة مع المحدث
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // العلاقة مع حلول الطلاب
    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    // الحصول على عدد الحلول المرسلة
    public function getSubmissionsCountAttribute()
    {
        return $this->submissions()->whereNotNull('submitted_at')->count();
    }

    // الحصول على عدد الحلول المصححة
    public function getCorrectedSubmissionsCountAttribute()
    {
        return $this->submissions()->whereNotNull('corrected_at')->count();
    }

    // تحقق من وجود ملف
    public function hasFile(): bool
    {
        return !empty($this->file_path) && \Storage::disk('spaces')->exists($this->file_path);
    }

    // الحصول على رابط تحميل الملف
    public function getDownloadUrlAttribute()
    {
        return route('assignments.download', $this->id);
    }

    // تنسيق حجم الملف
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }
}
