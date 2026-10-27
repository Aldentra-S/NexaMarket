<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MarketplaceDemoSeeder extends Seeder
{
    /**
     * Default password for all seeded demo accounts in local environment.
     */
    public const DEMO_PASSWORD = 'password123';

    /**
     * Run the marketplace demo database seeds.
     */
    public function run(): void
    {
        // 1. Environmental safety guard: local and testing environments only.
        if (! app()->isLocal() && ! app()->runningUnitTests()) {
            $msg = 'MarketplaceDemoSeeder hanya diizinkan untuk lingkungan development lokal atau testing!';
            if ($this->command) {
                $this->command->error($msg);
            }
            throw new RuntimeException($msg);
        }

        if ($this->command) {
            $this->command->info('Memulai seeding data dummy Marketplace NexaMarket (Idempotent)...');
        }

        // 2. Seed Categories
        $categories = $this->seedCategories();

        // 3. Seed Sellers & Shops
        $shops = $this->seedSellersAndShops();

        // 4. Seed Products and Local Image Placeholders
        $this->seedProducts($categories, $shops);

        if ($this->command) {
            $this->command->newLine();
            $this->command->info('✅ MarketplaceDemoSeeder berhasil dijalankan!');
            $this->command->table(
                ['Entitas', 'Total di Database'],
                [
                    ['Kategori Aktif', Category::count()],
                    ['Akun Demo Seller', User::where('email', 'like', 'seller.%@nexamarket.test')->count()],
                    ['Toko Disetujui (Approved)', Shop::where('status', ShopStatus::APPROVED)->count()],
                    ['Toko Menunggu (Pending)', Shop::where('status', ShopStatus::PENDING)->count()],
                    ['Produk Ditayangkan (Katalog)', Product::published()->forActiveShop()->count()],
                    ['Total Produk Terdaftar', Product::count()],
                ]
            );
        }
    }

    /**
     * Seed or update official marketplace categories.
     *
     * @return array<string, Category>
     */
    protected function seedCategories(): array
    {
        $categoriesData = [
            'elektronik-komputer' => [
                'name' => 'Elektronik & Komputer',
                'description' => 'Perangkat komputer, periferal gaming, monitor, dan aksesori audio berkualitas tinggi.',
            ],
            'fashion-aksesori' => [
                'name' => 'Fashion & Aksesori',
                'description' => 'Pakaian harian, jaket, aksesori gaya hidup, dan fashion kontemporer.',
            ],
            'rumah-perlengkapan-meja' => [
                'name' => 'Rumah & Perlengkapan Meja',
                'description' => 'Peralatan dekorasi interior, lampu meja, organizer kerja, dan kebutuhan rumah tangga.',
            ],
            'alat-tulis-belajar' => [
                'name' => 'Alat Tulis & Belajar',
                'description' => 'Buku catatan eksklusif, pulpen premium, alat gambar, dan perlengkapan studi.',
            ],
            'tas-kebutuhan-harian' => [
                'name' => 'Tas & Kebutuhan Harian',
                'description' => 'Ransel komuter, tas selempang, dompet modular, dan perlengkapan bepergian.',
            ],
        ];

        $seededCategories = [];

        foreach ($categoriesData as $slug => $data) {
            $category = Category::firstOrNew(['slug' => $slug]);
            $category->forceFill([
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'],
                'is_active' => true,
            ])->save();

            $seededCategories[$slug] = $category;
        }

        return $seededCategories;
    }

    /**
     * Seed or update demo sellers and their respective shops.
     *
     * @return array<string, Shop>
     */
    protected function seedSellersAndShops(): array
    {
        $sellersData = [
            'pixelgear-store' => [
                'user_name' => 'Budi PixelGear',
                'email' => 'seller.pixelgear@nexamarket.test',
                'role' => UserRole::SELLER,
                'shop_name' => 'PixelGear Store',
                'shop_slug' => 'pixelgear-store',
                'shop_description' => 'Pusat perlengkapan komputer, keyboard mekanikal, monitor, dan periferal gaming resmi dengan garansi terpercaya.',
                'shop_phone' => '081234567891',
                'shop_address' => 'Harco Mangga Dua Blok B Lt. 2 No. 15, Sawah Besar, Jakarta Pusat',
                'shop_status' => ShopStatus::APPROVED,
                'shop_approved_at' => now()->subDays(15),
            ],
            'karsa-outfit' => [
                'user_name' => 'Karsa Pratama',
                'email' => 'seller.karsa@nexamarket.test',
                'role' => UserRole::SELLER,
                'shop_name' => 'Karsa Outfit',
                'shop_slug' => 'karsa-outfit',
                'shop_description' => 'Brand fashion lokal kontemporer dengan bahan katun combed premium dan desain timeless untuk gaya harian Anda.',
                'shop_phone' => '081234567892',
                'shop_address' => 'Jl. Riau No. 88, Citarum, Bandung Wetan, Kota Bandung',
                'shop_status' => ShopStatus::APPROVED,
                'shop_approved_at' => now()->subDays(10),
            ],
            'ruangrapi-living' => [
                'user_name' => 'Rapi Handoko',
                'email' => 'seller.ruangrapi@nexamarket.test',
                'role' => UserRole::SELLER,
                'shop_name' => 'RuangRapi Living',
                'shop_slug' => 'ruangrapi-living',
                'shop_description' => 'Kurasi perabot minimalis, organizer meja kerja, dan dekorasi estetik untuk rumah serta ruang kerja yang produktif.',
                'shop_phone' => '081234567893',
                'shop_address' => 'BSD Green Office Park Kav. 7, Cisauk, Tangerang Selatan',
                'shop_status' => ShopStatus::APPROVED,
                'shop_approved_at' => now()->subDays(7),
            ],
            'bukukita-stationery' => [
                'user_name' => 'Dewi BukuKita',
                'email' => 'seller.bukukita@nexamarket.test',
                'role' => UserRole::SELLER,
                'shop_name' => 'BukuKita Stationery',
                'shop_slug' => 'bukukita-stationery',
                'shop_description' => 'Menyediakan buku catatan jilid benang, pena kaligrafi, jurnal perencana, dan perlengkapan kantor berkualitas tinggi.',
                'shop_phone' => '081234567894',
                'shop_address' => 'Jl. Kaliurang KM 5.2 No. 42, Sleman, D.I. Yogyakarta',
                'shop_status' => ShopStatus::APPROVED,
                'shop_approved_at' => now()->subDays(3),
            ],
            'dailycarry-goods' => [
                'user_name' => 'Dika DailyCarry',
                'email' => 'seller.dailycarry@nexamarket.test',
                'role' => UserRole::BUYER, // Onboarding belum disetujui -> perannya tetap Buyer
                'shop_name' => 'DailyCarry Goods',
                'shop_slug' => 'dailycarry-goods',
                'shop_description' => 'Produsen tas kanvas tahan air, ransel komuter ergonomis, dan dompet modular untuk aktivitas urban harian.',
                'shop_phone' => '081234567895',
                'shop_address' => 'Jl. Tebet Raya No. 45, Tebet Barat, Jakarta Selatan',
                'shop_status' => ShopStatus::PENDING, // Pending approval untuk menguji workflow Admin
                'shop_approved_at' => null,
            ],
        ];

        $seededShops = [];

        foreach ($sellersData as $key => $data) {
            // Idempotent user creation/update
            $user = User::firstOrNew(['email' => $data['email']]);
            $user->forceFill([
                'name' => $data['user_name'],
                'email' => $data['email'],
                'password' => Hash::make(self::DEMO_PASSWORD),
                'email_verified_at' => $user->email_verified_at ?? now(),
                'role' => $data['role'],
            ])->save();

            // Idempotent shop creation/update
            $shop = Shop::firstOrNew(['slug' => $data['shop_slug']]);
            $shop->forceFill([
                'user_id' => $user->id,
                'name' => $data['shop_name'],
                'slug' => $data['shop_slug'],
                'description' => $data['shop_description'],
                'phone' => $data['shop_phone'],
                'address' => $data['shop_address'],
                'status' => $data['shop_status'],
                'approved_at' => $data['shop_approved_at'],
                'rejection_reason' => null,
                'suspension_reason' => null,
                'suspended_at' => null,
            ])->save();

            $seededShops[$key] = $shop;
        }

        return $seededShops;
    }

    /**
     * Seed or update demo products, including local SVG artwork.
     *
     * @param  array<string, Category>  $categories
     * @param  array<string, Shop>  $shops
     */
    protected function seedProducts(array $categories, array $shops): void
    {
        $productsData = [
            // --- PixelGear Store (Approved - 5 Produk) ---
            [
                'shop_key' => 'pixelgear-store',
                'category_slug' => 'elektronik-komputer',
                'name' => 'NexaMechanical RGB Keyboard 75%',
                'slug' => 'nexamechanical-rgb-keyboard-75',
                'price' => 649000,
                'stock' => 25,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Keyboard mekanikal 75% hot-swappable dengan switch linear gateron yellow pre-lubed, keycaps PBT doubleshot, dan konektivitas triple mode (Bluetooth 5.0, 2.4GHz wireless, Type-C). Cocok untuk produktivitas kerja dan gaming kompetitif.',
                'image_type' => 'keyboard',
                'accent_color' => '#3B82F6',
                'bg_start' => '#F8FAFC',
                'bg_end' => '#EFF6FF',
            ],
            [
                'shop_key' => 'pixelgear-store',
                'category_slug' => 'elektronik-komputer',
                'name' => 'ProGlide Wireless Gaming Mouse',
                'slug' => 'proglide-wireless-gaming-mouse',
                'price' => 389000,
                'stock' => 30,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Mouse nirkabel ultra-ringan 58 gram dengan sensor optik presisi 26.000 DPI, koneksi 2.4GHz bebas lag, baterai tahan hingga 80 jam pemakaian, dan PTFE feet berkualitas tinggi.',
                'image_type' => 'mouse',
                'accent_color' => '#2563EB',
                'bg_start' => '#F8FAFC',
                'bg_end' => '#EFF6FF',
            ],
            [
                'shop_key' => 'pixelgear-store',
                'category_slug' => 'elektronik-komputer',
                'name' => 'ApexSound Spatial Wireless Headset',
                'slug' => 'apexsound-spatial-wireless-headset',
                'price' => 799000,
                'stock' => 18,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Headset audio premium dengan driver 50mm dynamic, dukungan 7.1 virtual surround sound, mikrofon peredam bising ENC, dan bantalan telinga memory foam yang sejuk.',
                'image_type' => 'headset',
                'accent_color' => '#1D4ED8',
                'bg_start' => '#F8FAFC',
                'bg_end' => '#EFF6FF',
            ],
            [
                'shop_key' => 'pixelgear-store',
                'category_slug' => 'elektronik-komputer',
                'name' => 'UltraView 27 Inch QHD 165Hz Monitor',
                'slug' => 'ultraview-27-inch-qhd-165hz-monitor',
                'price' => 2650000,
                'stock' => 10,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Monitor kerja dan gaming IPS 27 inci beresolusi Quad HD (2560x1440), refresh rate mulus 165Hz, cakupan warna 99% sRGB, dan stand ergonomis yang dapat diatur ketinggiannya.',
                'image_type' => 'monitor',
                'accent_color' => '#1E40AF',
                'bg_start' => '#F8FAFC',
                'bg_end' => '#EFF6FF',
            ],
            [
                'shop_key' => 'pixelgear-store',
                'category_slug' => 'elektronik-komputer',
                'name' => 'StreamClear 4K Ultra HD Webcam',
                'slug' => 'streamclear-4k-ultra-hd-webcam',
                'price' => 520000,
                'stock' => 22,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Kamera web resolusi 4K dengan sensor Sony Starvis, lensa autofokus sudut lebar 90 derajat, dual mikrofon stereo noise-canceling, serta penutup privasi fisik terintegrasi.',
                'image_type' => 'webcam',
                'accent_color' => '#3B82F6',
                'bg_start' => '#F8FAFC',
                'bg_end' => '#EFF6FF',
            ],

            // --- Karsa Outfit (Approved - 5 Produk) ---
            [
                'shop_key' => 'karsa-outfit',
                'category_slug' => 'fashion-aksesori',
                'name' => 'Oversized Heavyweight Cotton T-Shirt',
                'slug' => 'oversized-heavyweight-cotton-t-shirt',
                'price' => 135000,
                'stock' => 45,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Kaos model boxy oversized berbahan katun combed 20s premium seberat 220 GSM. Nyaman, tebal tidak menerawang, menyerap keringat dengan jahitan rantai ganda yang kokoh.',
                'image_type' => 'tshirt',
                'accent_color' => '#EA580C',
                'bg_start' => '#FFF7ED',
                'bg_end' => '#FFEDD5',
            ],
            [
                'shop_key' => 'karsa-outfit',
                'category_slug' => 'fashion-aksesori',
                'name' => 'Urban Canvas Utility Overshirt',
                'slug' => 'urban-canvas-utility-overshirt',
                'price' => 275000,
                'stock' => 20,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Jaket kemeja santai bergaya workwear dengan 4 saku multifungsi, kancing snap metal hitam, berbahan kanvas katun twill tahan lama untuk penampilan kasual berlapis.',
                'image_type' => 'overshirt',
                'accent_color' => '#C2410C',
                'bg_start' => '#FFF7ED',
                'bg_end' => '#FFEDD5',
            ],
            [
                'shop_key' => 'karsa-outfit',
                'category_slug' => 'fashion-aksesori',
                'name' => 'Relaxed Tapered Pleated Chino Pants',
                'slug' => 'relaxed-tapered-pleated-chino-pants',
                'price' => 219000,
                'stock' => 32,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Celana panjang chino berpotongan relaxed tapered dengan detail pleat depan klasik dan pinggang elastis tersembunyi, memberikan siluet rapi namun tetap leluasa bergerak.',
                'image_type' => 'chino',
                'accent_color' => '#B45309',
                'bg_start' => '#FFFBEB',
                'bg_end' => '#FEF3C7',
            ],
            [
                'shop_key' => 'karsa-outfit',
                'category_slug' => 'fashion-aksesori',
                'name' => 'Classic Minimalist Chrono Leather Watch',
                'slug' => 'classic-minimalist-chrono-leather-watch',
                'price' => 425000,
                'stock' => 15,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Jam tangan analog bergaya minimalis dengan diameter case 40mm, dial bertekstur sunburst, kaca safir tahan gores, dan strap kulit sapi asli berwarna cokelat tembakau.',
                'image_type' => 'watch',
                'accent_color' => '#9A3412',
                'bg_start' => '#FFF7ED',
                'bg_end' => '#FFEDD5',
            ],
            [
                'shop_key' => 'karsa-outfit',
                'category_slug' => 'tas-kebutuhan-harian',
                'name' => 'Everyday Heavy Canvas Tote Bag',
                'slug' => 'everyday-heavy-canvas-tote-bag',
                'price' => 145000,
                'stock' => 35,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Tas jinjing kanvas tebal 14oz dengan kompartemen laptop 14 inci berlapis busa, resleting utama YKK yang aman, dan saku botol minum di bagian samping dalam.',
                'image_type' => 'tote',
                'accent_color' => '#EA580C',
                'bg_start' => '#FFF7ED',
                'bg_end' => '#FED7AA',
            ],

            // --- RuangRapi Living (Approved - 5 Produk) ---
            [
                'shop_key' => 'ruangrapi-living',
                'category_slug' => 'rumah-perlengkapan-meja',
                'name' => 'Nordica Solid Wood Monitor Riser Stand',
                'slug' => 'nordica-solid-wood-monitor-riser-stand',
                'price' => 245000,
                'stock' => 19,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Dudukan monitor kayu jati belanda solid dengan sentuhan natural oil doff, menaikkan posisi layar setinggi mata serta menyisakan ruang lega di bawah untuk keyboard dan mouse.',
                'image_type' => 'stand',
                'accent_color' => '#059669',
                'bg_start' => '#F0FDF4',
                'bg_end' => '#DCFCE7',
            ],
            [
                'shop_key' => 'ruangrapi-living',
                'category_slug' => 'rumah-perlengkapan-meja',
                'name' => 'AuraGlow Desk Screenbar Light',
                'slug' => 'auraglow-desk-screenbar-light',
                'price' => 285000,
                'stock' => 24,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Lampu gantung monitor pintar dengan teknologi optik asimetris tanpa silau pada layar, pengaturan suhu warna stepless (3000K-6500K), dan sensor sentuh responsif.',
                'image_type' => 'screenbar',
                'accent_color' => '#10B981',
                'bg_start' => '#F0FDF4',
                'bg_end' => '#DCFCE7',
            ],
            [
                'shop_key' => 'ruangrapi-living',
                'category_slug' => 'rumah-perlengkapan-meja',
                'name' => 'Ergonomic Memory Foam Lumbar Support',
                'slug' => 'ergonomic-memory-foam-lumbar-support',
                'price' => 175000,
                'stock' => 35,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Bantal penyangga punggung kursi kerja bermaterial high-density memory foam yang menyesuaikan lekuk tulang belakang, dibalut kain cover mesh 3D bernapas yang dapat dicuci.',
                'image_type' => 'lumbar',
                'accent_color' => '#047857',
                'bg_start' => '#F0FDF4',
                'bg_end' => '#DCFCE7',
            ],
            [
                'shop_key' => 'ruangrapi-living',
                'category_slug' => 'rumah-perlengkapan-meja',
                'name' => 'Ceramic Matte Minimalist Coffee Tumbler 500ml',
                'slug' => 'ceramic-matte-minimalist-coffee-tumbler-500ml',
                'price' => 149000,
                'stock' => 40,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Tumbler kopi termal stainless steel SUS304 dengan lapisan keramik anti-bau pada bagian dalam, menjaga suhu panas hingga 8 jam dan suhu es dingin hingga 12 jam.',
                'image_type' => 'tumbler',
                'accent_color' => '#065F46',
                'bg_start' => '#F0FDF4',
                'bg_end' => '#DCFCE7',
            ],
            [
                'shop_key' => 'ruangrapi-living',
                'category_slug' => 'rumah-perlengkapan-meja',
                'name' => 'Modular Bamboo Desk Organizer Tray',
                'slug' => 'modular-bamboo-desk-organizer-tray',
                'price' => 115000,
                'stock' => 27,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Set baki meja modular berbahan bambu ramah lingkungan dengan 3 kompartemen serbaguna untuk menata alat tulis, smartphone, kartu nama, dan kabel agar meja selalu rapi.',
                'image_type' => 'tray',
                'accent_color' => '#059669',
                'bg_start' => '#F0FDF4',
                'bg_end' => '#DCFCE7',
            ],

            // --- BukuKita Stationery (Approved - 5 Produk) ---
            [
                'shop_key' => 'bukukita-stationery',
                'category_slug' => 'alat-tulis-belajar',
                'name' => 'Threadbound Dotted Hardcover Journal 160 GSM',
                'slug' => 'threadbound-dotted-hardcover-journal-160-gsm',
                'price' => 128000,
                'stock' => 38,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Buku jurnal catatan bersampul kokoh dengan kertas 160 GSM bebas asam berpola titik (dot grid), tahan bleed-through untuk pena tinta dan highlighter, jilid jahit benang buka datar 180°.',
                'image_type' => 'journal',
                'accent_color' => '#7C3AED',
                'bg_start' => '#F5F3FF',
                'bg_end' => '#EDE9FE',
            ],
            [
                'shop_key' => 'bukukita-stationery',
                'category_slug' => 'alat-tulis-belajar',
                'name' => 'Brass Elegance Fountain Pen Fine Nib',
                'slug' => 'brass-elegance-fountain-pen-fine-nib',
                'price' => 195000,
                'stock' => 16,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Pena tinta bertubuh kuningan solid dengan bobot seimbang, mata pena fine nib 0.5mm presisi yang mengalir lancar tanpa macet, lengkap dengan konverter pompa tinta internasional.',
                'image_type' => 'fountainpen',
                'accent_color' => '#6D28D9',
                'bg_start' => '#F5F3FF',
                'bg_end' => '#EDE9FE',
            ],
            [
                'shop_key' => 'bukukita-stationery',
                'category_slug' => 'alat-tulis-belajar',
                'name' => 'Premium Vegan Leather Roll Pencil Case',
                'slug' => 'premium-vegan-leather-roll-pencil-case',
                'price' => 85000,
                'stock' => 42,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Tempat pensil gulung berbahan kulit vegan lembut dengan 5 kompartemen elastis untuk melindungi koleksi pena berharga dari benturan saat dibawa bepergian.',
                'image_type' => 'pencilcase',
                'accent_color' => '#5B21B6',
                'bg_start' => '#F5F3FF',
                'bg_end' => '#EDE9FE',
            ],
            [
                'shop_key' => 'bukukita-stationery',
                'category_slug' => 'alat-tulis-belajar',
                'name' => 'Vegan Suede Waterproof Desk Mat 80x40cm',
                'slug' => 'vegan-suede-waterproof-desk-mat-80x40cm',
                'price' => 110000,
                'stock' => 33,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Alas meja kerja dua sisi berukuran 80x40cm, permukaan atas kulit sintetis tahan cipratan air dan permukaan bawah bahan suede lembut penahan geser untuk pergerakan mouse yang halus.',
                'image_type' => 'deskmat',
                'accent_color' => '#7C3AED',
                'bg_start' => '#F5F3FF',
                'bg_end' => '#EDE9FE',
            ],
            [
                'shop_key' => 'bukukita-stationery',
                'category_slug' => 'alat-tulis-belajar',
                'name' => 'Undated Goal & Habit Mastery Daily Planner',
                'slug' => 'undated-goal-habit-mastery-daily-planner',
                'price' => 95000,
                'stock' => 50,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Agenda perencana produktivitas tanpa tanggal untuk 6 bulan pemakaian, dilengkapi lembar habit tracker mingguan, evaluasi prioritas berkala, dan saku dokumen rahasia di bagian belakang.',
                'image_type' => 'planner',
                'accent_color' => '#4C1D95',
                'bg_start' => '#F5F3FF',
                'bg_end' => '#EDE9FE',
            ],

            // --- DailyCarry Goods (PENDING - 1 Produk Pengujian Proteksi Katalog) ---
            [
                'shop_key' => 'dailycarry-goods',
                'category_slug' => 'tas-kebutuhan-harian',
                'name' => 'Venture Weatherproof Commuter Backpack',
                'slug' => 'venture-weatherproof-commuter-backpack',
                'price' => 489000,
                'stock' => 20,
                'status' => ProductStatus::PUBLISHED,
                'description' => 'Ransel harian berkapasitas 22 liter dengan bahan Cordura 600D tahan air, kompartemen laptop 15.6 inci suspensi busa, tali bahu ergonomis, dan saku tersembunyi anti-maling.',
                'image_type' => 'backpack',
                'accent_color' => '#EA580C',
                'bg_start' => '#FFF7ED',
                'bg_end' => '#FED7AA',
            ],
        ];

        foreach ($productsData as $prod) {
            $shop = $shops[$prod['shop_key']];
            $category = $categories[$prod['category_slug']];

            // 1. Idempotent Product creation/update
            $product = Product::firstOrNew(['slug' => $prod['slug']]);
            $product->forceFill([
                'shop_id' => $shop->id,
                'category_id' => $category->id,
                'name' => $prod['name'],
                'slug' => $prod['slug'],
                'description' => $prod['description'],
                'price' => $prod['price'],
                'stock' => $prod['stock'],
                'status' => $prod['status'],
            ])->save();

            // 2. Ensure local SVG product artwork exists in public disk
            $imageFilename = "demo-{$prod['slug']}.svg";
            $this->ensureProductSvgImage(
                filename: $imageFilename,
                title: $prod['name'],
                categoryName: $category->name,
                shopName: $shop->name,
                price: $prod['price'],
                imageType: $prod['image_type'],
                accentColor: $prod['accent_color'],
                bgStart: $prod['bg_start'],
                bgEnd: $prod['bg_end']
            );

            // 3. Idempotent primary ProductImage record
            ProductImage::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'sort_order' => 0,
                ],
                [
                    'file_path' => "products/{$imageFilename}",
                    'is_primary' => true,
                ]
            );
        }
    }

    /**
     * Generate and store a clean, local vector SVG product card in storage/app/public/products.
     */
    protected function ensureProductSvgImage(
        string $filename,
        string $title,
        string $categoryName,
        string $shopName,
        float|int $price,
        string $imageType,
        string $accentColor,
        string $bgStart,
        string $bgEnd
    ): void {
        $storagePath = "products/{$filename}";

        // Always generate/refresh SVG file if missing
        if (! Storage::disk('public')->exists($storagePath)) {
            $iconSvg = $this->getIconSvgSnippet($imageType, $accentColor);
            $formattedPrice = 'Rp ' . number_format($price, 0, ',', '.');
            $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $safeCat = htmlspecialchars(strtoupper($categoryName), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $safeShop = htmlspecialchars($shopName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" width="600" height="600">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$bgStart}"/>
      <stop offset="100%" stop-color="{$bgEnd}"/>
    </linearGradient>
    <filter id="shadow" x="-10%" y="-10%" width="120%" height="120%">
      <feDropShadow dx="0" dy="8" stdDeviation="10" flood-color="#0F172A" flood-opacity="0.08"/>
    </filter>
  </defs>
  <rect width="600" height="600" rx="32" fill="url(#bg)"/>
  
  <circle cx="300" cy="230" r="140" fill="{$accentColor}" fill-opacity="0.08"/>
  <circle cx="300" cy="230" r="100" fill="{$accentColor}" fill-opacity="0.12"/>
  
  <g filter="url(#shadow)">
    <rect x="190" y="120" width="220" height="220" rx="36" fill="#FFFFFF"/>
  </g>
  
  <g transform="translate(240, 170)">
    {$iconSvg}
  </g>
  
  <g transform="translate(300, 64)">
    <rect x="-130" y="0" width="260" height="32" rx="16" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="1.5"/>
    <text x="0" y="21" text-anchor="middle" font-family="system-ui, -apple-system, sans-serif" font-size="11" font-weight="700" fill="{$accentColor}" letter-spacing="1">{$safeCat}</text>
  </g>
  
  <g transform="translate(300, 395)">
    <text x="0" y="0" text-anchor="middle" font-family="system-ui, -apple-system, sans-serif" font-size="13" font-weight="600" fill="#64748B" letter-spacing="0.5">{$safeShop}</text>
    
    <text x="0" y="32" text-anchor="middle" font-family="system-ui, -apple-system, sans-serif" font-size="17" font-weight="800" fill="#0F172A">{$safeTitle}</text>
    
    <g transform="translate(0, 58)">
      <rect x="-85" y="0" width="170" height="38" rx="19" fill="#EA580C"/>
      <text x="0" y="24" text-anchor="middle" font-family="system-ui, -apple-system, sans-serif" font-size="14" font-weight="800" fill="#FFFFFF">{$formattedPrice}</text>
    </g>
    
    <text x="0" y="132" text-anchor="middle" font-family="system-ui, -apple-system, sans-serif" font-size="10" font-weight="700" fill="#94A3B8" letter-spacing="1.5">NEXAMARKET CURATED DEMO</text>
  </g>
</svg>
SVG;

            Storage::disk('public')->put($storagePath, $svg);
        }
    }

    /**
     * Vector icon path definitions for products.
     */
    protected function getIconSvgSnippet(string $type, string $accent): string
    {
        return match ($type) {
            'keyboard' => <<<XML
<rect x="5" y="20" width="110" height="75" rx="8" fill="none" stroke="{$accent}" stroke-width="6"/>
<rect x="20" y="35" width="16" height="12" rx="2" fill="{$accent}"/>
<rect x="42" y="35" width="16" height="12" rx="2" fill="{$accent}"/>
<rect x="64" y="35" width="16" height="12" rx="2" fill="{$accent}"/>
<rect x="86" y="35" width="14" height="12" rx="2" fill="{$accent}"/>
<rect x="20" y="53" width="16" height="12" rx="2" fill="{$accent}"/>
<rect x="42" y="53" width="16" height="12" rx="2" fill="{$accent}"/>
<rect x="64" y="53" width="16" height="12" rx="2" fill="{$accent}"/>
<rect x="86" y="53" width="14" height="12" rx="2" fill="{$accent}"/>
<rect x="35" y="71" width="50" height="10" rx="2" fill="{$accent}"/>
XML,
            'mouse' => <<<XML
<path d="M35 15 C35 5 50 0 60 0 C70 0 85 5 85 15 L85 95 C85 110 70 120 60 120 C50 120 35 110 35 95 Z" fill="none" stroke="{$accent}" stroke-width="6"/>
<line x1="60" y1="0" x2="60" y2="45" stroke="{$accent}" stroke-width="5"/>
<rect x="55" y="20" width="10" height="22" rx="4" fill="{$accent}"/>
XML,
            'headset' => <<<XML
<path d="M20 70 A40 40 0 0 1 100 70" fill="none" stroke="{$accent}" stroke-width="8" stroke-linecap="round"/>
<rect x="10" y="65" width="22" height="35" rx="8" fill="{$accent}"/>
<rect x="88" y="65" width="22" height="35" rx="8" fill="{$accent}"/>
<path d="M25 95 C25 110 50 115 65 115" fill="none" stroke="{$accent}" stroke-width="5" stroke-linecap="round"/>
<circle cx="68" cy="115" r="5" fill="{$accent}"/>
XML,
            'monitor' => <<<XML
<rect x="12" y="10" width="96" height="70" rx="8" fill="none" stroke="{$accent}" stroke-width="6"/>
<line x1="60" y1="80" x2="60" y2="105" stroke="{$accent}" stroke-width="6"/>
<line x1="35" y1="105" x2="85" y2="105" stroke="{$accent}" stroke-width="6" stroke-linecap="round"/>
XML,
            'webcam' => <<<XML
<circle cx="60" cy="50" r="38" fill="none" stroke="{$accent}" stroke-width="6"/>
<circle cx="60" cy="50" r="22" fill="none" stroke="{$accent}" stroke-width="4"/>
<circle cx="60" cy="50" r="10" fill="{$accent}"/>
<path d="M35 105 L45 88 L75 88 L85 105 Z" fill="{$accent}"/>
XML,
            'tshirt' => <<<XML
<path d="M35 20 L48 35 C55 30 65 30 72 35 L85 20 L105 40 L92 53 L92 105 L28 105 L28 53 L15 40 Z" fill="none" stroke="{$accent}" stroke-width="6" stroke-linejoin="round"/>
XML,
            'overshirt' => <<<XML
<path d="M35 20 L48 35 L60 20 L72 35 L85 20 L105 40 L92 53 L92 105 L28 105 L28 53 L15 40 Z" fill="none" stroke="{$accent}" stroke-width="6" stroke-linejoin="round"/>
<line x1="60" y1="20" x2="60" y2="105" stroke="{$accent}" stroke-width="4"/>
<circle cx="53" cy="45" r="3.5" fill="{$accent}"/>
<circle cx="53" cy="65" r="3.5" fill="{$accent}"/>
<circle cx="53" cy="85" r="3.5" fill="{$accent}"/>
XML,
            'chino' => <<<XML
<path d="M28 15 L92 15 L98 105 L66 105 L60 55 L54 105 L22 105 Z" fill="none" stroke="{$accent}" stroke-width="6" stroke-linejoin="round"/>
XML,
            'watch' => <<<XML
<circle cx="60" cy="60" r="32" fill="none" stroke="{$accent}" stroke-width="6"/>
<rect x="45" y="8" width="30" height="20" rx="4" fill="{$accent}"/>
<rect x="45" y="92" width="30" height="20" rx="4" fill="{$accent}"/>
<line x1="60" y1="60" x2="60" y2="42" stroke="{$accent}" stroke-width="4" stroke-linecap="round"/>
<line x1="60" y1="60" x2="74" y2="60" stroke="{$accent}" stroke-width="4" stroke-linecap="round"/>
XML,
            'tote', 'backpack' => <<<XML
<path d="M32 45 C32 28 48 22 60 22 C72 22 88 28 88 45 L90 105 L30 105 Z" fill="none" stroke="{$accent}" stroke-width="6" stroke-linejoin="round"/>
<path d="M46 22 L46 12 C46 9 52 7 60 7 C68 7 74 9 74 12 L74 22" fill="none" stroke="{$accent}" stroke-width="4"/>
<rect x="42" y="65" width="36" height="28" rx="4" fill="none" stroke="{$accent}" stroke-width="4"/>
XML,
            'stand' => <<<XML
<rect x="12" y="32" width="96" height="20" rx="4" fill="{$accent}"/>
<rect x="22" y="52" width="12" height="32" rx="2" fill="{$accent}"/>
<rect x="86" y="52" width="12" height="32" rx="2" fill="{$accent}"/>
XML,
            'screenbar' => <<<XML
<rect x="15" y="25" width="90" height="12" rx="6" fill="{$accent}"/>
<path d="M60 37 L60 68 L78 84" fill="none" stroke="{$accent}" stroke-width="5" stroke-linecap="round"/>
<line x1="25" y1="52" x2="20" y2="72" stroke="{$accent}" stroke-width="3" stroke-dasharray="3 3"/>
<line x1="60" y1="52" x2="60" y2="76" stroke="{$accent}" stroke-width="3" stroke-dasharray="3 3"/>
<line x1="95" y1="52" x2="100" y2="72" stroke="{$accent}" stroke-width="3" stroke-dasharray="3 3"/>
XML,
            'lumbar' => <<<XML
<path d="M25 35 C45 30 75 30 95 35 C105 55 105 75 95 95 C75 90 45 90 25 95 C15 75 15 55 25 35 Z" fill="none" stroke="{$accent}" stroke-width="6" stroke-linejoin="round"/>
<line x1="45" y1="48" x2="75" y2="48" stroke="{$accent}" stroke-width="4" stroke-linecap="round"/>
XML,
            'tumbler' => <<<XML
<path d="M34 25 L86 25 L80 100 C80 106 75 110 68 110 L52 110 C45 110 40 106 40 100 Z" fill="none" stroke="{$accent}" stroke-width="6"/>
<rect x="30" y="14" width="60" height="11" rx="3" fill="{$accent}"/>
XML,
            'tray' => <<<XML
<rect x="18" y="32" width="84" height="64" rx="8" fill="none" stroke="{$accent}" stroke-width="6"/>
<line x1="60" y1="32" x2="60" y2="96" stroke="{$accent}" stroke-width="4"/>
<line x1="18" y1="64" x2="60" y2="64" stroke="{$accent}" stroke-width="4"/>
XML,
            'journal' => <<<XML
<rect x="25" y="15" width="70" height="90" rx="6" fill="none" stroke="{$accent}" stroke-width="6"/>
<line x1="40" y1="15" x2="40" y2="105" stroke="{$accent}" stroke-width="5"/>
<line x1="50" y1="38" x2="80" y2="38" stroke="{$accent}" stroke-width="3" stroke-linecap="round"/>
<line x1="50" y1="54" x2="80" y2="54" stroke="{$accent}" stroke-width="3" stroke-linecap="round"/>
<line x1="50" y1="70" x2="72" y2="70" stroke="{$accent}" stroke-width="3" stroke-linecap="round"/>
XML,
            'fountainpen' => <<<XML
<path d="M38 15 L82 15 L76 75 L60 110 L44 75 Z" fill="none" stroke="{$accent}" stroke-width="6" stroke-linejoin="round"/>
<line x1="60" y1="65" x2="60" y2="105" stroke="{$accent}" stroke-width="4"/>
<line x1="38" y1="45" x2="82" y2="45" stroke="{$accent}" stroke-width="5"/>
XML,
            'pencilcase' => <<<XML
<rect x="18" y="42" width="84" height="38" rx="10" fill="none" stroke="{$accent}" stroke-width="6"/>
<line x1="18" y1="61" x2="102" y2="61" stroke="{$accent}" stroke-width="4" stroke-dasharray="6 3"/>
XML,
            'deskmat' => <<<XML
<rect x="12" y="25" width="96" height="66" rx="8" fill="none" stroke="{$accent}" stroke-width="6"/>
<circle cx="80" cy="58" r="10" fill="{$accent}" fill-opacity="0.35"/>
XML,
            'planner' => <<<XML
<rect x="25" y="15" width="70" height="90" rx="6" fill="none" stroke="{$accent}" stroke-width="6"/>
<line x1="25" y1="32" x2="95" y2="32" stroke="{$accent}" stroke-width="4"/>
<circle cx="45" cy="50" r="4" fill="{$accent}"/>
<circle cx="60" cy="50" r="4" fill="{$accent}"/>
<circle cx="75" cy="50" r="4" fill="{$accent}"/>
<circle cx="45" cy="72" r="4" fill="{$accent}"/>
<circle cx="60" cy="72" r="4" fill="{$accent}"/>
<circle cx="75" cy="72" r="4" fill="{$accent}"/>
XML,
            default => <<<XML
<rect x="20" y="20" width="80" height="80" rx="10" fill="none" stroke="{$accent}" stroke-width="6"/>
XML,
        };
    }
}
