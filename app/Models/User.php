<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'firebase_uid', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => UserRole::BUYER,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function isBuyer(): bool
    {
        return $this->role === UserRole::BUYER;
    }

    /**
     * Check if user is a seller in business terms (owns a registered shop).
     */
    public function isSeller(): bool
    {
        return $this->hasShop();
    }

    /**
     * Check if user owns any registered shop.
     */
    public function hasShop(): bool
    {
        return $this->shop !== null;
    }

    /**
     * Check if user owns an approved shop.
     */
    public function hasApprovedShop(): bool
    {
        return $this->shop !== null && $this->shop->isApproved();
    }

    /**
     * Check if user is eligible to access Seller Center operations.
     */
    public function canAccessSellerCenter(): bool
    {
        return $this->isBuyer() && $this->hasApprovedShop();
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function hasRole(UserRole|string $role): bool
    {
        $roleValue = $role instanceof UserRole ? $role->value : $role;
        $currentValue = $this->role instanceof UserRole ? $this->role->value : (string) $this->role;

        return $currentValue === $roleValue;
    }

    public function shop(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Shop::class);
    }

    public function cart(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function getOrCreateCart(): Cart
    {
        return $this->cart()->firstOrCreate([]);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }
}


