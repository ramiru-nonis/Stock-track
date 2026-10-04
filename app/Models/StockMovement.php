<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'reason',
        'quantity',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        if (empty($type)) {
            return $query;
        }
        return $query->where('type', $type);
    }

    public function scopeOfReason(Builder $query, ?string $reason): Builder
    {
        if (empty($reason)) {
            return $query;
        }
        return $query->where('reason', $reason);
    }

    public function scopeForProduct(Builder $query, mixed $productId): Builder
    {
        if (empty($productId)) {
            return $query;
        }
        return $query->where('product_id', $productId);
    }

    public function scopeForUser(Builder $query, mixed $userId): Builder
    {
        if (empty($userId)) {
            return $query;
        }
        return $query->where('user_id', $userId);
    }

    public function scopeDateRange(Builder $query, ?string $fromDate, ?string $toDate): Builder
    {
        if (!empty($fromDate)) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('created_at', '<=', $toDate);
        }
        return $query;
    }
}
