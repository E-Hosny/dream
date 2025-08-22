<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSchedule extends Model
{
    protected $fillable = [
        'course_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // الحصول على اسم اليوم باللغة العربية
    public function getDayNameArAttribute(): string
    {
        $days = [
            'saturday' => 'السبت',
            'sunday' => 'الأحد',
            'monday' => 'الاثنين',
            'tuesday' => 'الثلاثاء',
            'wednesday' => 'الأربعاء',
            'thursday' => 'الخميس',
            'friday' => 'الجمعة',
        ];
        
        return $days[$this->day_of_week] ?? $this->day_of_week;
    }

    // الحصول على اسم اليوم باللغة الإنجليزية
    public function getDayNameEnAttribute(): string
    {
        $days = [
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
        ];
        
        return $days[$this->day_of_week] ?? $this->day_of_week;
    }

    // الحصول على اسم اليوم حسب اللغة الحالية
    public function getLocalizedDayNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->day_name_ar : $this->day_name_en;
    }

    // الحصول على التوقيت التالي للكورس
    public function getNextOccurrenceAttribute()
    {
        $today = now();
        $currentDay = strtolower($today->format('l'));
        $currentTime = $today->format('H:i:s');
        
        // إذا كان اليوم الحالي هو يوم الكورس والوقت لم يمر بعد
        if ($currentDay === $this->day_of_week && $currentTime < $this->start_time) {
            return $today->setTimeFrom($this->start_time);
        }
        
        // البحث عن اليوم التالي
        $nextDay = $today->copy();
        $daysToAdd = 0;
        
        switch ($this->day_of_week) {
            case 'saturday':
                $daysToAdd = $currentDay === 'friday' ? 1 : (6 - $today->dayOfWeek + 6) % 7;
                break;
            case 'sunday':
                $daysToAdd = $currentDay === 'saturday' ? 1 : (7 - $today->dayOfWeek) % 7;
                break;
            case 'monday':
                $daysToAdd = $currentDay === 'sunday' ? 1 : (8 - $today->dayOfWeek) % 7;
                break;
            case 'tuesday':
                $daysToAdd = $currentDay === 'monday' ? 1 : (9 - $today->dayOfWeek) % 7;
                break;
            case 'wednesday':
                $daysToAdd = $currentDay === 'tuesday' ? 1 : (10 - $today->dayOfWeek) % 7;
                break;
            case 'thursday':
                $daysToAdd = $currentDay === 'wednesday' ? 1 : (11 - $today->dayOfWeek) % 7;
                break;
            case 'friday':
                $daysToAdd = $currentDay === 'thursday' ? 1 : (12 - $today->dayOfWeek) % 7;
                break;
        }
        
        return $nextDay->addDays($daysToAdd)->setTimeFrom($this->start_time);
    }
}
