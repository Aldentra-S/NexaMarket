<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Shop;
use App\Models\ShopOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShopOrder>
 */
class ShopOrderFactory extends Factory
{
    protected $model = ShopOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'shop_id' => Shop::factory()->approved(),
            'shop_name' => fake()->company() . ' Shop',
            'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(fake()->unique()->bothify('??###')) . '-S1',
            'status' => OrderStatus::PENDING,
            'subtotal' => 100000.00,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::PAID,
        ]);
    }
}
