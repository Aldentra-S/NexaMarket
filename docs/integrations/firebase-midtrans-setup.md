# Panduan Integrasi Firebase Google Authentication & Midtrans Snap Sandbox

Dokumen ini memuat panduan komprehensif implementasi, konfigurasi environment, arsitektur keamanan, dan verifikasi untuk integrasi **Firebase Google Authentication** dan **Midtrans Snap Payment Gateway** di NexaMarket.

---

## 1. Arsitektur & Prinsip Desain

### A. Firebase Google Authentication
1. **Client-side Authentication:**
   - Frontend menginisialisasi Firebase Web App SDK (`firebase/auth`) menggunakan kredensial publik dari Vite env.
   - Pengguna menekan tombol "Lanjutkan dengan Google", memicu `signInWithPopup(auth, provider)`.
   - Client menerima Firebase ID Token (JWT) yang di-sign oleh Google menggunakan private key Google.
   - Client mengirimkan **hanya** `id_token` ke endpoint backend: `POST /auth/firebase/google`. Client tidak boleh mengirim role, email, atau status verifikasi buatan sendiri.

2. **Server-side RS256 Verification & Security (`app/Services/FirebaseTokenVerifier.php`):**
   - Backend memverifikasi token JWT menggunakan public key Google (sertifikat x509 yang di-cache otomatis dari Google Identity).
   - Validasi ketat diterapkan pada:
     - Header algoritma: wajib `RS256`.
     - Audience (`aud`): wajib cocok dengan `nexamarket-56e7c` (Project ID).
     - Issuer (`iss`): `https://securetoken.google.com/nexamarket-56e7c`.
     - Expiration (`exp`) & Authentication Time (`auth_time`).
     - Subject (`sub`): non-empty Firebase UID.

3. **Anti-Account-Takeover & User Lifecycle (`app/Services/FirebaseAuthService.php`):**
   - Klaim `email_verified` dari Google harus bernilai `true`.
   - Jika `firebase_uid` sudah terdaftar: pengguna langsung di-login.
   - Jika pengguna baru: akun dibuat dengan role default `buyer` (Role seller atau admin hanya dapat diberikan melalui alur verifikasi resmi).
   - Jika email sudah terdaftar dengan password (tanpa `firebase_uid`): akun di-link secara aman ke `firebase_uid` karena email telah diverifikasi oleh Google.
   - Session ID diregenerasi secara otomatis (`$request->session()->regenerate()`) untuk mencegah serangan *session fixation*.

---

### B. Midtrans Snap Sandbox Integration
1. **Token Generation Flow (`POST /orders/{order}/payment/snap`):**
   - Hanya pemilik pesanan (buyer) yang diizinkan memicu pembayaran.
   - Status pesanan wajib `pending` dan total bayar dihitung secara otoritatif di sisi server dari item pesanan.
   - Jika sudah ada transaksi pending yang aktif dengan `snap_token`, sistem mengembalikan token yang sama secara idempoten tanpa membuat duplikasi di gateway.
   - Snap token dan redirect URL disimpan ke dalam database pada tabel `payments`.

2. **Client-side Snap Modal:**
   - Halaman detail pesanan (`/orders/{order}`) memuat SDK `app.sandbox.midtrans.com/snap/snap.js` dengan atribut `data-client-key`.
   - Tombol "Bayar Sekarang (Midtrans)" memanggil endpoint backend via fetch API dan meluncurkan modal `window.snap.pay(snapToken, { onSuccess, onPending, onError, onClose })`.

3. **Webhook Notification Handling (`POST /payments/midtrans/notification`):**
   - Midtrans mengirim HTTP POST payload notifikasi transaksi.
   - **Signature Verification:** SHA512 hash diverifikasi dengan rumus:
     $$\text{hash} = \text{SHA512}(\text{order\_id} + \text{status\_code} + \text{gross\_amount} + \text{server\_key})$$
   - **Database Row Locking:** Baris pembayaran dikunci (`lockForUpdate()`) di dalam database transaction untuk mencegah race condition.
   - **Status Mapping:**
     - `capture` (accept) / `settlement` $\rightarrow$ `paid` (Order & ShopOrders dialihkan ke `paid`).
     - `deny` / `cancel` / `expire` $\rightarrow$ `failed` / `expired` (Stok produk dikembalikan secara atomik dan idempoten jika pesanan berstatus batal/kedaluwarsa).
   - **Out-of-Order Handling:** Jika pembayaran sudah berstatus `paid`, notifikasi yang datang terlambat atau tidak berurutan tidak akan mengubah status menjadi `pending`.
   - **CSRF Exemption:** Rute `/payments/midtrans/notification` dan `/api/payments/midtrans/notification` dikecualikan dari verifikasi CSRF di `bootstrap/app.php`.

---

## 2. Konfigurasi Environment (`.env`)

Tambahkan variabel berikut ke file `.env` lokal:

```dotenv
# =========================================================================
# FIREBASE CONFIGURATION (Google Authentication)
# =========================================================================
# Kredensial Web App didapatkan dari:
# Firebase Console -> Project Settings -> General -> Your apps -> Web app
VITE_FIREBASE_API_KEY=AIzaSy...
VITE_FIREBASE_AUTH_DOMAIN=nexamarket-56e7c.firebaseapp.com
VITE_FIREBASE_PROJECT_ID=nexamarket-56e7c
VITE_FIREBASE_APP_ID=1:123456789:web:...
VITE_FIREBASE_MESSAGING_SENDER_ID=123456789

# Project ID untuk verifikasi server-side (RS256 JWT aud & iss)
FIREBASE_PROJECT_ID=nexamarket-56e7c
# Opsional jika menggunakan Service Account credential file:
FIREBASE_CREDENTIALS=

# =========================================================================
# MIDTRANS CONFIGURATION (Payment Gateway Sandbox)
# =========================================================================
# Dapatkan dari Midtrans MAP: https://dashboard.sandbox.midtrans.com/settings/access_keys
MIDTRANS_SERVER_KEY=SB-Mid-server-...
MIDTRANS_CLIENT_KEY=SB-Mid-client-...
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

> [!IMPORTANT]
> Jangan pernah melakukan commit file `.env` atau credential private key ke repository. Gunakan `.env.example` untuk mencantumkan nama variabel saja.

---

## 3. Menjalankan & Memverifikasi Sistem

### A. Menjalankan Server & Asset Build
Untuk menguji di browser lokal:
```bash
# Terminal 1: Laravel Web Server
php artisan serve

# Terminal 2: Vite Dev Server (Hot Module Replacement untuk Firebase JS)
npm run dev

# Atau build aset produksi:
npm run build
```

### B. Konfigurasi Webhook Midtrans di Lingkungan Lokal
Untuk menerima webhook dari server Sandbox Midtrans ke localhost:
1. Jalankan tunneling (misalnya ngrok atau expose):
   ```bash
   ngrok http 8000
   ```
2. Salin URL publik HTTPS (contoh: `https://abcd-1234.ngrok-free.app`).
3. Masuk ke Dashboard Midtrans Sandbox $\rightarrow$ **Settings** $\rightarrow$ **Configuration**.
4. Set **Payment Notification URL** ke:
   ```
   https://abcd-1234.ngrok-free.app/payments/midtrans/notification
   ```
5. Simpan pengaturan.

### C. Menjalankan Automated Tests
Seluruh pengujian unit dan integrasi dapat dijalankan tanpa koneksi jaringan eksternal karena menggunakan mock gateway dan mock JWT verifier:

```bash
# 1. Uji autentikasi Firebase Google
php artisan test tests/Feature/Auth/FirebaseGoogleAuthenticationTest.php

# 2. Uji integrasi Midtrans Snap & Webhook Notification
php artisan test tests/Feature/MidtransPaymentIntegrationTest.php

# 3. Uji simulasi pembayaran internal QA
php artisan test tests/Feature/MockPaymentSimulationTest.php

# 4. Jalankan seluruh suite tes NexaMarket
php artisan test
```

---

## 4. Rute yang Tersedia

| Method | URI | Nama Rute | Middleware | Keterangan |
|---|---|---|---|---|
| `POST` | `/auth/firebase/google` | `auth.firebase.google` | `web, guest` | Endpoint pertukaran Firebase ID Token |
| `POST` | `/orders/{order}/payment/snap` | `orders.payment.snap` | `web, auth` | Generate / retrieve Snap token |
| `POST` | `/payments/midtrans/notification` | `payments.midtrans.notification` | `web` (CSRF exempt) | Webhook notifikasi Midtrans |
| `POST` | `/api/payments/midtrans/notification` | `api.payments.midtrans.notification` | `api` (CSRF exempt) | Alternatif endpoint webhook API |
| `POST` | `/orders/{order}/payment/simulate` | `orders.payment.simulate` | `web, auth` | QA testing simulation endpoint |

---

## 5. Checklist Kesiapan Produksi (Go-Live)

Sebelum beralih dari Sandbox ke Production di Midtrans & Firebase:
- [ ] Ubah `MIDTRANS_IS_PRODUCTION=true` di environment production.
- [ ] Ganti `MIDTRANS_SERVER_KEY` dan `MIDTRANS_CLIENT_KEY` dengan Production Keys dari Midtrans.
- [ ] Di Firebase Console: tambahkan domain produksi NexaMarket ke daftar **Authorized Domains** pada Authentication Settings.
- [ ] Pastikan Webhook URL di Midtrans Production MAP mengarah ke domain produksi NexaMarket via HTTPS valid.
- [ ] Pastikan queue worker berjalan jika notifikasi atau email order dikirim secara asinkron.
