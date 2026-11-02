<?php

namespace App\Models;

use App\Enums\ShopStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'phone',
        'address',
        'shipping_cost',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ShopStatus::PENDING,
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
            'status' => ShopStatus::class,
            'shipping_cost' => 'decimal:2',
            'approved_at' => 'datetime',
            'suspended_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the shop.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === ShopStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === ShopStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === ShopStatus::REJECTED;
    }

    public function isSuspended(): bool
    {
        return $this->status === ShopStatus::SUSPENDED;
    }

    /**
     * Get the products for the shop.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all child orders received by this shop.
     */
    public function shopOrders(): HasMany
    {
        return $this->hasMany(ShopOrder::class);
    }

    /**
     * Format shop shipping rate into Indonesian Rupiah currency display.
     */
    public function formattedShippingCost(): string
    {
        $cost = (float) $this->shipping_cost;
        if ($cost <= 0) {
            return 'Gratis Ongkir';
        }

        return 'Rp ' . number_format($cost, 0, ',', '.');
    }
}

