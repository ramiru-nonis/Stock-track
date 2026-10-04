<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(): View
    {
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return [
                'total_products' => Product::count(),
                'low_stock_count' => Product::lowStock()->count(),
                'total_categories' => Category::count(),
                'recent_movements_count' => StockMovement::where('created_at', '>=', now()->subDays(7))->count(),
            ];
        });

        $recentMovements = StockMovement::with(['product', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.dashboard', [
            'stats' => $stats,
            'recentMovements' => $recentMovements,
        ])->layout('layouts.app');
    }
}
