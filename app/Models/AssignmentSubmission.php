<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    // العلاقة مع الواجب
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    // العلاقة مع الطالب
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // تحقق من وجود ملف الحل
    public function hasSubmissionFile(): bool
    {
        return !empty($this->submission_file_path) && \Storage::disk('spaces')->exists($this->submission_file_path);
    }

    // تحقق من وجود ملف التصحيح
    public function hasCorrectionFile(): bool
    {
        return !empty($this->correction_file_path) && \Storage::disk('spaces')->exists($this->correction_file_path);
    }

    // الحصول على رابط تحميل ملف الحل
    public function getSubmissionDownloadUrlAttribute()
    {
        return $this->hasSubmissionFile() ? route('submissions.download', ['type' => 'submission', 'id' => $this->id]) : null;
    }

    // الحصول على رابط تحميل ملف التصحيح
    public function getCorrectionDownloadUrlAttribute()
    {
        return $this->hasCorrectionFile() ? route('submissions.download', ['type' => 'correction', 'id' => $this->id]) : null;
    }

    // تنسيق حجم ملف الحل
    public function getFormattedSubmissionFileSizeAttribute()
    {
        return $this->formatFileSize($this->submission_file_size);
    }

    // تنسيق حجم ملف التصحيح
    public function getFormattedCorrectionFileSizeAttribute()
    {
        return $this->formatFileSize($this->correction_file_size);
    }

    // دالة مساعدة لتنسيق حجم الملفات
    private function formatFileSize($bytes)
    {
        if (!$bytes) return '0 bytes';
        
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

    // الحصول على النجوم كـ array
    public function getStarsAttribute()
    {
        $stars = [];
        for ($i = 1; $i <= 5; $i++) {
            $stars[] = $i <= ($this->rating ?? 0);
        }
        return $stars;
    }

    // تحقق من الحالة
    public function getStatusAttribute()
    {
        if ($this->corrected_at) {
            return 'corrected'; // مصحح
        } elseif ($this->submitted_at) {
            return 'submitted'; // مرسل
        } else {
            return 'not_submitted'; // لم يرسل
        }
    }
}
