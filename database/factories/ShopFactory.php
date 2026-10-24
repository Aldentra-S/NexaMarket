<?php

namespace Database\Factories;

use App\Enums\ShopStatus;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Shop>
 */
class ShopFactory extends Factory
{
    protected $model = Shop::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company() . ' Shop';

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'status' => ShopStatus::PENDING,
            'rejection_reason' => null,
            'suspension_reason' => null,
            'approved_at' => null,
            'suspended_at' => null,
        ];
    }

    /**
     * Indicate that the shop is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ShopStatus::PENDING,
            'approved_at' => null,
            'rejection_reason' => null,
            'suspension_reason' => null,
            'suspended_at' => null,
        ]);
    }

    /**
     * Indicate that the shop is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ShopStatus::APPROVED,
            'approved_at' => now(),
            'rejection_reason' => null,
            'suspension_reason' => null,
            'suspended_at' => null,
        ]);
    }

    /**
     * Indicate that the shop is rejected.
     */
    public function rejected(?string $reason = 'Persyaratan informasi toko tidak lengkap.'): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ShopStatus::REJECTED,
            'rejection_reason' => $reason,
            'approved_at' => null,
            'suspension_reason' => null,
            'suspended_at' => null,
        ]);
    }

    /**
     * Indicate that the shop is suspended.
     */
    public function suspended(?string $reason = 'Toko melanggar syarat dan ketentuan operasional platform NexaMarket.'): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ShopStatus::SUSPENDED,
            'suspension_reason' => $reason,
            'suspended_at' => now(),
        ]);
    }
}
