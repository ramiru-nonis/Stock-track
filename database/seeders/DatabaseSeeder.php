<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Owner User
        $owner = User::firstOrCreate(
            ['email' => 'owner@shop.com'],
            [
                'name' => 'Shop Owner',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'is_active' => true,
            ]
        );

        // 2. Create Staff User
        $staff = User::firstOrCreate(
            ['email' => 'staff@shop.com'],
            [
                'name' => 'Store Assistant',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'is_active' => true,
            ]
        );

        // 3. Create Categories
        $electronics = Category::firstOrCreate(['name' => 'Electronics'], ['description' => 'Gadgets, appliances, and accessories']);
        $beverages = Category::firstOrCreate(['name' => 'Beverages'], ['description' => 'Cold drinks, tea, coffee, and juices']);
        $groceries = Category::firstOrCreate(['name' => 'Groceries'], ['description' => 'Daily essential food items and pantry goods']);
        $stationery = Category::firstOrCreate(['name' => 'Stationery'], ['description' => 'Pens, notebooks, and office supplies']);

        // 4. Create Sample Products
        $productsData = [
            [
                'category_id' => $electronics->id,
                'name' => 'Wireless Bluetooth Speaker',
                'sku' => 'ELEC-BTS-001',
                'description' => 'Portable 10W waterproof speaker',
                'selling_price' => 8500.00,
                'quantity' => 15,
                'low_stock_threshold' => 5,
            ],
            [
                'category_id' => $electronics->id,
                'name' => 'USB-C Charging Cable (2m)',
                'sku' => 'ELEC-USBC-002',
                'description' => 'Fast charging braided cable',
                'selling_price' => 1200.00,
                'quantity' => 3, // Low Stock!
                'low_stock_threshold' => 10,
            ],
            [
                'category_id' => $beverages->id,
                'name' => 'Ceylon Black Tea (250g)',
                'sku' => 'BEV-TEA-001',
                'description' => 'Premium BOPF loose leaf tea',
                'selling_price' => 950.00,
                'quantity' => 50,
                'low_stock_threshold' => 10,
            ],
            [
                'category_id' => $beverages->id,
                'name' => 'Organic Fresh Orange Juice (1L)',
                'sku' => 'BEV-JUICE-002',
                'description' => '100% natural cold pressed juice',
                'selling_price' => 750.00,
                'quantity' => 2, // Low Stock!
                'low_stock_threshold' => 5,
            ],
            [
                'category_id' => $groceries->id,
                'name' => 'Basmati Rice (5kg)',
                'sku' => 'GROC-RICE-001',
                'description' => 'Long grain fragrant rice',
                'selling_price' => 3200.00,
                'quantity' => 20,
                'low_stock_threshold' => 8,
            ],
            [
                'category_id' => $stationery->id,
                'name' => 'A4 Hardcover Notebook 200p',
                'sku' => 'STAT-NOTE-001',
                'description' => 'Ruled memo notebook',
                'selling_price' => 450.00,
                'quantity' => 40,
                'low_stock_threshold' => 10,
            ],
        ];

        foreach ($productsData as $data) {
            $product = Product::firstOrCreate(['sku' => $data['sku']], $data);

            // Create initial stock movements for audit ledger history
            StockMovement::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'reason' => 'initial_seed',
                ],
                [
                    'user_id' => $owner->id,
                    'type' => 'in',
                    'quantity' => $product->quantity,
                    'note' => 'Initial stock seed upon store initialization.',
                ]
            );
        }

        // Add a sample Stock Out movement by Staff
        $teaProduct = Product::where('sku', 'BEV-TEA-001')->first();
        if ($teaProduct) {
            StockMovement::create([
                'product_id' => $teaProduct->id,
                'user_id' => $staff->id,
                'type' => 'out',
                'reason' => 'sale',
                'quantity' => 5,
                'note' => 'Counter sale cash receipt #1004',
            ]);
        }
    }
}
