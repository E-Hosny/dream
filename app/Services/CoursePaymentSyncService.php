<?php

namespace App\Services;

use App\Models\CoursePayment;
use Illuminate\Support\Facades\Log;

class CoursePaymentSyncService
{
    public function __construct(
        private MoyasarService $moyasar
    ) {}

    public function syncPayment(CoursePayment $payment): bool
    {
        if ($payment->isPaid()) {
            return true;
        }

        try {
            $invoice = $this->moyasar->fetchInvoice($payment->moyasar_invoice_id);
            $payment->syncFromMoyasarInvoice($invoice);

            return $payment->fresh()->isPaid();
        } catch (\Exception $e) {
            Log::warning('Failed to sync payment status from Moyasar', [
                'payment_id' => $payment->id,
                'invoice_id' => $payment->moyasar_invoice_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function syncStudentUnpaidPayments(int $studentId): void
    {
        CoursePayment::where('student_id', $studentId)
            ->unpaid()
            ->get()
            ->each(fn (CoursePayment $payment) => $this->syncPayment($payment));
    }

    public function syncAllUnpaidPayments(): void
    {
        CoursePayment::unpaid()
            ->get()
            ->each(fn (CoursePayment $payment) => $this->syncPayment($payment));
    }
}
