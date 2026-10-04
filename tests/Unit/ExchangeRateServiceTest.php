<?php

namespace Tests\Unit;

use App\Services\ExchangeRateService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    public function test_exchange_rate_api_success_is_handled_and_cached(): void
    {
        Cache::forget('exchange_rate_LKR_USD');

        Http::fake([
            'open.er-api.com/*' => Http::response([
                'result' => 'success',
                'rates' => [
                    'USD' => 0.0033,
                ],
            ], 200),
        ]);

        $service = new ExchangeRateService();
        $rate = $service->getRate('LKR', 'USD');

        $this->assertEquals(0.0033, $rate);
        $this->assertEquals(0.0033, Cache::get('exchange_rate_LKR_USD'));
    }

    public function test_exchange_rate_conversion_math(): void
    {
        Cache::forever('exchange_rate_LKR_USD', 0.0033);

        $service = new ExchangeRateService();
        $converted = $service->convert(1000.00, 'LKR', 'USD');

        $this->assertEquals(3.30, $converted);
    }

    public function test_exchange_rate_api_failure_fallback_does_not_crash(): void
    {
        Cache::forget('exchange_rate_LKR_USD');

        Http::fake([
            'open.er-api.com/*' => Http::response([], 500),
        ]);

        $service = new ExchangeRateService();
        $rate = $service->getRate('LKR', 'USD');

        // Should return standard fallback rate (0.0033) without crashing
        $this->assertNotNull($rate);
        $this->assertEquals(0.0033, $rate);
    }
}
