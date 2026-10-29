# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Pembeli (Buyers)**: Pengguna konsumen di Indonesia yang mencari, membandingkan, dan membeli produk berkualitas dari mitra toko resmi. Membutuhkan alur penemuan produk yang cepat, navigasi kategori intuitif, fleksibilitas checkout, autentikasi instan (Google Firebase atau email/password), serta kepastian status pembayaran dan pelacakan pesanan.
- **Mitra Penjual (Sellers / Toko Mitra)**: Pemilik usaha dan merchant resmi yang telah melalui kurasi/persetujuan platform. Membutuhkan Seller Center yang ringkas dan efisien untuk mengelola etalase produk (multi-foto, gambar utama), memantau inventaris stok, serta memperbarui status pemenuhan pesanan pelanggan.
- **Administrator Platform**: Pengelola operasional marketplace yang bertugas memverifikasi pendaftaran toko (approval/rejection/suspend/reactivate), mengawasi integritas katalog global, meninjau rekonsiliasi transaksi pembayaran Midtrans, serta mengaudit laporan bisnis dan keuangan.

## Product Purpose

NexaMarket adalah platform marketplace e-commerce multi-vendor di Indonesia yang menghadirkan pengalaman belanja online yang aman, teratur, dan tepercaya dengan slogan *"Belanja Mudah, Hidup Lebih Baik"*. NexaMarket hadir untuk menjembatani pembeli dengan toko-toko mitra resmi terverifikasi melalui kepastian transaksi, jaminan stok aktual, dan ekosistem pembayaran digital yang terjamin.

Keberhasilan produk diukur melalui kelancaran konversi checkout tanpa friksi, kejelasan status transaksi real-time, kemudahan pengelolaan etalase bagi mitra toko, serta kepatuhan kurasi merchant oleh administrator.

## Positioning

Marketplace multi-vendor terkurasi dengan verifikasi ketat toko mitra resmi dan jaminan keamanan transaksi digital. Berbeda dari pasar digital terbuka tanpa filter, NexaMarket hanya menampilkan produk dari mitra yang disetujui (Approved Shops), melindungi pembeli dengan alur pembayaran Midtrans Snap bersertifikasi kriptografis SHA-512 dan rekonsiliasi status server-to-server otomatis.

## Operating Context

- **Lingkungan Penggunaan**: Web desktop, tablet, dan smartphone responsif.
- **Workflow Pembeli**: Eksplorasi katalog publik (pencarian, filter kategori dropdown, sortir) $\rightarrow$ Keranjang belanja multi-item $\rightarrow$ Checkout $\rightarrow$ Pembayaran Midtrans Snap (atau simulasi lokal) $\rightarrow$ Halaman rincian pesanan & pelacakan status.
- **Workflow Penjual**: Pendaftaran toko (`/buyer/shop/apply`) $\rightarrow$ Menunggu persetujuan admin $\rightarrow$ Akses Seller Center (`/seller/dashboard`) $\rightarrow$ Pengelolaan produk & galeri gambar $\rightarrow$ Pemrosesan pesanan toko.
- **Workflow Admin**: Approval Center toko (`/admin/shops`) $\rightarrow$ Pengawasan katalog & pesanan $\rightarrow$ Rekonsiliasi pembayaran $\rightarrow$ Laporan performa bisnis (ekspor CSV & PDF).

## Capabilities and Constraints

- **Autentikasi Ganda**: Firebase Google Authentication (verifikasi token RS256 di server) dan kredensial email/password berbasis Laravel Breeze & Livewire.
- **Pembayaran Terpercaya**: Midtrans Snap Sandbox (otomasi webhook ber-signature SHA-512, row locking transaksi database, dan rekonsiliasi status aktif dari API Midtrans) serta simulator pembayaran lokal untuk pengujian.
- **Katalog & Etalase**:
  - Grid responsif publik 4 kolom pada desktop dengan filter kategori dropdown dinamis dan sortir (Terbaru, Harga Terendah, Harga Tertinggi).
  - Isolasi produk: hanya produk dari toko berstatus *Approved* dan berstatus *Published* yang tampil di katalog publik.
- **Manajemen Inventaris & Pesanan**: Pengurangan stok otomatis saat pesanan lunas, serta pemulihan stok atomik jika pesanan dibatalkan atau kedaluwarsa.
- **Batasan Akses & Role**: Role berbasis enum `buyer` dan `admin`. Akses seller didasarkan pada kepemilikan toko yang telah berstatus disetujui (`shop.approved` middleware).

## Brand Commitments

- **Nama Brand**: NexaMarket (dengan tipografi `Nexa` gelap dan `Market` oranye).
- **Tagline**: *"Belanja Mudah, Hidup Lebih Baik"*.
- **Warna Identitas**: Oranye brand (`bg-orange-600`, aksen `text-orange-600`), teks navy gelap/slate (`text-slate-900`), latar abu-abu sangat muda (`bg-slate-50`), serta kartu putih bersih (`bg-white`).
- **Simbol Brand**: Ikon tas belanja NexaMarket dengan sudut melengkung dan ornamen minimalis.
- **Tone & Voice**: Profesional, ramah, meyakinkan, jelas, dan mengutamakan transparansi transaksi.

## Evidence on Hand

- Dokumen teknis dan arsitektur integrasi tersimpan di `docs/integrations/firebase-midtrans-setup.md`.
- Komponen logo resmi di `resources/views/components/nexa-logo.blade.php`.
- Skema database dan relasi Eloquent lengkap pada model `User`, `Shop`, `Product`, `Order`, `ShopOrder`, `OrderItem`, `Payment`, dan `Category`.
- Rangkaian pengujian fitur otomatis (Feature Tests) untuk alur autentikasi, katalog, keranjang, checkout, dan pembayaran Midtrans di direktori `tests/Feature/`.

## Product Principles

1. **Kejelasan Transaksi di Atas Segalanya**: Status pesanan, pembayaran, dan stok tidak boleh ambigu atau menyesatkan; konfirmasi pembayaran selalu bergantung pada verifikasi otoritatif yang aman.
2. **Keseimbangan Ekosistem Pembeli & Penjual**: Pengalaman belanja pembeli harus gesit dan bebas friksi, sementara dashboard mitra penjual harus informatif dan efisien untuk operasional harian.
3. **Integritas Mitra Resmi**: Kredibilitas platform dijaga dengan kurasi ketat toko dan perlindungan kualitas katalog publik.
4. **Respon Cepat & Aksesibel**: Tata letak responsif yang rapi pada desktop hingga mobile tanpa layout shift atau elemen yang terpotong.

## Accessibility & Inclusion

- Form dan kontrol navigasi dirancang dengan label eksplisit, kontras warna yang memenuhi rasio baca (WCAG AA), dan navigasi keyboard yang memadai pada dropdown dan modal dialog.
- Pesan status (sukses, verifikasi, gagal) dilengkapi indikator warna dan teks deskriptif yang jelas bagi seluruh pengguna.

