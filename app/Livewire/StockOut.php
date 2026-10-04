<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\StockService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StockOut extends Component
{
    public ?int $product_id = null;
    public int $quantity = 1;
    public string $reason = 'sale';
    public string $note = '';

    protected array $rules = [
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'reason' => 'required|string|in:sale,damage,adjustment',
        'note' => 'nullable|string|max:1000',
    ];

    public function recordStockOut(StockService $stockService): void
    {
        $this->validate();

        try {
            $product = Product::findOrFail($this->product_id);
            $stockService->stockOut(
                $product,
                Auth::user(),
                $this->quantity,
                $this->reason,
                $this->note ?: null
            );

            session()->flash('message', "Successfully dispatched {$this->quantity} unit(s) of {$product->name}. Remaining Stock: {$product->fresh()->quantity}");
            $this->reset(['product_id', 'quantity', 'note']);
            $this->quantity = 1;
            $this->reason = 'sale';
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(): View
    {
        $selectedProduct = $this->product_id ? Product::find($this->product_id) : null;
        $products = Product::with('category')->orderBy('name')->get();

        return view('livewire.stock-out', [
            'products' => $products,
            'selectedProduct' => $selectedProduct,
        ])->layout('layouts.app');
    }
}
