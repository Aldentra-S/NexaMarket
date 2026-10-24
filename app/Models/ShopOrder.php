<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'shop_id',
        'shop_name',
        'order_number',
        'status',
        'subtotal',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => OrderStatus::PENDING,
        'subtotal' => 0.00,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Get the parent order that owns this shop order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the shop related to this child order.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get all items belonging to this shop order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Format the shop order subtotal into Indonesian Rupiah currency display.
     */
    public function formattedSubtotal(): string
    {
        return 'Rp ' . number_format((float) $this->subtotal, 0, ',', '.');
    }

    /**
     * Calculate total items quantity in this shop order.
     */
    public function totalQuantity(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
