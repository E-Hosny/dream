<?php

namespace App\Http\Controllers;

use App\Models\CoursePayment;
use Illuminate\Http\Request;

class PaddleCheckoutController extends Controller
{
    public function show(Request $request)
    {
        $transactionId = $request->query('_ptxn')
            ?? $request->query('transaction_id');

        abort_if(!$transactionId, 404);

        $payment = CoursePayment::with(['course', 'student'])
            ->where('paddle_transaction_id', $transactionId)
            ->first();

        abort_if(!$payment, 404);

        if ($request->user() && $request->user()->hasRole('student') && $payment->student_id !== $request->user()->id) {
            abort(403);
        }

        if ($payment->isPaid()) {
            return redirect()->route('student.dashboard')
                ->with('success', 'هذه الفاتورة مدفوعة مسبقاً.');
        }

        if (!$payment->isUnpaid()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'هذه الفاتورة ملغاة أو غير متاحة للدفع.');
        }

        return response()
            ->view('payments.paddle-checkout', [
                'payment' => $payment,
                'transactionId' => $transactionId,
                'clientToken' => config('services.paddle.client_token'),
                'environment' => config('services.paddle.environment', 'sandbox'),
                'successUrl' => url('/student/payments/success') . '?_ptxn=' . urlencode($transactionId),
                'customerEmail' => $payment->student?->routeNotificationForMail(),
                'customerCountry' => 'SA',
            ])
            ->header('Content-Security-Policy', '');
    }
}
