<?php

namespace App\Models;

use App\Services\PaddleService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoursePayment extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'created_by',
        'amount',
        'amount_format',
        'currency',
        'description',
        'paddle_transaction_id',
        'paddle_checkout_url',
        'moyasar_invoice_id',
        'moyasar_invoice_url',
        'status',
        'paid_at',
    ];

    protected $appends = [
        'payment_url',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public const UNPAID_STATUSES = ['initiated'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isUnpaid(): bool
    {
        return in_array($this->status, self::UNPAID_STATUSES, true);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', self::UNPAID_STATUSES);
    }

    public function getPaymentUrlAttribute(): ?string
    {
        $url = $this->paddle_checkout_url ?: $this->moyasar_invoice_url;

        if ($this->paddle_transaction_id) {
            return app(\App\Services\PaddleService::class)
                ->normalizeCheckoutUrl($url, $this->paddle_transaction_id);
        }

        return $url;
    }

    public function getProviderPaymentIdAttribute(): ?string
    {
        return $this->paddle_transaction_id ?: $this->moyasar_invoice_id;
    }

    public static function findByPaddleTransactionId(string $transactionId): ?self
    {
        return static::where('paddle_transaction_id', $transactionId)->first();
    }

    public function syncFromPaddleTransaction(array $transaction): bool
    {
        $status = $transaction['status'] ?? null;

        if (!$status) {
            return false;
        }

        if (PaddleService::isPaidStatus($status)) {
            $this->status = 'paid';
            $this->paid_at = $this->paid_at ?? now();
        } elseif ($status === 'canceled') {
            $this->status = 'canceled';
        } elseif (in_array($status, ['past_due', 'failed'], true)) {
            $this->status = 'failed';
        }

        if (!empty($transaction['checkout']['url'])) {
            $this->paddle_checkout_url = $transaction['checkout']['url'];
        }

        $this->save();

        return true;
    }
}
