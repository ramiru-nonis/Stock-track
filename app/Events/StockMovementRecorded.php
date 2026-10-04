<?php

namespace App\Events;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockMovementRecorded
{
    use Dispatchable, SerializesModels;

    public StockMovement $movement;
    public Product $product;
    public ?User $user;

    public function __construct(StockMovement $movement, Product $product, ?User $user = null)
    {
        $this->movement = $movement;
        $this->product = $product;
        $this->user = $user;
    }
}
