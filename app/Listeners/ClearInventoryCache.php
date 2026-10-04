<?php

namespace App\Listeners;

use App\Events\StockMovementRecorded;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearInventoryCache
{
    /**
     * Handle the event.
     */
    public function handle(StockMovementRecorded $event): void
    {
        // Invalidate cached dashboard metrics
        Cache::forget('dashboard_stats');
        Cache::forget('low_stock_count');
        Cache::forget('recent_movements');

        Log::info('Stock movement recorded & inventory cache cleared.', [
            'movement_id' => $event->movement->id,
            'product_id' => $event->product->id,
            'product_name' => $event->product->name,
            'type' => $event->movement->type,
            'quantity' => $event->movement->quantity,
            'user_id' => $event->user?->id,
        ]);
    }
}
