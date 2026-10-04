<?php

namespace App\Services;

use App\Events\StockMovementRecorded;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidStockOperationException;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Record a Stock In movement and update product quantity inside a DB transaction.
     *
     * @throws InvalidStockOperationException
     */
    public function stockIn(Product|int $product, User|int $user, int $quantity, string $reason, ?string $note = null): StockMovement
    {
        if ($quantity <= 0) {
            throw new InvalidStockOperationException("Quantity must be greater than zero.");
        }

        $productId = $product instanceof Product ? $product->id : $product;
        $userId = $user instanceof User ? $user->id : $user;

        return DB::transaction(function () use ($productId, $userId, $quantity, $reason, $note) {
            /** @var Product $lockedProduct */
            $lockedProduct = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

            $lockedProduct->quantity += $quantity;
            $lockedProduct->save();

            $movement = StockMovement::create([
                'product_id' => $lockedProduct->id,
                'user_id' => $userId,
                'type' => 'in',
                'reason' => $reason,
                'quantity' => $quantity,
                'note' => $note,
            ]);

            $userObj = User::find($userId);
            event(new StockMovementRecorded($movement, $lockedProduct, $userObj));

            return $movement;
        });
    }

    /**
     * Record a Stock Out movement and update product quantity inside a DB transaction.
     *
     * @throws InvalidStockOperationException
     * @throws InsufficientStockException
     */
    public function stockOut(Product|int $product, User|int $user, int $quantity, string $reason, ?string $note = null): StockMovement
    {
        if ($quantity <= 0) {
            throw new InvalidStockOperationException("Quantity must be greater than zero.");
        }

        $productId = $product instanceof Product ? $product->id : $product;
        $userId = $user instanceof User ? $user->id : $user;

        return DB::transaction(function () use ($productId, $userId, $quantity, $reason, $note) {
            /** @var Product $lockedProduct */
            $lockedProduct = Product::where('id', $productId)->lockForUpdate()->firstOrFail();

            if ($lockedProduct->quantity < $quantity) {
                throw new InsufficientStockException(
                    "Cannot perform stock out. Requested: {$quantity}, Available: {$lockedProduct->quantity}"
                );
            }

            $lockedProduct->quantity -= $quantity;
            $lockedProduct->save();

            $movement = StockMovement::create([
                'product_id' => $lockedProduct->id,
                'user_id' => $userId,
                'type' => 'out',
                'reason' => $reason,
                'quantity' => $quantity,
                'note' => $note,
            ]);

            $userObj = User::find($userId);
            event(new StockMovementRecorded($movement, $lockedProduct, $userObj));

            return $movement;
        });
    }
}
