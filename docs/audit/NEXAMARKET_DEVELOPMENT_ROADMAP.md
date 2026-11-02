# NEXAMARKET — DEVELOPMENT ROADMAP
**Rencana Pengembangan & Penyempurnaan Bertahap Menuju Marketplace Komersial**

- **Dokumen Terkait:** `docs/audit/NEXAMARKET_FEATURE_GAP_ANALYSIS.md`
- **Target Kesiapan:** Marketplace Multi-Vendor Skala Produksi
- **Penyusun:** Senior Software Architect & Product Analyst
- **Tanggal:** 2 November 2026

---

## 1. Peta Jalan Pengembangan (Phase Overview)

```
[Phase 1] Fundamental Marketplace (Logistik, Pengiriman & Saldo Finansial)
   │
   ▼
[Phase 2] Buyer Experience (Alamat, Wishlist, Ulasan & Alur Retur/Refund)
   │
   ▼
[Phase 3] Seller Productivity (Varian Produk, Input Resi, Cetak Label & Notifikasi)
   │
   ▼
[Phase 4] Promotion and Discovery (Voucher Toko/Platform, Filter Lanjutan & Search)
   │
   ▼
[Phase 5] Admin Intelligence & Governance (Moderasi Produk, Banned User & Audit Log)
   │
   ▼
[Phase 6] Optimization and Production Readiness (Queues, Redis Caching & Live Chat)
```

---

## 2. Rincian Fase Pengembangan

---

### PHASE 1: Fundamental Marketplace (Fondasi Wajib & Kritis)
Fokus pada penyelesaian mata rantai transaksi fisik: alamat pengiriman, biaya logistik per vendor, dan penyaluran dana ke penjual.

#### 1.1 Entitas Alamat Pengiriman Pembeli (`addresses`)
- **Masalah yang Diselesaikan:** Pembeli saat ini tidak memiliki buku alamat, dan order tidak menyimpan informasi ke mana produk harus dikirim.
- **Alasan Prioritas:** P0 (Kritis). Tidak mungkin menjalankan e-commerce fisik tanpa data tujuan kirim.
- **Dependency:** Tabel `users`.
- **Estimasi Kompleksitas:** **M (Medium)**
- **Modul Terdampak:** Buyer Profile, Checkout, Order Details, Database Migrations.

#### 1.2 Kalkulasi Ongkos Kirim Multi-Vendor & Integrasi Ekspedisi
- **Masalah yang Diselesaikan:** Checkout saat ini hanya menagih harga produk tanpa biaya pengiriman. Karena multi-vendor, setiap toko mengirim dari lokasi asal berbeda.
- **Alasan Prioritas:** P0 (Kritis). Ongkos kirim merupakan komponen wajib tagihan e-commerce.
- **Dependency:** Entitas Alamat Pembeli (1.1), Alamat Toko Mitra, API Ekspedisi (misal RajaOngkir / Biteship).
- **Estimasi Kompleksitas:** **L (Large)**
- **Modul Terdampak:** `CheckoutService`, `ShopOrder`, Checkout View, Midtrans Payment Amount.

#### 1.3 Alokasi Saldo Toko & Penarikan Dana Penjual (`seller_balances` & `withdrawals`)
- **Masalah yang Diselesaikan:** Uang penjualan saat ini mengendap di platform tanpa ada pembukuan dompet penjual atau cara penjual mencairkan dana.
- **Alasan Prioritas:** P0 (Kritis). Penjual membutuhkan kepastian hak hasil penjualan saat pesanan telah selesai (`COMPLETED`).
- **Dependency:** `FulfillmentService`, `ShopOrder`.
- **Estimasi Kompleksitas:** **L (Large)**
- **Modul Terdampak:** Seller Dashboard, Admin Finance, Database Migrations.

#### 1.4 Pembatalan Mandiri Pesanan Pending oleh Pembeli
- **Masalah yang Diselesaikan:** Pembeli tidak bisa membatalkan pesanan yang belum dibayar secara manual dari antarmuka riwayat pesanan.
- **Alasan Prioritas:** P0 (Kritis). Membebaskan stok yang tertahan jika pembeli berubah pikiran.
- **Dependency:** `OrderController`, `PaymentService`.
- **Estimasi Kompleksitas:** **S (Small)**
- **Modul Terdampak:** Order Controller, Orders Show View, Stock Restoration Pipeline.

---

### PHASE 2: Buyer Experience (Purna Jual & Kepuasan Pembeli)
Fokus pada memperkaya interaksi pembeli setelah bertransaksi dan membangun reputasi platform.

#### 2.1 Sistem Ulasan & Rating Bintang Produk (`reviews`)
- **Masalah yang Diselesaikan:** Pembeli tidak dapat menilai kualitas barang, dan penjual tidak memiliki reputasi nyata.
- **Alasan Prioritas:** P1 (Tinggi). Ulasan sosial adalah pemicu utama konversi belanja di marketplace.
- **Dependency:** `Order` (status `COMPLETED`), `Product`, `User`.
- **Estimasi Kompleksitas:** **M (Medium)**
- **Modul Terdampak:** Product Detail Page, Buyer Orders View, Seller Reviews View.

#### 2.2 Alur Pengajuan Pengembalian Barang & Refund (Return / Dispute Flow)
- **Masalah yang Diselesaikan:** Pembeli tidak memiliki mekanisme formal untuk mengajukan komplain jika produk salah atau rusak.
- **Alasan Prioritas:** P1 (Tinggi). Memberikan perlindungan konsumen (*buyer protection*).
- **Dependency:** `ShopOrder`, `AdminRefundController`.
- **Estimasi Kompleksitas:** **L (Large)**
- **Modul Terdampak:** Buyer Orders, Seller Orders, Admin Dispute Center.

#### 2.3 Daftar Keinginan Pembeli (Wishlist / Favorites)
- **Masalah yang Diselesaikan:** Pembeli tidak dapat menyimpan produk incaran untuk dibeli di kemudian hari.
- **Alasan Prioritas:** P2 (Sedang). Menjaga keterikatan pengguna (*retention*).
- **Dependency:** `User`, `Product`.
- **Estimasi Kompleksitas:** **S (Small)**
- **Modul Terdampak:** Product Detail Page, Buyer Dashboard.

#### 2.4 Notifikasi Transaksional (Email & In-App Notification Bell)
- **Masalah yang Diselesaikan:** Pengguna tidak menerima kabar saat status pesanan berubah atau pembayaran sukses.
- **Alasan Prioritas:** P1 (Tinggi). Mengurangi kecemasan pembeli terhadap status uang dan paket mereka.
- **Dependency:** Laravel Notifications, Mail/SMTP driver.
- **Estimasi Kompleksitas:** **M (Medium)**
- **Modul Terdampak:** Topbar Navigation, Order Lifecycle Listeners.

---

### PHASE 3: Seller Productivity (Operasional & Manajemen Toko)
Fokus pada mempermudah penjual memproses pesanan dan mengelola katalog produk berskala besar.

#### 3.1 Input Nomor Resi Pengiriman (Airway Bill / Tracking Number)
- **Masalah yang Diselesaikan:** Penjual saat ini hanya mengubah status pesanan menjadi `SHIPPED` tanpa memasukkan nomor resi fisik.
- **Alasan Prioritas:** P1 (Tinggi). Pembeli membutuhkan nomor resi untuk melacak kurir ekspedisi.
- **Dependency:** `ShopOrder`, `FulfillmentService`.
- **Estimasi Kompleksitas:** **S (Small)**
- **Modul Terdampak:** Seller Order Show View, Buyer Order Show View, `UpdateOrderStatusRequest`.

#### 3.2 Varian Produk (SKU, Warna, Ukuran & Harga Bertingkat)
- **Masalah yang Diselesaikan:** Produk saat ini hanya berstruktur tunggal (1 harga, 1 stok). Tidak bisa menjual baju beda ukuran atau barang beda warna.
- **Alasan Prioritas:** P1 (Tinggi). Standar mutlak untuk marketplace ritel dan fashion.
- **Dependency:** `Product`, `CartItem`, `OrderItem`.
- **Estimasi Kompleksitas:** **XL (Extra Large)**
- **Modul Terdampak:** Seller Product CRUD, Product Catalog PDP, Cart & Checkout Service.

#### 3.3 Cetak Label Pengiriman (Shipping Label / Airway Bill PDF)
- **Masalah yang Diselesaikan:** Penjual harus menulis alamat tujuan dan pengirim secara manual di atas paket.
- **Alasan Prioritas:** P2 (Sedang). Mempercepat proses packing penjual.
- **Dependency:** DomPDF, Alamat Pembeli (1.1), Alamat Toko Mitra.
- **Estimasi Kompleksitas:** **S (Small)**
- **Modul Terdampak:** Seller Order Details.

#### 3.4 Riwayat Mutasi Stok Inventaris (Inventory Ledger)
- **Masalah yang Diselesaikan:** Penjual tidak dapat melacak kapan dan mengapa stok berkurang atau bertambah.
- **Alasan Prioritas:** P2 (Sedang). Mencegah selisih inventaris toko.
- **Dependency:** `Product`, `CheckoutService`, `PaymentService`.
- **Estimasi Kompleksitas:** **M (Medium)**
- **Modul Terdampak:** Seller Product Management.

---

### PHASE 4: Promotion and Discovery (Pemasaran & Penemuan Produk)
Fokus pada meningkatkan konversi dan nilai transaksi belanja rata-rata (*average order value*).

#### 4.1 Mesin Voucher Diskon & Kupon Promosi (`vouchers`)
- **Masalah yang Diselesaikan:** Tidak ada skema potongan harga kupon baik dari platform maupun mandiri per toko.
- **Alasan Prioritas:** P2 (Sedang). Insentif utama pembeli bertransaksi.
- **Dependency:** `CheckoutService`, `Shop`.
- **Estimasi Kompleksitas:** **L (Large)**
- **Modul Terdampak:** Checkout View, Cart, Seller Center, Admin Dashboard.

#### 4.2 Filter Pencarian Lanjutan (Faceted Search)
- **Masalah yang Diselesaikan:** Pencarian katalog hanya filter kategori. Pembeli tidak bisa memfilter rentang harga (min/max), rating, atau lokasi kota toko.
- **Alasan Prioritas:** P2 (Sedang). Mempermudah pembeli menemukan produk yang sesuai budget.
- **Dependency:** `ProductCatalogController`, Katalog View.
- **Estimasi Kompleksitas:** **M (Medium)**
- **Modul Terdampak:** Catalog Index View & Controller.

#### 4.3 Pemilihan Parsial Item Keranjang (Checkbox Checkout per Item)
- **Masalah yang Diselesaikan:** Pembeli terpaksa meng-checkout seluruh isi keranjang belanja sekaligus.
- **Alasan Prioritas:** P2 (Sedang). Standar belanja multi-toko agar pembeli bisa membayar item toko tertentu terlebih dahulu.
- **Dependency:** `CartItem`, `CartController`, `CheckoutService`.
- **Estimasi Kompleksitas:** **M (Medium)**
- **Modul Terdampak:** Cart View, Checkout View.

#### 4.4 CMS Banner & Promo Highlight Beranda
- **Masalah yang Diselesaikan:** Banner hero saat ini di-hardcode di Blade view.
- **Alasan Prioritas:** P3 (Rendah). Fleksibilitas pemasaran visual admin.
- **Dependency:** Admin Dashboard, Model `Banner`.
- **Estimasi Kompleksitas:** **M (Medium)**
- **Modul Terdampak:** Homepage, Admin Portal.

---

### PHASE 5: Admin Intelligence & Governance (Tata Kelola Platform)
Fokus pada penegakan kepatuhan, moderasi keamanan, dan audit menyeluruh platform.

#### 5.1 Tindakan Moderasi Produk Administratif (Admin Takedown / Suspend Produk)
- **Masalah yang Diselesaikan:** Admin saat ini hanya bisa melihat daftar produk secara *read-only*, tidak bisa menurunkan paksa produk ilegal atau terlarang.
- **Alasan Prioritas:** P1 (Tinggi). Manajemen risiko hukum dan reputasi marketplace.
- **Dependency:** `Admin\ProductController`, Model `Product`.
- **Estimasi Kompleksitas:** **S (Small)**
- **Modul Terdampak:** Admin Product Center, Seller Product Status.

#### 5.2 Tata Kelola Akun Pengguna Lanjutan (Banned / Suspend User)
- **Masalah yang Diselesaikan:** Admin tidak bisa menonaktifkan akun pembeli atau penjual bermasalah (fraud).
- **Alasan Prioritas:** P1 (Tinggi). Keamanan platform terhadap pelaku penipuan.
- **Dependency:** `Admin\UserController`, Middleware Auth.
- **Estimasi Kompleksitas:** **S (Small)**
- **Modul Terdampak:** Admin User Center, User Authentication.

#### 5.3 Tabel Jejak Audit Permanen (`audit_logs`)
- **Masalah yang Diselesaikan:** Tindakan penting admin (seperti menyetujui toko atau menghapus kategori) tidak tersimpan dalam catatan permanen siapa yang melakukan dan dari IP mana.
- **Alasan Prioritas:** P2 (Sedang). Kepatuhan (*compliance*) dan akuntabilitas tim internal.
- **Dependency:** Database Migration `audit_logs`, Event/Observer.
- **Estimasi Kompleksitas:** **M (Medium)**
- **Modul Terdampak:** Admin Core, Seluruh Controller Admin.

---

### PHASE 6: Optimization & Production Readiness (Skalabilitas & Interaksi Real-Time)
Fokus pada performa tinggi, efisiensi server, dan pengalaman pengguna interaktif.

#### 6.1 Sistem Chat Interaktif Pembeli-Penjual (Live Messaging)
- **Masalah yang Diselesaikan:** Tidak ada komunikasi langsung antar pengguna untuk konsultasi barang.
- **Alasan Prioritas:** P2 (Sedang). Memerlukan infrastruktur WebSockets (Laravel Reverb).
- **Dependency:** Laravel Reverb / Pusher, Livewire 3.
- **Estimasi Kompleksitas:** **XL (Extra Large)**
- **Modul Terdampak:** Buyer Dashboard, Seller Center, Global Chat Floating Widget.

#### 6.2 Antrian Asinkron (Queues & Workers) untuk Webhook & Email
- **Masalah yang Diselesaikan:** Pemrosesan webhook dan notifikasi berjalan sinkron di thread HTTP request.
- **Alasan Prioritas:** P2 (Sedang). Mencegah request timeout saat beban transaksi melonjak.
- **Dependency:** Redis / Database Queue Worker.
- **Estimasi Kompleksitas:** **M (Medium)**
- **Modul Terdampak:** `PaymentService`, Mailers, Event Listeners.

#### 6.3 Caching Layer Katalog & Beranda (Redis Caching)
- **Masalah yang Diselesaikan:** Kueri agregasi kategori dan katalog dieksekusi berulang kali ke PostgreSQL.
- **Alasan Prioritas:** P2 (Sedang). Meningkatkan performa response time di bawah 100ms.
- **Dependency:** Redis Cache Driver.
- **Estimasi Kompleksitas:** **S (Small)**
- **Modul Terdampak:** `ProductCatalogController`, `HomeController`.

---

## 3. Matriks Estimasi & Prioritas Pengembangan

| Fase | Inisiatif Fitur | Prioritas | Kompleksitas | Dampak Bisnis |
| :---: | :--- | :---: | :---: | :--- |
| **Phase 1** | 1.1 Entitas Alamat Pengiriman Pembeli | **P0** | **M** | Menjadikan pengiriman fisik dapat dieksekusi |
| **Phase 1** | 1.2 Kalkulasi Ongkir & Ekspedisi Multi-Vendor | **P0** | **L** | Monetisasi dan keakuratan tagihan checkout |
| **Phase 1** | 1.3 Dompet & Penarikan Dana Penjual | **P0** | **L** | Kepercayaan mitra toko & kepatuhan finansial |
| **Phase 1** | 1.4 Pembatalan Mandiri Pesanan Pending | **P0** | **S** | Perbaikan UX pembeli & perputaran stok |
| **Phase 2** | 2.1 Sistem Ulasan & Rating Bintang | **P1** | **M** | Peningkatan konversi & reputasi toko |
| **Phase 2** | 2.2 Alur Retur & Refund Komplain | **P1** | **L** | Perlindungan pembeli (*buyer trust*) |
| **Phase 2** | 2.4 Notifikasi Email & In-App | **P1** | **M** | Keterikatan pengguna & kepastian status |
| **Phase 3** | 3.1 Input Resi Pengiriman oleh Seller | **P1** | **S** | Transparansi pelacakan pengiriman kurir |
| **Phase 3** | 3.2 Varian Produk (SKU/Warna/Ukuran) | **P1** | **XL** | Ekspansi kategori ritel & fashion |
| **Phase 5** | 5.1 Moderasi Takedown Produk Admin | **P1** | **S** | Mitigasi risiko hukum produk terlarang |
| **Phase 5** | 5.2 Banned / Suspend Akun Pengguna | **P1** | **S** | Perlindungan keamanan dari penipuan |
| **Phase 2** | 2.3 Daftar Keinginan (Wishlist) | **P2** | **S** | Retensi pembeli |
| **Phase 4** | 4.1 Mesin Voucher & Kupon Promosi | **P2** | **L** | Stimulus volume transaksi (*GMV growth*) |
| **Phase 4** | 4.2 Filter Pencarian Lanjutan | **P2** | **M** | Kemudahan penemuan produk |
| **Phase 4** | 4.3 Checkbox Parsial Keranjang | **P2** | **M** | Fleksibilitas pembayaran multi-vendor |
| **Phase 6** | 6.1 Chat Interaktif Real-Time | **P2** | **XL** | Interaktivitas tanya jawab sebelum beli |
| **Phase 6** | 6.2 Background Queues & Workers | **P2** | **M** | Ketahanan server terhadap lonjakan traffic |
| **Phase 6** | 6.3 Redis Caching Layer | **P2** | **S** | Performa response time tinggi |

---
*Roadmap ini disusun sebagai panduan strategis implementasi bertahap untuk menjaga stabilitas arsitektur inti NexaMarket yang telah teruji dengan baik.*

