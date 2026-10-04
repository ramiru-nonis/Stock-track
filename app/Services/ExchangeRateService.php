<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExchangeRateService
{
    protected string $apiKey;
    protected string $baseCurrency;
    protected string $targetCurrency;

    public function __construct()
    {
        $this->apiKey = (string) config('services.exchangerate.api_key', '');
        $this->baseCurrency = (string) config('services.exchangerate.base_currency', 'LKR');
        $this->targetCurrency = (string) config('services.exchangerate.target_currency', 'USD');
    }

    /**
     * Get exchange rate from base currency to target currency.
     * Caches the rate for 6 hours.
     */
    public function getRate(string $base = 'LKR', string $target = 'USD'): ?float
    {
        $cacheKey = "exchange_rate_{$base}_{$target}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($base, $target) {
            return $this->fetchRateFromApi($base, $target);
        });
    }

    /**
     * Convert an amount in base currency to target currency.
     */
    public function convert(float $amount, string $base = 'LKR', string $target = 'USD'): ?float
    {
        $rate = $this->getRate($base, $target);

        if ($rate === null || $rate <= 0) {
            return null;
        }

        return round($amount * $rate, 2);
    }

    /**
     * Force refresh the cached rate.
     */
    public function refreshRate(string $base = 'LKR', string $target = 'USD'): ?float
    {
        $cacheKey = "exchange_rate_{$base}_{$target}";
        Cache::forget($cacheKey);

        return $this->getRate($base, $target);
    }

    /**
     * Fetch rate from third party ExchangeRate-API with timeouts and retry mechanism.
     */
    protected function fetchRateFromApi(string $base, string $target): ?float
    {
        try {
            $url = !empty($this->apiKey)
                ? "https://v6.exchangerate-api.com/v6/{$this->apiKey}/latest/{$base}"
                : "https://open.er-api.com/v6/latest/{$base}";

            $response = Http::timeout(5)
                ->retry(2, 100)
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['rates'][$target])) {
                    return (float) $data['rates'][$target];
                }

                if (isset($data['conversion_rates'][$target])) {
                    return (float) $data['conversion_rates'][$target];
                }
            }

            Log::warning('ExchangeRate-API request returned unsuccessful status', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to fetch rate from ExchangeRate-API: ' . $e->getMessage());
        }

        // Return a fallback static rate if LKR -> USD if network fails completely (e.g. 1 LKR = ~0.0033 USD)
        if ($base === 'LKR' && $target === 'USD') {
            return 0.0033;
        }

        return null;
    }
}
