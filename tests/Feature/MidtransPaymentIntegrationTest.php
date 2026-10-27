<?php

namespace Tests\Feature;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransPaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function bindFakeGateway(?string $mockToken = 'SNAP-MOCK-TOKEN-XYZ123', ?string $serverKey = 'test-server-key'): void
    {
        config(['services.midtrans.server_key' => $serverKey]);

        $fake = new class($mockToken, $serverKey) implements PaymentGatewayInterface {
            public function __construct(
                private ?string $mockToken,
                private ?string $serverKey
            ) {}

            public function createTransaction(Order $order): Payment
            {
                if ($this->mockToken === null) {
                    throw new Exception('Midtrans API Gateway Timeout.');
                }

                $existing = Payment::where('order_id', $order->id)
                    ->where('payment_method', 'midtrans')
                    ->where('status', PaymentStatus::PENDING)
                    ->whereNotNull('snap_token')
                    ->first();

                if ($existing) {
                    return $existing;
                }

                $paymentNumber = 'PAY-MID-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

                return Payment::create([
                    'order_id' => $order->id,
                    'payment_number' => $paymentNumber,
                    'status' => PaymentStatus::PENDING,
                    'amount' => $order->total_amount,
                    'payment_method' => 'midtrans',
                    'snap_token' => $this->mockToken,
                    'snap_redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $this->mockToken,
                    'payload' => [
                        'gateway' => 'midtrans_sandbox',
                    ],
                ]);
            }

            public function verifyNotification(array $payload): bool
            {
                $orderId = $payload['order_id'] ?? '';
                $statusCode = $payload['status_code'] ?? '';
                $grossAmount = $payload['gross_amount'] ?? '';
                $signature = $payload['signature_key'] ?? '';

                if (empty($orderId) || empty($statusCode) || empty($grossAmount) || empty($signature)) {
                    return false;
                }

                $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

                return hash_equals($expected, $signature);
            }

            public function checkTransactionStatus(string $paymentNumber): ?array
            {
                return null;
            }
        };

        $this->app->instance(PaymentGatewayInterface::class, $fake);
    }

    private function createOrderWithProduct(User $buyer, float $price = 75000.00, int $quantity = 2): array
    {
        $shop = Shop::factory()->approved()->create();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'price' => $price,
            'stock' => 10 - $quantity, // stock decremented upon order placement
        ]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-MID-' . uniqid(),
            'status' => OrderStatus::PENDING,
            'total_amount' => $price * $quantity,
        ]);

        $shopOrder = ShopOrder::create([
            'order_id' => $order->id,
            'shop_id' => $shop->id,
            'shop_name' => $shop->name,
            'order_number' => $order->order_number . '-S1',
            'status' => OrderStatus::PENDING,
            'subtotal' => $price * $quantity,
        ]);

        $item = OrderItem::create([
            'shop_order_id' => $shopOrder->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'unit_price' => $price,
            'quantity' => $quantity,
            'subtotal' => $price * $quantity,
        ]);

        return [$order, $product, $shopOrder, $item];
    }

    private function generateMidtransSignature(string $orderId, string $statusCode, string $grossAmount, string $serverKey = 'test-server-key'): string
    {
        return hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
    }

    public function test_buyer_can_create_snap_token_with_correct_server_calculated_amount(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order] = $this->createOrderWithProduct($buyer, price: 50000.00, quantity: 2); // total: 100,000

        $this->bindFakeGateway('SNAP-TOKEN-12345');

        $response = $this->actingAs($buyer)->postJson(route('orders.payment.snap', $order));

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'snap_token' => 'SNAP-TOKEN-12345',
        ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'snap_token' => 'SNAP-TOKEN-12345',
            'payment_method' => 'midtrans',
            'status' => PaymentStatus::PENDING->value,
            'amount' => 100000.00,
        ]);
    }

    public function test_buyer_cannot_create_snap_token_for_another_buyers_order(): void
    {
        $buyerA = User::factory()->buyer()->create();
        $buyerB = User::factory()->buyer()->create();
        [$orderB] = $this->createOrderWithProduct($buyerB);

        $this->bindFakeGateway();

        $response = $this->actingAs($buyerA)->postJson(route('orders.payment.snap', $orderB));

        $response->assertForbidden();
    }

    public function test_already_paid_order_cannot_generate_new_snap_token(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order] = $this->createOrderWithProduct($buyer);
        $order->update(['status' => OrderStatus::PAID]);

        $this->bindFakeGateway();

        $response = $this->actingAs($buyer)->postJson(route('orders.payment.snap', $order));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order']);
    }

    public function test_subsequent_request_reuses_existing_active_snap_token_without_duplicate(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order] = $this->createOrderWithProduct($buyer);

        $this->bindFakeGateway('SNAP-REUSED-TOKEN');

        // First call creates
        $res1 = $this->actingAs($buyer)->postJson(route('orders.payment.snap', $order));
        $res1->assertOk();

        // Second call reuses
        $res2 = $this->actingAs($buyer)->postJson(route('orders.payment.snap', $order));
        $res2->assertOk();
        $res2->assertJson(['snap_token' => 'SNAP-REUSED-TOKEN']);

        // Assert only 1 payment was created
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_midtrans_webhook_with_valid_signature_settlement_marks_order_and_shop_orders_paid(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order, $product, $shopOrder] = $this->createOrderWithProduct($buyer, price: 60000.00, quantity: 2); // 120,000

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_number' => 'PAY-MID-TEST-001',
            'status' => PaymentStatus::PENDING,
            'amount' => 120000.00,
            'payment_method' => 'midtrans',
            'snap_token' => 'SNAP-TEST-001',
        ]);

        $this->bindFakeGateway();

        $grossAmount = '120000.00';
        $signature = $this->generateMidtransSignature($payment->payment_number, '200', $grossAmount);

        $webhookPayload = [
            'order_id' => $payment->payment_number,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'transaction_status' => 'settlement',
            'transaction_id' => 'midtrans-trx-998877',
            'signature_key' => $signature,
        ];

        $response = $this->postJson(route('payments.midtrans.notification'), $webhookPayload);

        $response->assertOk();
        $response->assertJson(['status' => 'success']);

        $this->assertSame(OrderStatus::PAID, $order->fresh()->status);
        $this->assertSame(OrderStatus::PAID, $shopOrder->fresh()->status);
        $this->assertSame(PaymentStatus::SUCCESS, $payment->fresh()->status);
        $this->assertSame('midtrans-trx-998877', $payment->fresh()->gateway_transaction_id);
        $this->assertNotNull($order->fresh()->paid_at);
        // Stock remains decremented (8)
        $this->assertSame(8, $product->fresh()->stock);
    }

    public function test_midtrans_webhook_with_invalid_signature_is_rejected(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order, $product, $shopOrder] = $this->createOrderWithProduct($buyer);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_number' => 'PAY-MID-TAMPER-001',
            'status' => PaymentStatus::PENDING,
            'amount' => $order->total_amount,
            'payment_method' => 'midtrans',
        ]);

        $this->bindFakeGateway();

        $webhookPayload = [
            'order_id' => $payment->payment_number,
            'status_code' => '200',
            'gross_amount' => (string) $order->total_amount,
            'transaction_status' => 'settlement',
            'signature_key' => 'TAMPERED-INVALID-SIGNATURE-HASH',
        ];

        $response = $this->postJson(route('payments.midtrans.notification'), $webhookPayload);

        $response->assertStatus(422);
        $this->assertSame(OrderStatus::PENDING, $order->fresh()->status);
        $this->assertSame(PaymentStatus::PENDING, $payment->fresh()->status);
    }

    public function test_midtrans_webhook_with_amount_mismatch_is_rejected(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order] = $this->createOrderWithProduct($buyer, price: 100000.00, quantity: 1);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_number' => 'PAY-MID-MISMATCH',
            'status' => PaymentStatus::PENDING,
            'amount' => 100000.00,
            'payment_method' => 'midtrans',
        ]);

        $this->bindFakeGateway();

        // Attacker attempts to pay 10.00 instead of 100000.00
        $fakeAmount = '10.00';
        $signature = $this->generateMidtransSignature($payment->payment_number, '200', $fakeAmount);

        $webhookPayload = [
            'order_id' => $payment->payment_number,
            'status_code' => '200',
            'gross_amount' => $fakeAmount,
            'transaction_status' => 'settlement',
            'signature_key' => $signature,
        ];

        $response = $this->postJson(route('payments.midtrans.notification'), $webhookPayload);

        $response->assertStatus(422);
        $this->assertSame(OrderStatus::PENDING, $order->fresh()->status);
    }

    public function test_midtrans_webhook_expire_expires_order_and_restores_stock_atomically(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order, $product, $shopOrder] = $this->createOrderWithProduct($buyer, price: 50000.00, quantity: 3); // initial stock 10 - 3 = 7

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_number' => 'PAY-MID-EXPIRE-TEST',
            'status' => PaymentStatus::PENDING,
            'amount' => 150000.00,
            'payment_method' => 'midtrans',
        ]);

        $this->bindFakeGateway();

        $grossAmount = '150000.00';
        $signature = $this->generateMidtransSignature($payment->payment_number, '200', $grossAmount);

        $webhookPayload = [
            'order_id' => $payment->payment_number,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'transaction_status' => 'expire',
            'signature_key' => $signature,
        ];

        $response = $this->postJson(route('payments.midtrans.notification'), $webhookPayload);

        $response->assertOk();
        $this->assertSame(OrderStatus::EXPIRED, $order->fresh()->status);
        $this->assertSame(OrderStatus::EXPIRED, $shopOrder->fresh()->status);
        $this->assertSame(PaymentStatus::EXPIRED, $payment->fresh()->status);
        // Stock restored from 7 back to 10
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_duplicate_webhook_is_handled_idempotently_without_double_stock_restoration(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order, $product] = $this->createOrderWithProduct($buyer, price: 50000.00, quantity: 2); // 8 left

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_number' => 'PAY-MID-DUP-TEST',
            'status' => PaymentStatus::PENDING,
            'amount' => 100000.00,
            'payment_method' => 'midtrans',
        ]);

        $this->bindFakeGateway();

        $grossAmount = '100000.00';
        $signature = $this->generateMidtransSignature($payment->payment_number, '200', $grossAmount);

        $webhookPayload = [
            'order_id' => $payment->payment_number,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'transaction_status' => 'expire',
            'signature_key' => $signature,
        ];

        // Send 1st webhook
        $this->postJson(route('payments.midtrans.notification'), $webhookPayload)->assertOk();
        $this->assertSame(10, $product->fresh()->stock);

        // Send 2nd duplicate webhook
        $this->postJson(route('payments.midtrans.notification'), $webhookPayload)->assertOk();
        // Stock must still be 10, NOT 12!
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_out_of_order_webhook_does_not_regress_paid_order(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order, $product] = $this->createOrderWithProduct($buyer, price: 50000.00, quantity: 2);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_number' => 'PAY-MID-OUT-OF-ORDER',
            'status' => PaymentStatus::PENDING,
            'amount' => 100000.00,
            'payment_method' => 'midtrans',
        ]);

        $this->bindFakeGateway();

        $grossAmount = '100000.00';
        $signature = $this->generateMidtransSignature($payment->payment_number, '200', $grossAmount);

        // 1. Settlement arrives first
        $this->postJson(route('payments.midtrans.notification'), [
            'order_id' => $payment->payment_number,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'transaction_status' => 'settlement',
            'signature_key' => $signature,
        ])->assertOk();

        $this->assertSame(OrderStatus::PAID, $order->fresh()->status);

        // 2. Delayed 'pending' or 'expire' webhook arrives later
        $this->postJson(route('payments.midtrans.notification'), [
            'order_id' => $payment->payment_number,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'transaction_status' => 'pending',
            'signature_key' => $signature,
        ])->assertOk();

        // Order MUST stay PAID, must NOT regress to PENDING
        $this->assertSame(OrderStatus::PAID, $order->fresh()->status);
        $this->assertSame(PaymentStatus::SUCCESS, $payment->fresh()->status);
    }

    public function test_tampered_browser_redirect_does_not_mark_order_as_paid(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order] = $this->createOrderWithProduct($buyer);

        $this->bindFakeGateway();

        // Attacker appends Midtrans redirect query parameters to the order detail URL
        $response = $this->actingAs($buyer)->get(route('orders.show', $order) . '?order_id=' . $order->order_number . '&status_code=200&transaction_status=settlement');

        $response->assertOk();
        // Server ignores browser query params: order remains PENDING!
        $this->assertSame(OrderStatus::PENDING, $order->fresh()->status);
        $this->assertFalse($order->fresh()->isPaid());
    }

    public function test_delayed_settlement_webhook_does_not_resurrect_cancelled_or_expired_order(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order, $product] = $this->createOrderWithProduct($buyer, price: 50000.00, quantity: 2);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_number' => 'PAY-MID-CANCELLED-RESURRECT',
            'status' => PaymentStatus::PENDING,
            'amount' => 100000.00,
            'payment_method' => 'midtrans',
        ]);

        $this->bindFakeGateway();

        $grossAmount = '100000.00';
        $expireSig = $this->generateMidtransSignature($payment->payment_number, '200', $grossAmount);

        // 1. Order expires first (stock restored back to 10)
        $this->postJson(route('payments.midtrans.notification'), [
            'order_id' => $payment->payment_number,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'transaction_status' => 'expire',
            'signature_key' => $expireSig,
        ])->assertOk();

        $this->assertSame(OrderStatus::EXPIRED, $order->fresh()->status);
        $this->assertSame(10, $product->fresh()->stock);

        // 2. Delayed settlement arrives afterwards
        $settleSig = $this->generateMidtransSignature($payment->payment_number, '200', $grossAmount);
        $this->postJson(route('payments.midtrans.notification'), [
            'order_id' => $payment->payment_number,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'transaction_status' => 'settlement',
            'signature_key' => $settleSig,
        ])->assertOk();

        // Order MUST stay EXPIRED, must NOT resurrect to PAID, and stock must remain 10
        $this->assertSame(OrderStatus::EXPIRED, $order->fresh()->status);
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_returning_from_gateway_displays_verification_waiting_state_without_premature_paid_status(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order] = $this->createOrderWithProduct($buyer);

        Payment::create([
            'order_id' => $order->id,
            'payment_number' => 'PAY-MID-VERIFY-001',
            'status' => PaymentStatus::PENDING,
            'amount' => $order->total_amount,
            'payment_method' => 'midtrans',
            'snap_token' => 'SNAP-VERIFY-TOKEN',
        ]);

        $this->bindFakeGateway();

        // User returns from Midtrans after completing payment
        $response = $this->actingAs($buyer)->get(route('orders.show', $order) . '?order_id=PAY-MID-VERIFY-001&status_code=200&transaction_status=settlement');

        $response->assertOk();
        $response->assertSee('Menunggu Verifikasi Pembayaran');
        $response->assertSee('PAY-MID-VERIFY-001');
        // Must NOT prematurely mark as paid without trusted verification
        $this->assertSame(OrderStatus::PENDING, $order->fresh()->status);
    }

    public function test_gateway_status_reconciliation_verifies_payment_when_authoritatively_confirmed(): void
    {
        $buyer = User::factory()->buyer()->create();
        [$order, $product, $shopOrder] = $this->createOrderWithProduct($buyer, price: 80000.00, quantity: 1);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_number' => 'PAY-MID-RECONCILE-001',
            'status' => PaymentStatus::PENDING,
            'amount' => 80000.00,
            'payment_method' => 'midtrans',
            'snap_token' => 'SNAP-RECONCILE-TOKEN',
        ]);

        $serverKey = 'test-server-key';
        config(['services.midtrans.server_key' => $serverKey]);
        $sig = $this->generateMidtransSignature($payment->payment_number, '200', '80000.00', $serverKey);

        $fakeWithReconcile = new class('SNAP-RECONCILE-TOKEN', $serverKey, $payment->payment_number, $sig) implements PaymentGatewayInterface {
            public function __construct(
                private ?string $mockToken,
                private ?string $serverKey,
                private string $expectedOrderNumber,
                private string $expectedSig
            ) {}

            public function createTransaction(Order $order): Payment
            {
                return Payment::where('order_id', $order->id)->firstOrFail();
            }

            public function verifyNotification(array $payload): bool
            {
                $orderId = $payload['order_id'] ?? '';
                $statusCode = $payload['status_code'] ?? '';
                $grossAmount = $payload['gross_amount'] ?? '';
                $signature = $payload['signature_key'] ?? '';

                $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

                return hash_equals($expected, $signature);
            }

            public function checkTransactionStatus(string $paymentNumber): ?array
            {
                return [
                    'order_id' => $this->expectedOrderNumber,
                    'status_code' => '200',
                    'gross_amount' => '80000.00',
                    'transaction_status' => 'settlement',
                    'transaction_id' => 'midtrans-auto-reconciled-123',
                    'signature_key' => $this->expectedSig,
                ];
            }
        };

        $this->app->instance(PaymentGatewayInterface::class, $fakeWithReconcile);

        // When user returns and reconcile check runs against gateway
        $response = $this->actingAs($buyer)->get(route('orders.show', $order) . '?order_id=' . $payment->payment_number . '&status_code=200&transaction_status=settlement');

        $response->assertOk();
        $this->assertSame(OrderStatus::PAID, $order->fresh()->status);
        $this->assertSame(OrderStatus::PAID, $shopOrder->fresh()->status);
        $this->assertSame(PaymentStatus::SUCCESS, $payment->fresh()->status);
        $response->assertSee('Pembayaran Berhasil Diverifikasi');
        $response->assertSee('Midtrans Snap');
    }
}
