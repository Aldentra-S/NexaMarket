<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
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
        'price',
        'stock',
        'status',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ProductStatus::DRAFT,
        'stock' => 0,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProductStatus::class,
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    /**
     * Get the shop that owns the product.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Scope a query to only include published products.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ProductStatus::PUBLISHED);
    }

    /**
     * Scope a query to only include products from approved shops.
     */
    public function scopeForActiveShop(Builder $query): Builder
    {
        return $query->whereHas('shop', function (Builder $shopQuery) {
            $shopQuery->where('status', ShopStatus::APPROVED);
        });
    }

    public function isPublished(): bool
    {
        return $this->status === ProductStatus::PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === ProductStatus::DRAFT;
    }

    public function isArchived(): bool
    {
        return $this->status === ProductStatus::ARCHIVED;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function formattedPrice(): string
    {
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
    }
}
