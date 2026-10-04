<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LowStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_scope_identifies_items_at_or_below_threshold(): void
    {
        $category = Category::create(['name' => 'General']);

        // Normal Stock item (quantity 10, threshold 5)
        $normalProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'Healthy Stock Product',
            'sku' => 'NORMAL-01',
            'selling_price' => 100,
            'quantity' => 10,
            'low_stock_threshold' => 5,
        ]);

        // Low Stock item (quantity 4, threshold 5)
        $lowProduct1 = Product::create([
            'category_id' => $category->id,
            'name' => 'Low Stock Item 1',
            'sku' => 'LOW-01',
            'selling_price' => 100,
            'quantity' => 4,
            'low_stock_threshold' => 5,
        ]);

        // Low Stock item exact threshold match (quantity 5, threshold 5)
        $lowProduct2 = Product::create([
            'category_id' => $category->id,
            'name' => 'Low Stock Item 2',
            'sku' => 'LOW-02',
            'selling_price' => 100,
            'quantity' => 5,
            'low_stock_threshold' => 5,
        ]);

        $lowStockProducts = Product::lowStock()->get();

        $this->assertCount(2, $lowStockProducts);
        $this->assertTrue($lowStockProducts->contains($lowProduct1));
        $this->assertTrue($lowStockProducts->contains($lowProduct2));
        $this->assertFalse($lowStockProducts->contains($normalProduct));
    }
}
