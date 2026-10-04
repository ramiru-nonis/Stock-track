<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\StockService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StockIn extends Component
{
    public ?int $product_id = null;
    public int $quantity = 1;
    public string $reason = 'restock';
    public string $note = '';

    protected array $rules = [
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'reason' => 'required|string|in:restock,adjustment',
        'note' => 'nullable|string|max:1000',
    ];

    public function recordStockIn(StockService $stockService): void
    {
        $this->validate();

        try {
            $product = Product::findOrFail($this->product_id);
            $stockService->stockIn(
                $product,
                Auth::user(),
                $this->quantity,
                $this->reason,
                $this->note ?: null
            );

            session()->flash('message', "Successfully added {$this->quantity} unit(s) to {$product->name}. New Stock: {$product->fresh()->quantity}");
            $this->reset(['product_id', 'quantity', 'note']);
            $this->quantity = 1;
            $this->reason = 'restock';
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(): View
    {
        $selectedProduct = $this->product_id ? Product::find($this->product_id) : null;
        $products = Product::with('category')->orderBy('name')->get();

        return view('livewire.stock-in', [
            'products' => $products,
            'selectedProduct' => $selectedProduct,
        ])->layout('layouts.app');
    }
}
