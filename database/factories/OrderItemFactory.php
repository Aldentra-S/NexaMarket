<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShopOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shop_order_id' => ShopOrder::factory(),
            'product_id' => Product::factory()->published(),
            'product_name' => fake()->words(3, true),
            'product_slug' => fake()->slug(),
            'unit_price' => 50000.00,
            'quantity' => 2,
            'subtotal' => 100000.00,
        ];
    }
}
