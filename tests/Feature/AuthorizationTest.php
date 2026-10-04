<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $staff;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create([
            'role' => 'owner',
            'is_active' => true,
        ]);

        $this->staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Description',
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'selling_price' => 100.00,
            'quantity' => 10,
            'low_stock_threshold' => 5,
        ]);
    }

    public function test_guest_cannot_access_dashboard_or_inventory(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/products')->assertRedirect('/login');
        $this->get('/categories')->assertRedirect('/login');
        $this->get('/stock-history')->assertRedirect('/login');
        $this->get('/staff')->assertRedirect('/login');
    }

    public function test_staff_cannot_access_owner_only_routes(): void
    {
        $this->actingAs($this->staff);

        $this->get('/stock-history')->assertStatus(403);
        $this->get('/staff')->assertStatus(403);
    }

    public function test_owner_can_access_owner_only_routes(): void
    {
        $this->actingAs($this->owner);

        $this->get('/stock-history')->assertStatus(200);
        $this->get('/staff')->assertStatus(200);
    }

    public function test_staff_cannot_create_update_or_delete_product(): void
    {
        $this->actingAs($this->staff);

        $this->assertFalse($this->staff->can('create', Product::class));
        $this->assertFalse($this->staff->can('update', $this->product));
        $this->assertFalse($this->staff->can('delete', $this->product));
    }

    public function test_owner_can_create_update_and_delete_product(): void
    {
        $this->actingAs($this->owner);

        $this->assertTrue($this->owner->can('create', Product::class));
        $this->assertTrue($this->owner->can('update', $this->product));
        $this->assertTrue($this->owner->can('delete', $this->product));
    }

    public function test_staff_cannot_manage_categories(): void
    {
        $this->actingAs($this->staff);

        $this->assertFalse($this->staff->can('create', Category::class));
        $this->assertFalse($this->staff->can('update', $this->category));
        $this->assertFalse($this->staff->can('delete', $this->category));
    }
}
