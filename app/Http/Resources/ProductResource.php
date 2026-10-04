<?php

namespace App\Http\Resources;

use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var ExchangeRateService $exchangeService */
        $exchangeService = app(ExchangeRateService::class);
        $usdPrice = $exchangeService->convert((float) $this->selling_price, 'LKR', 'USD');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'selling_price' => (float) $this->selling_price,
            'base_currency' => config('services.exchangerate.base_currency', 'LKR'),
            'selling_price_usd' => $usdPrice,
            'quantity' => $this->quantity,
            'low_stock_threshold' => $this->low_stock_threshold,
            'is_low_stock' => $this->isLowStock(),
            'has_image' => !empty($this->image_data),
            'image_url' => $this->image_url,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
