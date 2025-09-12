<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZoomMeeting extends Model
{
    protected $fillable = [
        'course_id',
        'course_schedule_id',
        'zoom_account_id',
        'zoom_meeting_id',
        'topic',
        'start_time',
        'actual_start_time',
        'actual_end_time',
        'duration',
        'join_url',
        'start_url',
        'password',
        'status',
        'host_email',
        'meeting_type',
        'timezone',
        'settings',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // العلاقات
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function courseSchedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class);
    }

    public function zoomAccount(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ZoomAccount::class);
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ZoomMeetingParticipant::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'meeting_id');
    }

    // Accessors
    public function getStatusTextAttribute(): string
    {
        $statuses = [
            'scheduled' => 'مجدول',
            'started' => 'بدأ',
            'ended' => 'انتهى',
            'cancelled' => 'ملغي',
            'created' => 'تم إنشاؤه'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            'scheduled' => 'bg-blue-100 text-blue-800',
            'started' => 'bg-green-100 text-green-800',
            'ended' => 'bg-gray-100 text-gray-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'created' => 'bg-yellow-100 text-yellow-800'
        ];

        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getIsActiveAttribute(): bool
    {
        return in_array($this->status, ['scheduled', 'started']);
    }

    public function getCanJoinAttribute(): bool
    {
        if ($this->status === 'started') {
            return true;
        }

        if ($this->status === 'scheduled') {
            $now = now();
            $startTime = $this->start_time;
            $endTime = $startTime->copy()->addMinutes($this->duration);
            
            // يمكن الانضمام قبل 15 دقيقة من بداية الاجتماع
            return $now->between($startTime->subMinutes(15), $endTime);
        }

        return false;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['scheduled', 'started']);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeStarted($query)
    {
        return $query->where('status', 'started');
    }

    public function scopeEnded($query)
    {
        return $query->where('status', 'ended');
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByInstructor($query, $instructorId)
    {
        return $query->whereHas('course', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        });
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('start_time', '>=', now())
                    ->where('start_time', '<=', now()->addDays($days))
                    ->where('status', 'scheduled');
    }

    public function scopeActiveAndValid($query)
    {
        return $query->where('status', 'started')
                    ->where('created_at', '>', now()->subHours(8)) // الاجتماعات التي لا تزيد عن 8 ساعات
                    ->where(function($query) {
                        // إما أن يكون start_time في المستقبل أو خلال الساعات الماضية القليلة
                        $query->where('start_time', '>', now()->subHours(4))
                              ->orWhere('start_time', '<=', now()->addHours(1));
                    });
    }

    /**
     * تنظيف الاجتماعات القديمة
     */
    public static function cleanupOldMeetings()
    {
        return static::where('status', 'started')
            ->where('created_at', '<', now()->subHours(8))
            ->update(['status' => 'ended', 'updated_at' => now()]);
    }
}
