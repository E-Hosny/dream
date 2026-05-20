<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoyasarService
{
    protected string $secretKey;
    protected string $baseUrl = 'https://api.moyasar.com/v1';

    public function __construct()
    {
        $this->secretKey = config('services.moyasar.secret_key', '');
    }

    /**
     * Create a Moyasar invoice and return the API response data.
     *
     * @throws \RuntimeException
     */
    public function createInvoice(int $amountHalalas, string $description, string $callbackUrl, ?string $successUrl = null): array
    {
        if (empty($this->secretKey)) {
            throw new \RuntimeException('Moyasar secret key is not configured.');
        }

        $payload = [
            'amount' => $amountHalalas,
            'currency' => config('services.moyasar.currency', 'SAR'),
            'description' => $description,
            'callback_url' => $callbackUrl,
        ];

        if ($successUrl) {
            $payload['success_url'] = $successUrl;
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->post("{$this->baseUrl}/invoices", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Moyasar API Error: ' . $response->body(), [
                'status' => $response->status(),
                'payload' => $payload,
            ]);

            throw new \RuntimeException('Failed to create Moyasar invoice: ' . $response->body());
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Moyasar Service Error: ' . $e->getMessage());
            throw new \RuntimeException('Failed to connect to Moyasar: ' . $e->getMessage());
        }
    }

    /**
     * Fetch a Moyasar invoice by ID.
     *
     * @throws \RuntimeException
     */
    public function fetchInvoice(string $invoiceId): array
    {
        if (empty($this->secretKey)) {
            throw new \RuntimeException('Moyasar secret key is not configured.');
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->acceptJson()
                ->get("{$this->baseUrl}/invoices/{$invoiceId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Moyasar fetch invoice error: ' . $response->body(), [
                'invoice_id' => $invoiceId,
                'status' => $response->status(),
            ]);

            throw new \RuntimeException('Failed to fetch Moyasar invoice: ' . $response->body());
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Moyasar Service Error: ' . $e->getMessage());
            throw new \RuntimeException('Failed to connect to Moyasar: ' . $e->getMessage());
        }
    }

    /**
     * Convert SAR amount to halalas (smallest currency unit).
     */
    public static function sarToHalalas(float $amountSar): int
    {
        return (int) round($amountSar * 100);
    }
}
