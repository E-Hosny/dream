<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CoursePayment;
use App\Services\CoursePaymentSyncService;
use App\Services\MoyasarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Handle redirect after successful Moyasar payment.
     */
    public function success(Request $request, CoursePaymentSyncService $syncService)
    {
        $user = Auth::user();
        $invoiceId = $request->query('id') ?? $request->query('invoice');

        if ($invoiceId) {
            $payment = CoursePayment::where('moyasar_invoice_id', $invoiceId)
                ->where('student_id', $user->id)
                ->first();

            if ($payment) {
                $syncService->syncPayment($payment);
            }
        } else {
            $syncService->syncStudentUnpaidPayments($user->id);
        }

        return redirect()->route('student.dashboard')
            ->with('success', 'تم الدفع بنجاح! شكراً لك.');
    }
}
