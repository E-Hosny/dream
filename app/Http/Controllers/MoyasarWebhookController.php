<?php

namespace App\Http\Controllers;

use App\Models\CoursePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MoyasarWebhookController extends Controller
{
    /**
     * Handle Moyasar invoice callback (POST when invoice status changes).
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        Log::info('Moyasar webhook received', ['payload' => $payload]);

        $invoiceId = $payload['id'] ?? null;

        if (!$invoiceId) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $payment = CoursePayment::findByMoyasarInvoiceId($invoiceId);

        if (!$payment) {
            Log::warning('Moyasar webhook: payment not found', ['invoice_id' => $invoiceId]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->syncFromMoyasarInvoice($payload);

        return response()->json(['message' => 'OK']);
    }
}
