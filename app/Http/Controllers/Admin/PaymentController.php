<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display a listing of all payment gateway transactions.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        $query = Payment::with(['order.user'])->latest();

        if ($statusFilter && in_array($statusFilter, PaymentStatus::values(), true)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'ilike', "%{$search}%")
                  ->orWhere('gateway_transaction_id', 'ilike', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'ilike', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        $totalSettled = (float) Payment::where('status', PaymentStatus::SUCCESS->value)->sum('amount');

        return view('admin.payments.index', [
            'payments' => $payments,
            'currentStatus' => $statusFilter,
            'search' => $search,
            'totalCount' => Payment::count(),
            'successCount' => Payment::where('status', PaymentStatus::SUCCESS->value)->count(),
            'pendingCount' => Payment::where('status', PaymentStatus::PENDING->value)->count(),
            'failedCount' => Payment::where('status', PaymentStatus::FAILED->value)->count(),
            'totalSettled' => $totalSettled,
        ]);
    }
}
