<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\ShopOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FulfillmentService
{
    /**
     * Transition a child shop order to a new fulfillment status with parent-order consistency.
     *
     * @throws ValidationException
     */
    public function updateStatus(ShopOrder $shopOrder, OrderStatus $targetStatus): ShopOrder
    {
        return DB::transaction(function () use ($shopOrder, $targetStatus) {
            // Lock the shop order record to prevent concurrent updates
            /** @var ShopOrder $freshShopOrder */
            $freshShopOrder = ShopOrder::where('id', $shopOrder->id)->lockForUpdate()->firstOrFail();

            // Validate that the transition from current status to target status is permitted
            if (! $freshShopOrder->status->canTransitionTo($targetStatus)) {
                throw ValidationException::withMessages([
                    'status' => "Perubahan status dari '{$freshShopOrder->status->label()}' menjadi '{$targetStatus->label()}' tidak diizinkan.",
                ]);
            }

            // Update only the status column of the shop order
            $freshShopOrder->update([
                'status' => $targetStatus,
            ]);

            // If the child shop order is completed, inspect if all sibling child orders under the parent order are completed
            if ($targetStatus === OrderStatus::COMPLETED) {
                $parentOrder = Order::where('id', $freshShopOrder->order_id)->lockForUpdate()->firstOrFail();

                $hasIncompleteChildOrders = $parentOrder->shopOrders()
                    ->where('status', '!=', OrderStatus::COMPLETED->value)
                    ->exists();

                if (! $hasIncompleteChildOrders) {
                    $parentOrder->update([
                        'status' => OrderStatus::COMPLETED,
                    ]);
                }
            }

            return $freshShopOrder;
        });
    }
}
