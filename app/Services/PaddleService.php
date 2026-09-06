<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaddleService
{
    public const PAID_STATUSES = ['billed', 'paid', 'completed'];

    protected string $apiKey;
    protected string $baseUrl;
    protected string $currency;
    protected string $displayCurrency;
    protected string $webhookSecret;

    public function __construct()
    {
        $this->apiKey = (string) config('services.paddle.api_key', '');
        $this->currency = strtoupper((string) config('services.paddle.currency', 'USD'));
        $this->displayCurrency = strtoupper((string) config('services.paddle.display_currency', 'SAR'));
        $this->webhookSecret = (string) config('services.paddle.webhook_secret', '');
        $environment = config('services.paddle.environment', 'sandbox');

        $this->baseUrl = $environment === 'live'
            ? 'https://api.paddle.com'
            : 'https://sandbox-api.paddle.com';
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function displayCurrency(): string
    {
        return $this->displayCurrency;
    }

    /**
     * How many SAR equal 1 USD.
     */
    public function usdSarRate(): float
    {
        $rate = (float) config('services.paddle.usd_sar_rate', 3.75);

        return $rate > 0 ? $rate : 3.75;
    }

    /**
     * Convert a SAR amount in minor units to USD minor units for Paddle.
     */
    public function convertSarToUsdMinor(int $sarMinor): int
    {
        $sar = $sarMinor / 100;
        $usd = $sar / $this->usdSarRate();

        return (int) round($usd * 100);
    }

    /**
     * Create a Paddle transaction with a custom one-off amount and return checkout details.
     *
     * @throws \RuntimeException
     */
    public function createTransaction(
        int $amountMinor,
        string $description,
        string $productName,
        array $customData = [],
        ?string $customerEmail = null,
        ?string $customerName = null
    ): array {
        $this->ensureConfigured();

        $payload = [
            'items' => [
                [
                    'quantity' => 1,
                    'price' => [
                        'description' => $description,
                        'name' => $productName,
                        'unit_price' => [
                            'amount' => (string) $amountMinor,
                            'currency_code' => $this->currency,
                        ],
                        'quantity' => [
                            'minimum' => 1,
                            'maximum' => 1,
                        ],
                        'product' => [
                            'name' => $productName,
                            'description' => $description,
                            'tax_category' => 'standard',
                        ],
                    ],
                ],
            ],
            'currency_code' => $this->currency,
            'custom_data' => $this->stringifyCustomData($customData),
        ];

        if ($customerEmail) {
            $customer = $this->findOrCreateCustomer($customerEmail, $customerName);
            $address = $this->ensureCustomerAddress($customer['id']);
            $payload['customer_id'] = $customer['id'];
            $payload['address_id'] = $address['id'];
        }

        $response = $this->client()->post("{$this->baseUrl}/transactions", $payload);

        if (!$response->successful()) {
            Log::error('Paddle create transaction error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException($this->extractErrorMessage($response->json(), $response->body()));
        }

        $transaction = $response->json('data') ?? [];
        $checkoutUrl = $this->checkoutUrlForTransaction($transaction['id'] ?? '');

        if (empty($transaction['id']) || empty($checkoutUrl)) {
            throw new \RuntimeException('Paddle returned a transaction without a checkout URL.');
        }

        return [
            'id' => $transaction['id'],
            'status' => $transaction['status'] ?? 'draft',
            'checkout_url' => $checkoutUrl,
            'currency' => $transaction['currency_code'] ?? $this->currency,
            'amount' => $amountMinor,
        ];
    }

    /**
     * Fetch a transaction from Paddle.
     *
     * @throws \RuntimeException
     */
    public function fetchTransaction(string $transactionId): array
    {
        $this->ensureConfigured();

        $response = $this->client()->get("{$this->baseUrl}/transactions/{$transactionId}");

        if (!$response->successful()) {
            Log::error('Paddle fetch transaction error', [
                'transaction_id' => $transactionId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException($this->extractErrorMessage($response->json(), $response->body()));
        }

        return $response->json('data') ?? [];
    }

    public function cancelTransaction(?string $transactionId): bool
    {
        if (!$transactionId) {
            return true;
        }

        try {
            $this->ensureConfigured();
        } catch (\RuntimeException $e) {
            return false;
        }

        $response = $this->client()->patch("{$this->baseUrl}/transactions/{$transactionId}", [
            'status' => 'canceled',
        ]);

        if ($response->successful()) {
            return true;
        }

        Log::warning('Paddle cancel transaction failed', [
            'transaction_id' => $transactionId,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return false;
    }

    public function findOrCreateCustomer(string $email, ?string $name = null): array
    {
        $list = $this->client()->get("{$this->baseUrl}/customers", [
            'email' => $email,
        ]);

        if ($list->successful()) {
            $existing = $list->json('data.0');
            if (!empty($existing['id'])) {
                return $existing;
            }
        }

        $response = $this->client()->post("{$this->baseUrl}/customers", array_filter([
            'email' => $email,
            'name' => $name,
        ]));

        if ($response->successful()) {
            return $response->json('data') ?? [];
        }

        // Customer may already exist; retry lookup.
        $retry = $this->client()->get("{$this->baseUrl}/customers", [
            'email' => $email,
        ]);

        $existing = $retry->json('data.0');
        if (!empty($existing['id'])) {
            return $existing;
        }

        throw new \RuntimeException($this->extractErrorMessage($response->json(), $response->body()));
    }

    public function ensureCustomerAddress(string $customerId, string $countryCode = 'SA'): array
    {
        $list = $this->client()->get("{$this->baseUrl}/customers/{$customerId}/addresses");

        if ($list->successful()) {
            foreach ($list->json('data') ?? [] as $address) {
                if (($address['country_code'] ?? null) === $countryCode && ($address['status'] ?? '') === 'active') {
                    return $address;
                }
            }
        }

        $response = $this->client()->post("{$this->baseUrl}/customers/{$customerId}/addresses", [
            'country_code' => $countryCode,
            'description' => 'Default',
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException($this->extractErrorMessage($response->json(), $response->body()));
        }

        return $response->json('data') ?? [];
    }

    public static function toMinorUnits(float $amount): int
    {
        return (int) round($amount * 100);
    }

    public static function formatAmount(int $amountMinor, string $currency): string
    {
        $code = strtoupper($currency);
        $label = $code === 'SAR' ? 'ر.س' : $code;

        return number_format($amountMinor / 100, 2) . ' ' . $label;
    }

    public static function isPaidStatus(?string $status): bool
    {
        return in_array($status, self::PAID_STATUSES, true);
    }

    public function verifyWebhookSignature(string $rawBody, ?string $signatureHeader): bool
    {
        if ($this->webhookSecret === '') {
            return true;
        }

        if (!$signatureHeader) {
            return false;
        }

        $parts = [];
        foreach (explode(';', $signatureHeader) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);
            if ($key && $value !== null) {
                $parts[$key][] = $value;
            }
        }

        $timestamp = $parts['ts'][0] ?? null;
        $hashes = $parts['h1'] ?? [];

        if (!$timestamp || empty($hashes)) {
            return false;
        }

        $signedPayload = $timestamp . ':' . $rawBody;
        $expected = hash_hmac('sha256', $signedPayload, $this->webhookSecret);

        foreach ($hashes as $hash) {
            if (hash_equals($expected, $hash)) {
                return true;
            }
        }

        return false;
    }

    public function checkoutUrlForTransaction(string $transactionId): string
    {
        return url('/checkout') . '?_ptxn=' . urlencode($transactionId);
    }

    public function normalizeCheckoutUrl(?string $checkoutUrl, string $transactionId): string
    {
        return $this->checkoutUrlForTransaction($transactionId);
    }

    protected function client()
    {
        return Http::withToken($this->apiKey)
            ->withHeaders(['Paddle-Version' => '1'])
            ->acceptJson()
            ->asJson();
    }

    protected function ensureConfigured(): void
    {
        if ($this->apiKey === '') {
            throw new \RuntimeException('Paddle API key is not configured.');
        }
    }

    protected function stringifyCustomData(array $customData): array
    {
        return collect($customData)
            ->map(fn ($value) => is_scalar($value) ? (string) $value : json_encode($value))
            ->all();
    }

    protected function extractErrorMessage(?array $json, string $fallback): string
    {
        return $json['error']['detail'] ?? $json['error']['code'] ?? $fallback;
    }
}
