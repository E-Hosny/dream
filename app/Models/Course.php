<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'description_ar',
        'image',
        'price',
        'duration_hours',
        'level',
        'status',
        'instructor_id',
        'max_students',
        'start_date',
        'end_date',
        'requirements',
        'learning_outcomes',
    ];

    protected $casts = [
        'requirements' => 'array',
        'learning_outcomes' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
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

    // العلاقة مع مواعيد الكورس
    public function schedules(): HasMany
    {
        return $this->hasMany(CourseSchedule::class);
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
