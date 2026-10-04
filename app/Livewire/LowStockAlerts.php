<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class LowStockAlerts extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $query = Product::with('category')->lowStock();

        if (!empty($this->search)) {
            $query->search($this->search);
        }

        return view('livewire.low-stock-alerts', [
            'products' => $query->paginate(10),
        ])->layout('layouts.app');
    }
}
