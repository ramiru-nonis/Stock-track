<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'selling_price',
        'quantity',
        'low_stock_threshold',
        'image_data',
        'image_mime',
        'cloudinary_url',
        'cloudinary_public_id',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'quantity' => 'integer',
            'low_stock_threshold' => 'integer',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!empty($this->cloudinary_url)) {
            return $this->cloudinary_url;
        }

        if (!empty($this->image_data)) {
            return route('products.image', $this->id);
        }

        return null;
    }

    public function getBase64DataUriAttribute(): ?string
    {
        if (empty($this->image_data)) {
            return null;
        }

        $mime = $this->image_mime ?? 'image/jpeg';
        return "data:{$mime};base64,{$this->image_data}";
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('quantity', '<=', 'low_stock_threshold');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function (Builder $sub) use ($term) {
            $sub->where('name', 'like', '%' . $term . '%')
                ->orWhere('sku', 'like', '%' . $term . '%')
                ->orWhereHas('category', function (Builder $cat) use ($term) {
                    $cat->where('name', 'like', '%' . $term . '%');
                });
        });
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->low_stock_threshold;
    }
}
