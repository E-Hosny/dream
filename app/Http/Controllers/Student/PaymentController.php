<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CoursePayment;
use App\Services\CoursePaymentSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Handle redirect after successful Paddle checkout.
     */
    public function success(Request $request, CoursePaymentSyncService $syncService)
    {
        $user = Auth::user();
        $transactionId = $request->query('_ptxn')
            ?? $request->query('transaction_id')
            ?? $request->query('id');

        if ($transactionId) {
            $payment = CoursePayment::where('paddle_transaction_id', $transactionId)
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
