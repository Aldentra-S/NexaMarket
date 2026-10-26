<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RefundController extends Controller
{
    /**
     * Display a listing of cancelled or expired orders requiring refund/reconciliation review.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', OrderStatus::CANCELLED->value);
        $search = $request->query('search');

        $query = Order::with(['user', 'shopOrders.shop', 'payments'])
            ->whereIn('status', [OrderStatus::CANCELLED->value, OrderStatus::EXPIRED->value])
            ->latest();

        if ($statusFilter && in_array($statusFilter, [OrderStatus::CANCELLED->value, OrderStatus::EXPIRED->value], true)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'ilike', "%{$search}%")
                         ->orWhere('email', 'ilike', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        $totalCancelledCount = Order::where('status', OrderStatus::CANCELLED->value)->count();
        $totalExpiredCount = Order::where('status', OrderStatus::EXPIRED->value)->count();

        // Orders that were paid before being cancelled
        $paidAndCancelled = Order::where('status', OrderStatus::CANCELLED->value)
            ->whereNotNull('paid_at')
            ->sum('total_amount');

        return view('admin.refunds.index', [
            'orders' => $orders,
            'currentStatus' => $statusFilter,
            'search' => $search,
            'totalCancelledCount' => $totalCancelledCount,
            'totalExpiredCount' => $totalExpiredCount,
            'paidAndCancelledAmount' => (float) $paidAndCancelled,
        ]);
    }
}
