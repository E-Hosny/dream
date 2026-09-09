<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'description_ar',
        'image',
        'price',
        'prepaid_sessions',
        'duration_hours',
        'level',
        'status',
        'instructor_id',
        'max_students',
        'start_date',
        'end_date',
        'requirements',
        'learning_outcomes',
        'student_message',
    ];

    protected $casts = [
        'requirements' => 'array',
        'learning_outcomes' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'prepaid_sessions' => 'integer',
    ];

    // العلاقات
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_enrollments', 'course_id', 'student_id')
                    ->withPivot(['status', 'progress', 'enrolled_at', 'completed_at', 'final_grade'])
                    ->withTimestamps();
    }

    // الحصول على الطلاب المسجلين فقط (الحالة: enrolled أو active)
    public function enrolledStudents()
    {
        return $this->belongsToMany(User::class, 'course_enrollments', 'course_id', 'student_id')
                    ->select('users.*') // تحديد الأعمدة من جدول users فقط
                    ->wherePivotIn('status', ['enrolled', 'active', 'completed'])
                    ->withPivot(['status', 'progress', 'enrolled_at', 'completed_at', 'final_grade'])
                    ->withTimestamps();
    }

    // العلاقة مع مواعيد الكورس
    public function schedules(): HasMany
    {
        return $this->hasMany(CourseSchedule::class);
    }

    // العلاقة مع اجتماعات Zoom
    public function zoomMeetings(): HasMany
    {
        return $this->hasMany(ZoomMeeting::class);
    }

    // الحصول على الموعد التالي للكورس
    public function getNextScheduleAttribute()
    {
        $nextSchedule = null;
        $nextTime = null;
        
        foreach ($this->schedules as $schedule) {
            if ($schedule->is_active) {
                $nextOccurrence = $schedule->next_occurrence;
                if (!$nextTime || $nextOccurrence < $nextTime) {
                    $nextTime = $nextOccurrence;
                    $nextSchedule = $schedule;
                }
            }
        }
        
        return $nextSchedule;
    }

    // Accessors
    public function getLocalizedTitleAttribute()
    {
        return app()->getLocale() === 'ar' && $this->title_ar ? $this->title_ar : $this->title;
    }

    public function getLocalizedDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' && $this->description_ar ? $this->description_ar : $this->description;
    }

    public function getEnrolledStudentsCountAttribute()
    {
        return $this->enrollments()->where('status', 'enrolled')->count();
    }

    public function getAvailableSpotsAttribute()
    {
        if (!$this->max_students) return null;
        return $this->max_students - $this->enrolled_students_count;
    }

    /**
     * إضافة حصص مدفوعة مقدماً: تُطبَّق أولاً على الحصص الحالية غير المدفوعة، والباقي رصيد للحصص القادمة.
     *
     * @return array{applied:int, remaining:int, leftover_added:int}
     */
    public function addPrepaidSessions(int $count): array
    {
        $count = max(0, $count);

        if ($count === 0) {
            return [
                'applied' => 0,
                'remaining' => (int) $this->prepaid_sessions,
                'leftover_added' => 0,
            ];
        }

        return DB::transaction(function () use ($count) {
            $course = static::query()->whereKey($this->id)->lockForUpdate()->first();
            $applied = 0;
            $left = $count;

            $unpaid = ZoomMeeting::query()
                ->where('course_id', $course->id)
                ->where('is_paid', false)
                ->orderByRaw('COALESCE(actual_start_time, start_time) ASC')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($unpaid as $meeting) {
                if ($left <= 0) {
                    break;
                }
                $course->markMeetingAsPrepaid($meeting);
                $applied++;
                $left--;
            }

            if ($left > 0) {
                $course->increment('prepaid_sessions', $left);
            }

            $course->refresh();

            return [
                'applied' => $applied,
                'remaining' => (int) $course->prepaid_sessions,
                'leftover_added' => $left,
            ];
        });
    }

    /**
     * تعديل رصيد الحصص المدفوعة مقدماً المتبقية دون تغيير الحصص التي طُبّق عليها الدفع مسبقاً.
     */
    public function setPrepaidRemaining(int $count): array
    {
        $count = max(0, $count);
        $this->update(['prepaid_sessions' => $count]);

        return $this->fresh()->prepaidSummary();
    }

    /**
     * استهلاك حصة واحدة من رصيد الدفع المقدم عند إنشاء اجتماع جديد.
     */
    public function consumePrepaidForMeeting(ZoomMeeting $meeting): bool
    {
        if ($meeting->course_id !== $this->id || $meeting->is_paid) {
            return false;
        }

        return DB::transaction(function () use ($meeting) {
            $course = static::query()->whereKey($this->id)->lockForUpdate()->first();

            if ((int) $course->prepaid_sessions < 1) {
                return false;
            }

            $freshMeeting = ZoomMeeting::query()->whereKey($meeting->id)->lockForUpdate()->first();
            if (!$freshMeeting || $freshMeeting->is_paid) {
                return false;
            }

            $course->decrement('prepaid_sessions');
            $course->markMeetingAsPrepaid($freshMeeting);
            $meeting->refresh();

            return true;
        });
    }

    /**
     * إعادة حصة للرصيد إذا أُلغي تعليم حصة كانت مدفوعة مقدماً.
     */
    public function restorePrepaidFromMeeting(ZoomMeeting $meeting): void
    {
        if ($meeting->course_id !== $this->id || !$meeting->is_prepaid) {
            return;
        }

        DB::transaction(function () use ($meeting) {
            $course = static::query()->whereKey($this->id)->lockForUpdate()->first();
            $course->increment('prepaid_sessions');
        });
    }

    public function markMeetingAsPrepaid(ZoomMeeting $meeting): void
    {
        if ($meeting->session_price === null) {
            $meeting->session_price = $this->price;
        }
        $meeting->is_paid = true;
        $meeting->is_prepaid = true;
        $meeting->due_notice = false;
        $meeting->save();
    }

    public function prepaidSummary(): array
    {
        $remaining = (int) $this->prepaid_sessions;
        $sessionPrice = (float) ($this->price ?? 0);
        $appliedCount = ZoomMeeting::query()
            ->where('course_id', $this->id)
            ->where('is_prepaid', true)
            ->count();
        $remainingValue = $remaining * $sessionPrice;

        return [
            'remaining' => $remaining,
            'applied_count' => $appliedCount,
            'session_price' => $sessionPrice,
            'remaining_value' => round($remainingValue, 2),
            'remaining_value_format' => number_format($remainingValue, 2) . ' ر.س',
        ];
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }
}
