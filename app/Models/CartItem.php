<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    /**
     * Get the cart that owns the item.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product associated with the cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate item subtotal using current product price.
     */
    public function subtotal(): float
    {
        $price = (string) ($this->product?->price ?? '0.00');
        $quantity = (string) max(0, $this->quantity);

        if (function_exists('bcmul')) {
            return (float) bcmul($price, $quantity, 2);
        }

        return (float) round((float) $price * (int) $quantity, 2);
    }

    /**
     * Check if product and shop are active and sufficient stock is available.
     */
    public function isAvailable(): bool
    {
        if (! $this->product) {
            return false;
        }

        $isPublished = $this->product->status === ProductStatus::PUBLISHED;
        $shop = $this->product->shop;
        $isShopActive = $shop && $shop->status === ShopStatus::APPROVED && ! $shop->is_suspended;
        $hasStock = $this->product->stock >= $this->quantity;

        return $isPublished && $isShopActive && $hasStock;
    }
}
