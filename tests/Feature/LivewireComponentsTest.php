<?php

namespace Tests\Feature;

use App\Livewire\StaffManagement;
use App\Livewire\StockHistory;
use App\Livewire\StockIn;
use App\Livewire\StockOut;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireComponentsTest extends TestCase
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
            'name' => 'Owner User',
            'email' => 'owner@example.com',
            'role' => 'owner',
            'is_active' => true,
        ]);

        $this->staff = User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Electronics',
            'description' => 'Gadgets and hardware',
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Test Laptop',
            'sku' => 'LAPTOP-01',
            'selling_price' => 1200.00,
            'quantity' => 20,
            'low_stock_threshold' => 5,
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /* StaffManagement Tests                                                      */
    /* -------------------------------------------------------------------------- */

    public function test_owner_can_render_staff_management(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(StaffManagement::class)
            ->assertStatus(200)
            ->assertSee('Staff User');
    }

    public function test_staff_cannot_render_staff_management(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(StaffManagement::class)
            ->assertForbidden();
    }

    public function test_owner_can_create_staff_account(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(StaffManagement::class)
            ->call('openCreateModal')
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('createStaff')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'staff',
            'is_active' => true,
        ]);
    }

    public function test_owner_can_toggle_staff_active_status(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(StaffManagement::class)
            ->call('toggleUserStatus', $this->staff->id);

        $this->assertFalse($this->staff->fresh()->is_active);
    }

    public function test_owner_cannot_deactivate_another_owner(): void
    {
        $anotherOwner = User::factory()->create([
            'role' => 'owner',
            'is_active' => true,
        ]);

        $this->actingAs($this->owner);

        Livewire::test(StaffManagement::class)
            ->call('confirmDeactivate', $anotherOwner->id)
            ->assertSee('Cannot deactivate an owner account.');
    }

    /* -------------------------------------------------------------------------- */
    /* StockHistory Tests                                                         */
    /* -------------------------------------------------------------------------- */

    public function test_owner_can_view_stock_history_and_filter(): void
    {
        $this->actingAs($this->owner);

        StockMovement::create([
            'product_id' => $this->product->id,
            'user_id' => $this->staff->id,
            'type' => 'in',
            'reason' => 'restock',
            'quantity' => 10,
            'note' => 'Initial shipment',
        ]);

        Livewire::test(StockHistory::class)
            ->assertStatus(200)
            ->assertSee('Test Laptop')
            ->set('productId', (string)$this->product->id)
            ->assertSee('Initial shipment')
            ->call('resetFilters')
            ->assertSet('productId', '');
    }

    public function test_staff_cannot_view_stock_history(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(StockHistory::class)
            ->assertForbidden();
    }

    /* -------------------------------------------------------------------------- */
    /* StockIn Tests                                                              */
    /* -------------------------------------------------------------------------- */

    public function test_staff_can_record_stock_in(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(StockIn::class)
            ->set('product_id', $this->product->id)
            ->set('quantity', 5)
            ->set('reason', 'restock')
            ->set('note', 'Weekly restock')
            ->call('recordStockIn')
            ->assertHasNoErrors()
            ->assertSee('Successfully added 5 unit(s) to Test Laptop');

        $this->assertEquals(25, $this->product->fresh()->quantity);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'user_id' => $this->staff->id,
            'type' => 'in',
            'quantity' => 5,
            'reason' => 'restock',
            'note' => 'Weekly restock',
        ]);
    }

    public function test_stock_in_preselects_product_id_from_mount(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(StockIn::class, ['product_id' => $this->product->id])
            ->assertSet('product_id', $this->product->id);
    }

    /* -------------------------------------------------------------------------- */
    /* StockOut Tests                                                             */
    /* -------------------------------------------------------------------------- */

    public function test_staff_can_record_stock_out(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(StockOut::class)
            ->set('product_id', $this->product->id)
            ->set('quantity', 8)
            ->set('reason', 'sale')
            ->set('note', 'Customer sale')
            ->call('recordStockOut')
            ->assertHasNoErrors()
            ->assertSee('Successfully dispatched 8 unit(s) of Test Laptop');

        $this->assertEquals(12, $this->product->fresh()->quantity);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'user_id' => $this->staff->id,
            'type' => 'out',
            'quantity' => 8,
            'reason' => 'sale',
            'note' => 'Customer sale',
        ]);
    }

    public function test_stock_out_fails_when_exceeding_stock(): void
    {
        $this->actingAs($this->staff);

        Livewire::test(StockOut::class)
            ->set('product_id', $this->product->id)
            ->set('quantity', 999)
            ->set('reason', 'sale')
            ->call('recordStockOut')
            ->assertSee('Cannot perform stock out');

        $this->assertEquals(20, $this->product->fresh()->quantity);
    }
}
