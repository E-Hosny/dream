<?php

namespace App\Models;

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
        'moyasar_invoice_id',
        'moyasar_invoice_url',
        'status',
        'paid_at',
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

    public static function findByMoyasarInvoiceId(string $invoiceId): ?self
    {
        return static::where('moyasar_invoice_id', $invoiceId)->first();
    }

    public function syncFromMoyasarInvoice(array $invoice): bool
    {
        $status = $invoice['status'] ?? null;

        if (!$status) {
            return false;
        }

        $this->status = $status;

        if ($status === 'paid') {
            $this->paid_at = $this->paid_at ?? now();
        }

        $this->save();

        return true;
    }
}
