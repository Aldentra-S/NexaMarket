<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'shop_order_id',
        'product_id',
        'product_name',
        'product_slug',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    /**
     * Get the child shop order that owns this item.
     */
    public function shopOrder(): BelongsTo
    {
        return $this->belongsTo(ShopOrder::class);
    }

    /**
     * Get the product referenced by this item (nullable if product was deleted).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Format unit price into Indonesian Rupiah currency display.
     */
    public function formattedUnitPrice(): string
    {
        return 'Rp ' . number_format((float) $this->unit_price, 0, ',', '.');
    }

    /**
     * Format subtotal into Indonesian Rupiah currency display.
     */
    public function formattedSubtotal(): string
    {
        return 'Rp ' . number_format((float) $this->subtotal, 0, ',', '.');
    }
}
