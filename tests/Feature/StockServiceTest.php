<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidStockOperationException;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockService $stockService;
    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockService = app(StockService::class);
        $this->user = User::factory()->create(['role' => 'staff']);

        $category = Category::create(['name' => 'General']);
        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sample Widget',
            'sku' => 'WIDGET-001',
            'selling_price' => 500.00,
            'quantity' => 10,
            'low_stock_threshold' => 3,
        ]);
    }

    public function test_stock_in_increases_quantity_and_creates_movement(): void
    {
        $movement = $this->stockService->stockIn(
            $this->product,
            $this->user,
            5,
            'restock',
            'Added 5 units'
        );

        $this->assertEquals(15, $this->product->fresh()->quantity);
        $this->assertInstanceOf(StockMovement::class, $movement);
        $this->assertEquals('in', $movement->type);
        $this->assertEquals('restock', $movement->reason);
        $this->assertEquals(5, $movement->quantity);
        $this->assertEquals($this->user->id, $movement->user_id);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'type' => 'in',
            'quantity' => 5,
        ]);
    }

    public function test_stock_out_decreases_quantity_and_creates_movement(): void
    {
        $movement = $this->stockService->stockOut(
            $this->product,
            $this->user,
            4,
            'sale',
            'Customer purchase'
        );

        $this->assertEquals(6, $this->product->fresh()->quantity);
        $this->assertEquals('out', $movement->type);
        $this->assertEquals(4, $movement->quantity);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'type' => 'out',
            'quantity' => 4,
        ]);
    }

    public function test_cannot_stock_out_more_than_available_quantity(): void
    {
        $this->expectException(InsufficientStockException::class);

        $this->stockService->stockOut(
            $this->product,
            $this->user,
            100, // Available is only 10
            'sale'
        );
    }

    public function test_invalid_quantity_throws_exception(): void
    {
        $this->expectException(InvalidStockOperationException::class);

        $this->stockService->stockIn(
            $this->product,
            $this->user,
            0,
            'adjustment'
        );
    }

    public function test_stock_quantity_does_not_become_negative(): void
    {
        try {
            $this->stockService->stockOut($this->product, $this->user, 20, 'sale');
        } catch (InsufficientStockException $e) {
            // Caught as expected
        }

        $this->assertEquals(10, $this->product->fresh()->quantity);
    }
}
