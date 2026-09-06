<?php

namespace App\Services;

use App\Models\CoursePayment;
use Illuminate\Support\Facades\Log;

class CoursePaymentSyncService
{
    public function __construct(
        private PaddleService $paddle
    ) {}

    public function syncPayment(CoursePayment $payment): bool
    {
        if ($payment->isPaid()) {
            return true;
        }

        if (!$payment->paddle_transaction_id) {
            return false;
        }

        try {
            $transaction = $this->paddle->fetchTransaction($payment->paddle_transaction_id);
            $payment->syncFromPaddleTransaction($transaction);

            return $payment->fresh()->isPaid();
        } catch (\Exception $e) {
            Log::warning('Failed to sync payment status from Paddle', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->paddle_transaction_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function syncStudentUnpaidPayments(int $studentId): void
    {
        CoursePayment::where('student_id', $studentId)
            ->unpaid()
            ->whereNotNull('paddle_transaction_id')
            ->get()
            ->each(fn (CoursePayment $payment) => $this->syncPayment($payment));
    }

    public function syncAllUnpaidPayments(): void
    {
        CoursePayment::unpaid()
            ->whereNotNull('paddle_transaction_id')
            ->get()
            ->each(fn (CoursePayment $payment) => $this->syncPayment($payment));
    }
}
