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

    public const COURIERS = [
        'JNE' => 'JNE',
        'J&T Express' => 'J&T Express',
        'SiCepat' => 'SiCepat',
        'AnterAja' => 'AnterAja',
        'Pos Indonesia' => 'Pos Indonesia',
        'Ninja Xpress' => 'Ninja Xpress',
        'ID Express' => 'ID Express',
        'Lainnya' => 'Lainnya',
    ];

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
        'shipping_cost',
        'courier',
        'tracking_number',
        'shipped_at',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => OrderStatus::PENDING,
        'subtotal' => 0.00,
        'shipping_cost' => 0.00,
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
            'shipping_cost' => 'decimal:2',
            'shipped_at' => 'datetime',
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
     * Calculate total shop order amount including shipping cost.
     */
    public function total(): float
    {
        return (float) $this->subtotal + (float) $this->shipping_cost;
    }

    /**
     * Format the total shop order amount into Indonesian Rupiah currency display.
     */
    public function formattedTotal(): string
    {
        return 'Rp ' . number_format($this->total(), 0, ',', '.');
    }

    /**
     * Format shop shipping cost into Indonesian Rupiah currency display.
     */
    public function formattedShippingCost(): string
    {
        $cost = (float) $this->shipping_cost;
        if ($cost <= 0) {
            return 'Gratis Ongkir';
        }

        return 'Rp ' . number_format($cost, 0, ',', '.');
    }

    /**
     * Calculate total items quantity in this shop order.
     */
    public function totalQuantity(): int
    {
        return (int) $this->items->sum('quantity');
    }

    /**
     * Check if this shop order has been shipped with courier and tracking details.
     */
    public function isShipped(): bool
    {
        return in_array($this->status, [OrderStatus::SHIPPED, OrderStatus::COMPLETED], true);
    }
}
