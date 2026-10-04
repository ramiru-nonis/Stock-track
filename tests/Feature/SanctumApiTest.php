<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SanctumApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $staffUser;
    protected User $ownerUser;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ownerUser = User::factory()->create([
            'role' => 'owner',
            'is_active' => true,
        ]);

        $this->staffUser = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
        ]);

        $category = Category::create(['name' => 'Tech']);
        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Wireless Mouse',
            'sku' => 'MOUSE-01',
            'selling_price' => 2500.00,
            'quantity' => 20,
            'low_stock_threshold' => 5,
        ]);
    }

    public function test_unauthenticated_api_request_returns_401(): void
    {
        $this->getJson('/api/products')->assertStatus(401);
    }

    public function test_sanctum_token_issuance(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->staffUser->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token', 'user']);
    }

    public function test_authenticated_user_can_fetch_products_api(): void
    {
        $token = $this->staffUser->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)
                         ->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'sku', 'selling_price', 'selling_price_usd', 'quantity']
                     ]
                 ]);
    }

    public function test_stock_movement_api_records_stock_in(): void
    {
        $token = $this->staffUser->createToken('test-token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/stock-movements', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 10,
            'reason' => 'restock',
            'note' => 'API supplier restock',
        ]);

        $response->assertStatus(201);
        $this->assertEquals(30, $this->product->fresh()->quantity);
    }
}
