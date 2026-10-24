<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->buyer(),
            'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(fake()->unique()->bothify('??###')),
            'status' => OrderStatus::PENDING,
            'total_amount' => 100000.00,
            'idempotency_key' => fake()->unique()->uuid(),
            'notes' => null,
            'paid_at' => null,
            'cancelled_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::PAID,
            'paid_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::EXPIRED,
            'cancelled_at' => now(),
        ]);
    }
}
