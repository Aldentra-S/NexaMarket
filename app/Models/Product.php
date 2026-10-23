<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    /**
     * Boot the model to handle deletion lifecycle for related images.
     */
    protected static function booted(): void
    {
        static::deleting(function (Product $product) {
            foreach ($product->images as $image) {
                $image->delete();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
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
     * Get the category that the product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all images for the product ordered by sort_order.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Get the primary image for the product.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get the thumbnail image (primary or first available).
     */
    public function thumbnail(): ?ProductImage
    {
        return $this->primaryImage ?: $this->images->first();
    }

    /**
     * Get the thumbnail URL with fallback to placeholder SVG.
     */
    public function getThumbnailUrlAttribute(): string
    {
        $thumb = $this->thumbnail();
        if ($thumb && $thumb->fileExists()) {
            return $thumb->url;
        }

        return asset('images/placeholder-product.svg');
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
