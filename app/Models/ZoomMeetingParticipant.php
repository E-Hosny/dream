<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZoomMeetingParticipant extends Model
{
    protected $fillable = [
        'zoom_meeting_id',
        'user_id',
        'join_time',
        'leave_time',
        'duration',
        'status',
        'role',
        'device_type',
        'ip_address',
        'location',
        'notes'
    ];

    protected $casts = [
        'join_time' => 'datetime',
        'leave_time' => 'datetime',
        'join_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // العلاقات
    public function zoomMeeting(): BelongsTo
    {
        return $this->belongsTo(ZoomMeeting::class, 'zoom_meeting_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getStatusTextAttribute(): string
    {
        $statuses = [
            'joined' => 'انضم',
            'left' => 'غادر',
            'present' => 'حاضر',
            'absent' => 'غائب'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getRoleTextAttribute(): string
    {
        $roles = [
            'host' => 'مضيف',
            'co-host' => 'مضيف مشارك',
            'participant' => 'مشارك'
        ];

        return $roles[$this->role] ?? $this->role;
    }

    public function getDurationTextAttribute(): string
    {
        if (!$this->duration) {
            return 'غير محدد';
        }

        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($hours > 0) {
            return "{$hours} ساعة و {$minutes} دقيقة";
        }

        return "{$minutes} دقيقة";
    }

    // Scopes
    public function scopeByMeeting($query, $meetingId)
    {
        return $query->where('zoom_meeting_id', $meetingId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }
}
