<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MeetingAttendance extends Model
{
    protected $table = 'meeting_attendance';

    protected $fillable = [
        'meeting_id',
        'user_id',
        'user_type',
        'action_type',
        'action_time',
        'ip_address',
        'user_agent',
        'duration_seconds',
        'additional_data'
    ];

    protected $casts = [
        'action_time' => 'datetime',
        'additional_data' => 'array',
        'duration_seconds' => 'integer'
    ];

    // Constants for user types
    const USER_TYPE_TEACHER = 'teacher';
    const USER_TYPE_STUDENT = 'student';

    // Constants for action types
    const ACTION_JOIN = 'join';
    const ACTION_LEAVE = 'leave';
    const ACTION_MEETING_START = 'meeting_start';
    const ACTION_MEETING_END = 'meeting_end';

    /**
     * العلاقة مع الاجتماع
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(ZoomMeeting::class, 'meeting_id');
    }

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope للحصول على حضور المعلمين فقط
     */
    public function scopeTeachers($query)
    {
        return $query->where('user_type', self::USER_TYPE_TEACHER);
    }

    /**
     * Scope للحصول على حضور الطلاب فقط
     */
    public function scopeStudents($query)
    {
        return $query->where('user_type', self::USER_TYPE_STUDENT);
    }

    /**
     * Scope للحصول على أحداث الانضمام
     */
    public function scopeJoinActions($query)
    {
        return $query->whereIn('action_type', [self::ACTION_JOIN, self::ACTION_MEETING_START]);
    }

    /**
     * Scope للحصول على أحداث المغادرة
     */
    public function scopeLeaveActions($query)
    {
        return $query->whereIn('action_type', [self::ACTION_LEAVE, self::ACTION_MEETING_END]);
    }

    /**
     * حساب مدة الحضور بالدقائق
     */
    public function getDurationInMinutesAttribute()
    {
        return $this->duration_seconds ? round($this->duration_seconds / 60, 2) : null;
    }

    /**
     * تنسيق مدة الحضور
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_seconds) {
            return null;
        }

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * الحصول على نص وصفي لنوع الإجراء
     */
    public function getActionTypeTextAttribute()
    {
        return match($this->action_type) {
            self::ACTION_JOIN => 'انضمام للاجتماع',
            self::ACTION_LEAVE => 'مغادرة الاجتماع',
            self::ACTION_MEETING_START => 'بداية الاجتماع',
            self::ACTION_MEETING_END => 'نهاية الاجتماع',
            default => $this->action_type
        };
    }

    /**
     * الحصول على نص وصفي لنوع المستخدم
     */
    public function getUserTypeTextAttribute()
    {
        return match($this->user_type) {
            self::USER_TYPE_TEACHER => 'معلم',
            self::USER_TYPE_STUDENT => 'طالب',
            default => $this->user_type
        };
    }

    /**
     * تسجيل حدث حضور جديد
     */
    public static function logAttendance(
        int $meetingId,
        int $userId,
        string $userType,
        string $actionType,
        array $additionalData = null
    ) {
        return static::create([
            'meeting_id' => $meetingId,
            'user_id' => $userId,
            'user_type' => $userType,
            'action_type' => $actionType,
            'action_time' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'additional_data' => $additionalData
        ]);
    }

    /**
     * حساب وتحديث مدة الحضور
     */
    public static function calculateAndUpdateDuration(int $meetingId, int $userId)
    {
        $joinRecord = static::where('meeting_id', $meetingId)
            ->where('user_id', $userId)
            ->whereIn('action_type', [self::ACTION_JOIN, self::ACTION_MEETING_START])
            ->orderBy('action_time', 'desc')
            ->first();

        $leaveRecord = static::where('meeting_id', $meetingId)
            ->where('user_id', $userId)
            ->whereIn('action_type', [self::ACTION_LEAVE, self::ACTION_MEETING_END])
            ->orderBy('action_time', 'desc')
            ->first();

        if ($joinRecord && $leaveRecord && $leaveRecord->action_time > $joinRecord->action_time) {
            $duration = $leaveRecord->action_time->diffInSeconds($joinRecord->action_time);
            
            $leaveRecord->update([
                'duration_seconds' => $duration
            ]);

            return $duration;
        }

        return null;
    }
}
