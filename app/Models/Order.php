<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_amount',
        'idempotency_key',
        'notes',
        'paid_at',
        'cancelled_at',
        'shipping_recipient_name',
        'shipping_phone',
        'shipping_province',
        'shipping_city',
        'shipping_district',
        'shipping_postal_code',
        'shipping_address',
        'shipping_delivery_notes',
        'shipping_cost_total',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => OrderStatus::PENDING,
        'total_amount' => 0.00,
        'shipping_cost_total' => 0.00,
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
            'total_amount' => 'decimal:2',
            'shipping_cost_total' => 'decimal:2',
            'paid_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Get the buyer that placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the multi-vendor child shop orders under this parent order.
     */
    public function shopOrders(): HasMany
    {
        return $this->hasMany(ShopOrder::class);
    }

    /**
     * Get all order items across all shop orders in this parent order.
     */
    public function items(): HasManyThrough
    {
        return $this->hasManyThrough(OrderItem::class, ShopOrder::class);
    }

    /**
     * Format the total order amount into Indonesian Rupiah currency display.
     */
    public function formattedTotal(): string
    {
        return 'Rp ' . number_format((float) $this->total_amount, 0, ',', '.');
    }

    /**
     * Format the total shipping cost into Indonesian Rupiah currency display.
     */
    public function formattedShippingCost(): string
    {
        $cost = (float) $this->shipping_cost_total;
        if ($cost <= 0) {
            return 'Gratis Ongkir';
        }

        return 'Rp ' . number_format($cost, 0, ',', '.');
    }

    /**
     * Calculate sum of item subtotals excluding shipping cost.
     */
    public function itemsSubtotal(): float
    {
        return (float) $this->shopOrders->sum('subtotal');
    }

    /**
     * Format items subtotal into Indonesian Rupiah currency display.
     */
    public function formattedItemsSubtotal(): string
    {
        return 'Rp ' . number_format($this->itemsSubtotal(), 0, ',', '.');
    }

    /**
     * Check if this order contains a saved shipping address snapshot.
     */
    public function hasShippingAddress(): bool
    {
        return ! empty($this->shipping_address);
    }

    /**
     * Get a formatted multiline shipping address string.
     */
    public function formattedShippingAddress(): string
    {
        $parts = [
            $this->shipping_address,
            $this->shipping_district ? 'Kec. ' . $this->shipping_district : null,
            $this->shipping_city,
            $this->shipping_province,
            $this->shipping_postal_code,
        ];

        return implode(', ', array_filter($parts));
    }

    /**
     * Calculate total items quantity across all child shop orders.
     */
    public function totalQuantity(): int
    {
        return (int) $this->shopOrders->sum(function (ShopOrder $shopOrder) {
            return $shopOrder->items->sum('quantity');
        });
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatus::PENDING;
    }

    public function isPaid(): bool
    {
        return $this->status === OrderStatus::PAID;
    }

    public function isCancelled(): bool
    {
        return $this->status === OrderStatus::CANCELLED;
    }

    public function isExpired(): bool
    {
        return $this->status === OrderStatus::EXPIRED;
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::COMPLETED;
    }

    /**
     * Get all payment transaction attempts for this order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the latest payment record for this order.
     */
    public function latestPayment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }
}

