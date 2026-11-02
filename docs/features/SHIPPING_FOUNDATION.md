# NexaMarket Shipping Foundation (Phase 1)
**Address Book, Flat-Rate Shipping Cost, Courier Selection & Order Tracking**

---

## 1. Executive Summary

NexaMarket Shipping Foundation Phase 1 introduces complete physical shipping capabilities to the multi-vendor marketplace platform without external paid API dependencies (Budget: Rp0).

The system enables:
- **Buyers** to maintain an address book, toggle default addresses, select delivery destinations during checkout, and track courier details and tracking numbers (*nomor resi*) with a one-click copy tool.
- **Sellers** to set flat-rate shipping costs per shop, inspect recipient delivery information, select couriers from standard Indonesian logistics providers, and input tracking numbers to dispatch orders.
- **Admins** to audit complete shipping snapshots, courier tracking numbers, and delivery costs across all vendor orders.

---

## 2. Architecture & Database Design

### 2.1 Database Schema Extensions

#### `addresses` Table
```sql
CREATE TABLE addresses (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    label VARCHAR(50) NOT NULL, -- 'Rumah', 'Kantor', 'Apartemen'
    recipient_name VARCHAR(100) NOT NULL,
    phone VARCHAR(25) NOT NULL,
    province VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    district VARCHAR(100) NOT NULL,
    village VARCHAR(100) NULL,
    postal_code VARCHAR(10) NOT NULL,
    address_line TEXT NOT NULL,
    delivery_notes VARCHAR(255) NULL,
    is_default BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### `shops` Table
- Added `shipping_cost NUMERIC(12, 2) NOT NULL DEFAULT 0.00`.

#### `orders` Table (Snapshot Fields)
- `shipping_recipient_name VARCHAR(100) NULL`
- `shipping_phone VARCHAR(25) NULL`
- `shipping_province VARCHAR(100) NULL`
- `shipping_city VARCHAR(100) NULL`
- `shipping_district VARCHAR(100) NULL`
- `shipping_postal_code VARCHAR(10) NULL`
- `shipping_address TEXT NULL`
- `shipping_delivery_notes VARCHAR(255) NULL`
- `shipping_cost_total NUMERIC(12, 2) NOT NULL DEFAULT 0.00`

#### `shop_orders` Table
- `shipping_cost NUMERIC(12, 2) NOT NULL DEFAULT 0.00`
- `courier VARCHAR(50) NULL`
- `tracking_number VARCHAR(100) NULL`
- `shipped_at TIMESTAMP NULL`

---

## 3. Business Logic & Data Flow

### 3.1 Snapshot Immutability
All order shipping data is written as a static snapshot upon checkout completion in `CheckoutService`.
- If a buyer subsequently updates, edits, or permanently deletes an address from their address book, previously created `orders` records retain their full original recipient name, phone, address lines, and delivery instructions.
- Foreign keys to `addresses` are not stored on `orders` to ensure complete historical auditability.

### 3.2 Multi-Vendor Shipping Cost Calculation
- Each vendor shop independently configures its flat-rate shipping cost (`shops.shipping_cost`).
- During checkout, cart items are grouped by `shop_id`.
- The shipping cost for each shop is computed once per vendor:
  $$\text{Grand Total} = \sum(\text{Items Subtotal}) + \sum_{\text{shops}}(\text{Shipping Cost})$$
- Each child `ShopOrder` records its shop's `shipping_cost`.
- The parent `Order` records `shipping_cost_total = sum(child_shipping_costs)`.

### 3.3 Midtrans Snap Payment Gateway Alignment
- Midtrans Snap API strictly enforces that the sum of all elements in `item_details` matches `gross_amount`.
- In `MidtransPaymentGateway`, when `shopOrder->shipping_cost > 0`, an item detail entry is automatically injected:
  ```php
  $itemDetails[] = [
      'id' => 'shipping-shop-' . $shopOrder->id,
      'price' => (int) round((float) $shopOrder->shipping_cost),
      'quantity' => 1,
      'name' => mb_substr('Ongkir: ' . $shopOrder->shop_name, 0, 50),
  ];
  ```
- Buyer recipient details and phone number are passed into Midtrans `customer_details`.

---

## 4. Logistics & Courier Fulfillment Workflow

### Supported Couriers
- `JNE`
- `J&T Express`
- `SiCepat`
- `AnterAja`
- `Pos Indonesia`
- `Ninja Xpress`
- `ID Express`
- `Lainnya` (Manual / Other)

### Status Transition Flow
```
PAID (Pembayaran Terverifikasi)
  │
  ▼ [Seller: "Mulai Proses Pesanan"]
PROCESSING (Sedang Dikemas)
  │
  ▼ [Seller: Input Courier + Tracking Number]
SHIPPED (Dalam Pengiriman & Resi Tersedia)
  │
  ▼ [Seller: "Selesaikan Pesanan"]
COMPLETED (Pesanan Selesai)
```

---

## 5. Security & IDOR Protection

1. **Address Ownership Policy (`AddressPolicy`)**:
   - Every `view`, `update`, `delete`, and `setDefault` operation verifies `$user->id === $address->user_id`.
   - Access attempts to other users' addresses return an immediate `403 Forbidden`.
2. **Checkout Address ID Verification**:
   - `ProcessCheckoutRequest` validates `address_id exists:addresses,id`.
   - `CheckoutService` explicitly filters `$user->addresses()->where('id', $addressId)->first()`, rejecting address IDs belonging to other users.
3. **Seller Order Fulfillment Boundary**:
   - `ShopOrderPolicy` ensures sellers can only view, update, or ship `ShopOrder` records that belong to their approved shop.

---

## 6. Automated Test Coverage

| Test Suite | File Path | Focus |
|---|---|---|
| Address Book | `tests/Feature/AddressBookTest.php` | CRUD, auto default, default toggle, validation, IDOR protection |
| Shipping Cost | `tests/Feature/ShippingCostTest.php` | Default zero, seller update, validation, authorization |
| Checkout Shipping | `tests/Feature/CheckoutShippingTest.php` | Address snapshot, multi-vendor shipping calculation, snapshot immutability, IDOR rejection |
| Seller Shipping | `tests/Feature/SellerShippingManagementTest.php` | Recipient inspection, courier & resi input, transition to SHIPPED, validation, completion |
| Buyer Tracking | `tests/Feature/BuyerOrderTrackingTest.php` | Buyer visibility of courier, resi, recipient snapshot, privacy boundary |
| Admin Audit | `tests/Feature/AdminOrderShippingTest.php` | Admin visibility of shipping snapshots, tracking info, breakdown |

---

## 7. UI Design Compliance

All newly built interfaces follow NexaMarket's Soft Pastel Gradient & Modern Minimalist SaaS design tokens:
- **Card Surfaces**: Clean white (`#FFFFFF`) with subtle slate borders (`border-slate-200`) and soft shadows (`shadow-xs`).
- **Interactive Modals**: Smooth Alpine.js backdrop blur modals with keyboard navigation and focus states.
- **Micro-interactions**: One-click clipboard copy (`navigator.clipboard.writeText`) with real-time SVG checkmark feedback.

