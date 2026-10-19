<?php

namespace App\Models;

use App\Enums\ShopStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ShopStatus::PENDING,
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
}
