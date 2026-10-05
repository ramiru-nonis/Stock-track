<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class StockHistory extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    #[Url]
    public string $productId = '';

    #[Url]
    public string $userId = '';

    #[Url]
    public string $type = '';

    #[Url]
    public string $reason = '';

    #[Url]
    public string $fromDate = '';

    #[Url]
    public string $toDate = '';

    public function updatingProductId(): void { $this->resetPage(); }
    public function updatingUserId(): void { $this->resetPage(); }
    public function updatingType(): void { $this->resetPage(); }
    public function updatingReason(): void { $this->resetPage(); }
    public function updatingFromDate(): void { $this->resetPage(); }
    public function updatingToDate(): void { $this->resetPage(); }

    public function resetFilters(): void
    {
        $this->reset(['productId', 'userId', 'type', 'reason', 'fromDate', 'toDate']);
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        $this->authorize('viewHistory', StockMovement::class);

        $query = StockMovement::with(['product.category', 'user'])
            ->latest();

        if (!empty($this->productId)) {
            $query->forProduct($this->productId);
        }

        if (!empty($this->userId)) {
            $query->forUser($this->userId);
        }

        if (!empty($this->type)) {
            $query->ofType($this->type);
        }

        if (!empty($this->reason)) {
            $query->ofReason($this->reason);
        }

        if (!empty($this->fromDate) || !empty($this->toDate)) {
            $query->dateRange($this->fromDate, $this->toDate);
        }

        return view('livewire.stock-history', [
            'movements' => $query->paginate(15),
            'products' => Product::withTrashed()->orderBy('name')->get(),
            'users' => User::withTrashed()->orderBy('name')->get(),
        ]);
    }
}
