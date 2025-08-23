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
        
        // تحويل اسم اليوم إلى رقم (0 = الأحد، 6 = السبت)
        $dayMap = [
            'sunday' => 0,
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
        ];
        
        $currentDayNum = $today->dayOfWeek; // 0 = الأحد، 6 = السبت
        $targetDayNum = $dayMap[$this->day_of_week];
        
        // إذا كان اليوم الحالي هو يوم الكورس والوقت لم يمر بعد
        if ($currentDayNum === $targetDayNum && $currentTime < $this->start_time->format('H:i:s')) {
            return $today->copy()->setTimeFrom($this->start_time);
        }
        
        // حساب الأيام المطلوبة للوصول إلى اليوم التالي
        $daysToAdd = ($targetDayNum - $currentDayNum + 7) % 7;
        
        // إذا كان اليوم الحالي هو يوم الكورس والوقت قد مر، ننتقل للأسبوع التالي
        if ($daysToAdd === 0 && $currentTime >= $this->start_time->format('H:i:s')) {
            $daysToAdd = 7;
        }
        
        return $today->copy()->addDays($daysToAdd)->setTimeFrom($this->start_time);
    }
}
