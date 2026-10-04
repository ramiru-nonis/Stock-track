<?php

namespace App\Jobs;

use App\Services\ExchangeRateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RefreshExchangeRate implements ShouldQueue
{
    use Queueable;

    public string $base;
    public string $target;

    /**
     * Create a new job instance.
     */
    public function __construct(string $base = 'LKR', string $target = 'USD')
    {
        $this->base = $base;
        $this->target = $target;
    }

    /**
     * Execute the job.
     */
    public function handle(ExchangeRateService $exchangeRateService): void
    {
        $rate = $exchangeRateService->refreshRate($this->base, $this->target);
        Log::info("Queued job RefreshExchangeRate executed. Rate for {$this->base}->{$this->target}: " . ($rate ?? 'N/A'));
    }
}
