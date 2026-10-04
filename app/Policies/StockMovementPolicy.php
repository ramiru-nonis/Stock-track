<?php

namespace App\Policies;

use App\Models\StockMovement;
use App\Models\User;

class StockMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function viewHistory(User $user): bool
    {
        return $user->is_active && $user->isOwner();
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }
}
