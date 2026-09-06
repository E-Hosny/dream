<?php

namespace App\Http\Controllers;

use App\Models\CoursePayment;
use App\Services\PaddleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaddleWebhookController extends Controller
{
    public function handle(Request $request, PaddleService $paddle)
    {
        $rawBody = $request->getContent();

        if (!$paddle->verifyWebhookSignature($rawBody, $request->header('Paddle-Signature'))) {
            Log::warning('Paddle webhook signature verification failed');

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $payload = $request->all();
        $eventType = $payload['event_type'] ?? null;
        $transaction = $payload['data'] ?? [];

        Log::info('Paddle webhook received', [
            'event_type' => $eventType,
            'transaction_id' => $transaction['id'] ?? null,
        ]);

        $transactionId = $transaction['id'] ?? null;

        if (!$transactionId || !str_starts_with((string) $eventType, 'transaction.')) {
            return response()->json(['message' => 'Ignored']);
        }

        $payment = CoursePayment::findByPaddleTransactionId($transactionId);

        if (!$payment) {
            $customData = $transaction['custom_data'] ?? [];
            if (!empty($customData['student_id']) && !empty($customData['course_id'])) {
                $payment = CoursePayment::where('student_id', $customData['student_id'])
                    ->where('course_id', $customData['course_id'])
                    ->unpaid()
                    ->latest()
                    ->first();
            }
        }

        if (!$payment) {
            Log::warning('Paddle webhook: payment not found', ['transaction_id' => $transactionId]);

            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->syncFromPaddleTransaction($transaction);

        return response()->json(['message' => 'OK']);
    }
}
