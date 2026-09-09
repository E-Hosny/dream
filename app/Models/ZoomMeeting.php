<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        'session_price',
        'is_paid',
        'is_prepaid',
        'due_notice',
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
        'session_price' => 'decimal:2',
        'is_paid' => 'boolean',
        'is_prepaid' => 'boolean',
        'due_notice' => 'boolean',
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
    /**
     * ملخص المستحقات الظاهرة للطالب من الحصص المحددة بإشعار الاستحقاق.
     */
    public static function dueNoticeSummary(int $courseId, float $defaultSessionPrice = 0): ?array
    {
        $meetings = static::query()
            ->where('course_id', $courseId)
            ->where('due_notice', true)
            ->where('is_paid', false)
            ->get();

        if ($meetings->isEmpty()) {
            return null;
        }

        $sessionsCount = $meetings->count();
        $amount = $meetings->sum(function (self $meeting) use ($defaultSessionPrice) {
            return (float) ($meeting->session_price ?? $defaultSessionPrice);
        });

        return [
            'sessions_count' => $sessionsCount,
            'amount' => round($amount, 2),
            'amount_format' => number_format($amount, 2) . ' ر.س',
            'message_ar' => sprintf(
                'لديك مبلغ مستحق بقيمة %s لعدد %d حصة.',
                number_format($amount, 2) . ' ر.س',
                $sessionsCount
            ),
            'message_en' => sprintf(
                'You have an outstanding amount of %s for %d session(s).',
                number_format($amount, 2) . ' SAR',
                $sessionsCount
            ),
        ];
    }

    /**
     * إحصائيات الحصص والدفع لشهر معيّن (YYYY-MM).
     *
     * @param  array<int>|null  $courseIds
     */
    public static function monthlyPaymentStats(?string $month = null, ?array $courseIds = null): array
    {
        $month = $month && preg_match('/^\d{4}-\d{2}$/', $month)
            ? $month
            : now('Asia/Riyadh')->format('Y-m');

        $start = Carbon::createFromFormat('Y-m', $month, 'Asia/Riyadh')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $query = static::query()
            ->with('course:id,price')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('actual_start_time', [$start, $end])
                    ->orWhere(function ($inner) use ($start, $end) {
                        $inner->whereNull('actual_start_time')
                            ->whereBetween('start_time', [$start, $end]);
                    });
            })
            ->where('status', '!=', 'cancelled');

        if ($courseIds !== null) {
            if (empty($courseIds)) {
                return self::emptyMonthlyStats($month, $start);
            }
            $query->whereIn('course_id', $courseIds);
        }

        $meetings = $query->get();

        $totalCount = $meetings->count();
        $paidMeetings = $meetings->where('is_paid', true);
        $unpaidMeetings = $meetings->where('is_paid', false);

        $priceOf = function (self $meeting): float {
            return (float) ($meeting->session_price ?? $meeting->course?->price ?? 0);
        };

        $paidAmount = $paidMeetings->sum($priceOf);
        $unpaidAmount = $unpaidMeetings->sum($priceOf);
        $totalAmount = $paidAmount + $unpaidAmount;
        $dueNoticeCount = $meetings->where('due_notice', true)->where('is_paid', false)->count();

        $monthOptionsQuery = static::query()->where('status', '!=', 'cancelled');
        if ($courseIds !== null) {
            $monthOptionsQuery->whereIn('course_id', $courseIds);
        }

        $monthOptions = $monthOptionsQuery
            ->get(['actual_start_time', 'start_time'])
            ->map(function (self $meeting) {
                $date = $meeting->actual_start_time ?? $meeting->start_time;
                return $date ? $date->timezone('Asia/Riyadh')->format('Y-m') : null;
            })
            ->filter()
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        if (!in_array($month, $monthOptions, true)) {
            array_unshift($monthOptions, $month);
            $monthOptions = array_values(array_unique($monthOptions));
            rsort($monthOptions);
        }

        return [
            'month' => $month,
            'month_label_ar' => $start->locale('ar')->translatedFormat('F Y'),
            'month_label_en' => $start->locale('en')->translatedFormat('F Y'),
            'total_sessions' => $totalCount,
            'paid_sessions' => $paidMeetings->count(),
            'unpaid_sessions' => $unpaidMeetings->count(),
            'paid_amount' => round($paidAmount, 2),
            'unpaid_amount' => round($unpaidAmount, 2),
            'total_amount' => round($totalAmount, 2),
            'paid_amount_format' => number_format($paidAmount, 2) . ' ر.س',
            'unpaid_amount_format' => number_format($unpaidAmount, 2) . ' ر.س',
            'total_amount_format' => number_format($totalAmount, 2) . ' ر.س',
            'due_notice_sessions' => $dueNoticeCount,
            'month_options' => $monthOptions,
        ];
    }

    private static function emptyMonthlyStats(string $month, Carbon $start): array
    {
        return [
            'month' => $month,
            'month_label_ar' => $start->locale('ar')->translatedFormat('F Y'),
            'month_label_en' => $start->locale('en')->translatedFormat('F Y'),
            'total_sessions' => 0,
            'paid_sessions' => 0,
            'unpaid_sessions' => 0,
            'paid_amount' => 0,
            'unpaid_amount' => 0,
            'total_amount' => 0,
            'paid_amount_format' => '0.00 ر.س',
            'unpaid_amount_format' => '0.00 ر.س',
            'total_amount_format' => '0.00 ر.س',
            'due_notice_sessions' => 0,
            'month_options' => [$month],
        ];
    }

    public static function cleanupOldMeetings()
    {
        return static::where('status', 'started')
            ->where('created_at', '<', now()->subHours(8))
            ->update(['status' => 'ended', 'updated_at' => now()]);
    }

    /**
     * مدة الجلسة بالثواني (من أوقات البداية/النهاية الفعلية، أو حقل المدة).
     */
    public function sessionDurationSeconds(): int
    {
        $start = $this->actual_start_time ?: $this->start_time;
        $end = $this->actual_end_time;

        if ($start && $end && $end->greaterThan($start)) {
            return (int) $start->diffInSeconds($end);
        }

        if ($start && $this->status === 'started') {
            return (int) $start->diffInSeconds(now());
        }

        if ($this->duration) {
            return (int) $this->duration * 60;
        }

        $teacherDuration = MeetingAttendance::where('meeting_id', $this->id)
            ->where('user_type', MeetingAttendance::USER_TYPE_TEACHER)
            ->whereNotNull('duration_seconds')
            ->max('duration_seconds');

        return (int) ($teacherDuration ?: 0);
    }

    /**
     * اجتماعات نفس الكورس في يوم معيّن (حسب Asia/Riyadh).
     */
    public static function forCourseOnDate(int $courseId, ?Carbon $date = null)
    {
        $date = ($date ?: now('Asia/Riyadh'))->copy()->timezone('Asia/Riyadh');
        $dayStart = $date->copy()->startOfDay()->timezone(config('app.timezone'));
        $dayEnd = $date->copy()->endOfDay()->timezone(config('app.timezone'));

        return static::where('course_id', $courseId)
            ->where(function ($query) use ($dayStart, $dayEnd) {
                $query->whereBetween(DB::raw('COALESCE(actual_start_time, start_time)'), [$dayStart, $dayEnd])
                    ->orWhere(function ($q) use ($dayStart, $dayEnd) {
                        $q->whereNull('actual_start_time')
                            ->whereNull('start_time')
                            ->whereBetween('created_at', [$dayStart, $dayEnd]);
                    });
            });
    }

    /**
     * اجتماع نشط لنفس الكورس خلال اليوم الحالي.
     */
    public static function findActiveForCourseToday(int $courseId): ?self
    {
        return static::forCourseOnDate($courseId)
            ->where('status', 'started')
            ->orderByDesc('actual_start_time')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * دمج جلسات نفس اليوم لنفس الكورس والإبقاء على الأطول مدة.
     * لا يدمج إن وُجد اجتماع ما زال started (ما عدا عند تمرير $preferMeetingId بعد إنهائه).
     */
    public static function consolidateSameDaySessions(int $courseId, ?Carbon $date = null, ?int $preferMeetingId = null): ?self
    {
        $meetings = static::forCourseOnDate($courseId, $date)
            ->orderBy('id')
            ->get();

        if ($meetings->count() <= 1) {
            return $meetings->first();
        }

        $stillActive = $meetings->where('status', 'started');
        if ($stillActive->isNotEmpty() && !$preferMeetingId) {
            return $stillActive->sortByDesc('id')->first();
        }

        if ($stillActive->isNotEmpty() && $preferMeetingId) {
            $activeOthers = $stillActive->where('id', '!=', $preferMeetingId);
            if ($activeOthers->isNotEmpty()) {
                return $stillActive->sortByDesc('id')->first();
            }
        }

        $keeper = $meetings->sort(function (self $a, self $b) {
            $durationCompare = $b->sessionDurationSeconds() <=> $a->sessionDurationSeconds();
            if ($durationCompare !== 0) {
                return $durationCompare;
            }

            return $b->id <=> $a->id;
        })->first();

        if ($preferMeetingId) {
            $preferred = $meetings->firstWhere('id', $preferMeetingId);
            if ($preferred && $preferred->sessionDurationSeconds() >= $keeper->sessionDurationSeconds()) {
                $keeper = $preferred;
            }
        }

        $duplicates = $meetings->where('id', '!=', $keeper->id);

        DB::transaction(function () use ($keeper, $duplicates, $meetings) {
            foreach ($duplicates as $duplicate) {
                MeetingAttendance::where('meeting_id', $duplicate->id)
                    ->update(['meeting_id' => $keeper->id]);

                Assignment::where('meeting_id', $duplicate->id)
                    ->update(['meeting_id' => $keeper->id]);

                ZoomMeetingParticipant::where('zoom_meeting_id', $duplicate->id)->delete();

                $duplicate->delete();
            }

            $starts = $meetings->map(fn (self $m) => $m->actual_start_time ?: $m->start_time)->filter();
            $ends = $meetings->map(fn (self $m) => $m->actual_end_time)->filter();

            $earliestStart = $starts->sort()->first();
            $latestEnd = $ends->sortDesc()->first();
            $durationMinutes = max(
                (int) $keeper->duration,
                $earliestStart && $latestEnd
                    ? (int) ceil($earliestStart->diffInSeconds($latestEnd) / 60)
                    : (int) ceil($keeper->sessionDurationSeconds() / 60)
            );

            $keeper->update([
                'actual_start_time' => $earliestStart ?: $keeper->actual_start_time,
                'start_time' => $earliestStart ?: $keeper->start_time,
                'actual_end_time' => $latestEnd ?: $keeper->actual_end_time,
                'duration' => max(1, $durationMinutes),
                'status' => $keeper->status === 'started' ? 'started' : 'ended',
            ]);
        });

        Log::info('Consolidated same-day Zoom meetings', [
            'course_id' => $courseId,
            'kept_meeting_id' => $keeper->id,
            'removed_count' => $duplicates->count(),
        ]);

        return $keeper->fresh();
    }

    /**
     * دمج كل أيام الكورس التي فيها أكثر من جلسة (للتنظيف عند عرض صفحة الاجتماعات).
     */
    public static function consolidateAllDaysForCourse(int $courseId): void
    {
        $dates = static::where('course_id', $courseId)
            ->get(['id', 'actual_start_time', 'start_time', 'created_at'])
            ->map(function (self $meeting) {
                $stamp = $meeting->actual_start_time ?: $meeting->start_time ?: $meeting->created_at;
                return $stamp ? $stamp->copy()->timezone('Asia/Riyadh')->toDateString() : null;
            })
            ->filter()
            ->unique()
            ->values();

        foreach ($dates as $dateString) {
            static::consolidateSameDaySessions(
                $courseId,
                Carbon::parse($dateString, 'Asia/Riyadh')
            );
        }
    }
}
