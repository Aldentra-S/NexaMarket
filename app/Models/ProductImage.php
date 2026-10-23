<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * Maximum allowed images per product.
     */
    public const MAX_PER_PRODUCT = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'file_path',
        'is_primary',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Boot the model. Clean up physical files when an image is deleted.
     */
    protected static function booted(): void
    {
        static::deleted(function (ProductImage $image) {
            $image->deleteFileIfUnreferenced();
        });
    }

    /**
     * Get the product that owns this image.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the public URL for the image.
     */
    public function getUrlAttribute(): string
    {
        if (Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->url($this->file_path);
        }

        return asset('images/placeholder-product.svg');
    }

    /**
     * Check if the image file exists in storage.
     */
    public function fileExists(): bool
    {
        return Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Delete the image file from public disk if no other record references it.
     */
    public function deleteFileIfUnreferenced(): void
    {
        if (empty($this->file_path)) {
            return;
        }

        $referencedElsewhere = static::where('file_path', $this->file_path)
            ->where('id', '!=', $this->id)
            ->exists();

        if (! $referencedElsewhere && Storage::disk('public')->exists($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
        }
    }
}
