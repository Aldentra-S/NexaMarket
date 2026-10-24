<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items in the cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculate total items quantity in the cart.
     */
    public function totalQuantity(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    /**
     * Calculate subtotal of all items in the cart based on current product prices.
     */
    public function subtotal(): float
    {
        return (float) $this->items->sum(function (CartItem $item) {
            return $item->subtotal();
        });
    }
}
