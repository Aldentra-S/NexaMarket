<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Enums\ShopStatus;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Helper to create a seller user with an approved shop.
     */
    private function createSellerWithApprovedShop(): array
    {
        $seller = User::factory()->seller()->create();
        $shop = Shop::factory()->approved()->create([
            'user_id' => $seller->id,
        ]);

        return [$seller, $shop];
    }

    public function test_approved_seller_can_create_product_with_valid_images(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();

        $image1 = UploadedFile::fake()->image('cover.jpg', 600, 600)->size(500);
        $image2 = UploadedFile::fake()->image('detail.webp', 800, 800)->size(700);

        $payload = [
            'name' => 'Meja Kerja Minimalis Kayu Jati',
            'description' => 'Meja kerja kayu jati solid dengan finishing halus dan konstruksi kokoh.',
            'price' => 1250000,
            'stock' => 15,
            'status' => ProductStatus::PUBLISHED->value,
            'images' => [$image1, $image2],
        ];

        $response = $this->actingAs($seller)->post(route('seller.products.store'), $payload);

        $response->assertRedirect(route('seller.products.index'));

        $product = Product::where('name', 'Meja Kerja Minimalis Kayu Jati')->first();
        $this->assertNotNull($product);
        $this->assertCount(2, $product->images);

        $primaryImage = $product->primaryImage;
        $this->assertNotNull($primaryImage);
        $this->assertTrue($primaryImage->is_primary);
        Storage::disk('public')->assertExists($primaryImage->file_path);

        $secondImage = $product->images()->where('is_primary', false)->first();
        $this->assertNotNull($secondImage);
        Storage::disk('public')->assertExists($secondImage->file_path);
    }

    public function test_approved_seller_can_upload_additional_images_to_existing_product(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        $file = UploadedFile::fake()->image('side-view.png', 500, 500)->size(400);

        $response = $this->actingAs($seller)->post(route('seller.products.images.store', $product), [
            'images' => [$file],
        ]);

        $response->assertRedirect();
        $this->assertCount(1, $product->fresh()->images);
        $this->assertTrue($product->fresh()->images->first()->is_primary);
        Storage::disk('public')->assertExists($product->fresh()->images->first()->file_path);
    }

    public function test_image_upload_rejects_invalid_file_types_and_oversized_files(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        // 1. Invalid file format (PDF)
        $invalidFile = UploadedFile::fake()->create('spec.pdf', 300, 'application/pdf');

        $response1 = $this->actingAs($seller)->post(route('seller.products.images.store', $product), [
            'images' => [$invalidFile],
        ]);
        $response1->assertSessionHasErrors(['images.0']);

        // 2. Oversized image (> 2048 KB)
        $oversizedFile = UploadedFile::fake()->image('large.jpg')->size(3000);

        $response2 = $this->actingAs($seller)->post(route('seller.products.images.store', $product), [
            'images' => [$oversizedFile],
        ]);
        $response2->assertSessionHasErrors(['images.0']);

        // Database should have zero images
        $this->assertSame(0, ProductImage::count());
    }

    public function test_product_enforces_maximum_image_count_limit(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        // Pre-create 4 images
        for ($i = 0; $i < 4; $i++) {
            $product->images()->create([
                'file_path' => "products/test-{$i}.jpg",
                'is_primary' => $i === 0,
                'sort_order' => $i,
            ]);
        }

        // Attempt to upload 2 more images (would exceed MAX_PER_PRODUCT = 5)
        $file1 = UploadedFile::fake()->image('img1.jpg');
        $file2 = UploadedFile::fake()->image('img2.jpg');

        $response = $this->actingAs($seller)->post(route('seller.products.images.store', $product), [
            'images' => [$file1, $file2],
        ]);

        $response->assertSessionHasErrors('images');
        $this->assertSame(4, $product->fresh()->images()->count());
    }

    public function test_seller_cannot_manage_images_of_another_sellers_product(): void
    {
        [$sellerA, $shopA] = $this->createSellerWithApprovedShop();
        [$sellerB, $shopB] = $this->createSellerWithApprovedShop();

        $productB = Product::factory()->create(['shop_id' => $shopB->id]);
        $imageB = $productB->images()->create([
            'file_path' => 'products/sellerB-photo.jpg',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $fakeFile = UploadedFile::fake()->image('hacker.jpg');

        // Seller A tries to add image to Product B
        $postResponse = $this->actingAs($sellerA)->post(route('seller.products.images.store', $productB), [
            'images' => [$fakeFile],
        ]);
        $postResponse->assertForbidden();

        // Seller A tries to replace image of Product B
        $putResponse = $this->actingAs($sellerA)->put(route('seller.products.images.update', [$productB, $imageB]), [
            'image' => $fakeFile,
        ]);
        $putResponse->assertForbidden();

        // Seller A tries to set primary image of Product B
        $primaryResponse = $this->actingAs($sellerA)->patch(route('seller.products.images.primary', [$productB, $imageB]));
        $primaryResponse->assertForbidden();

        // Seller A tries to delete image of Product B
        $deleteResponse = $this->actingAs($sellerA)->delete(route('seller.products.images.destroy', [$productB, $imageB]));
        $deleteResponse->assertForbidden();

        // Ensure Seller B's image is intact
        $this->assertDatabaseHas('product_images', ['id' => $imageB->id]);
    }

    public function test_seller_can_set_primary_image_and_reorder_images(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        $img1 = $product->images()->create([
            'file_path' => 'products/img-1.jpg',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $img2 = $product->images()->create([
            'file_path' => 'products/img-2.jpg',
            'is_primary' => false,
            'sort_order' => 1,
        ]);

        // Change primary image to img2
        $responsePrimary = $this->actingAs($seller)->patch(route('seller.products.images.primary', [$product, $img2]));
        $responsePrimary->assertRedirect();

        $img1->refresh();
        $img2->refresh();

        $this->assertFalse($img1->is_primary);
        $this->assertTrue($img2->is_primary);

        // Reorder images
        $responseReorder = $this->actingAs($seller)->patch(route('seller.products.images.reorder', $product), [
            'order' => [$img2->id, $img1->id],
        ]);
        $responseReorder->assertRedirect();

        $img1->refresh();
        $img2->refresh();

        $this->assertSame(1, $img1->sort_order);
        $this->assertSame(0, $img2->sort_order);
    }

    public function test_deleting_image_safely_removes_file_and_promotes_next_primary_if_needed(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        Storage::disk('public')->put('products/photo1.jpg', 'fake-image-content-1');
        Storage::disk('public')->put('products/photo2.jpg', 'fake-image-content-2');

        $img1 = $product->images()->create([
            'file_path' => 'products/photo1.jpg',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $img2 = $product->images()->create([
            'file_path' => 'products/photo2.jpg',
            'is_primary' => false,
            'sort_order' => 1,
        ]);

        // Delete primary image
        $response = $this->actingAs($seller)->delete(route('seller.products.images.destroy', [$product, $img1]));
        $response->assertRedirect();

        // Metadata deleted
        $this->assertDatabaseMissing('product_images', ['id' => $img1->id]);

        // Physical file photo1.jpg deleted from disk
        Storage::disk('public')->assertMissing('products/photo1.jpg');

        // Remaining image img2 promoted to primary
        $img2->refresh();
        $this->assertTrue($img2->is_primary);
        Storage::disk('public')->assertExists('products/photo2.jpg');
    }

    public function test_product_deletion_cleans_up_all_associated_image_files(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $product = Product::factory()->create(['shop_id' => $shop->id]);

        Storage::disk('public')->put('products/p1.jpg', 'image1');
        Storage::disk('public')->put('products/p2.jpg', 'image2');

        $product->images()->create(['file_path' => 'products/p1.jpg', 'is_primary' => true]);
        $product->images()->create(['file_path' => 'products/p2.jpg', 'is_primary' => false]);

        $this->assertCount(2, $product->images);

        // Delete product
        $response = $this->actingAs($seller)->delete(route('seller.products.destroy', $product));
        $response->assertRedirect(route('seller.products.index'));

        // DB records removed
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertSame(0, ProductImage::where('product_id', $product->id)->count());

        // Files on disk removed
        Storage::disk('public')->assertMissing('products/p1.jpg');
        Storage::disk('public')->assertMissing('products/p2.jpg');
    }

    public function test_product_without_image_displays_gracefully_with_placeholder(): void
    {
        [$seller, $shop] = $this->createSellerWithApprovedShop();
        $product = Product::factory()->published()->create([
            'shop_id' => $shop->id,
            'name' => 'Produk Tanpa Foto Eksplisit',
        ]);

        // Catalog Index
        $indexResponse = $this->get(route('products.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Produk Tanpa Foto Eksplisit');
        $indexResponse->assertSee('placeholder-product.svg');

        // Catalog Show
        $showResponse = $this->get(route('products.show', $product->slug));
        $showResponse->assertOk();
        $showResponse->assertSee('Produk Tanpa Foto Eksplisit');
        $showResponse->assertSee('placeholder-product.svg');
    }

    public function test_unapproved_or_suspended_shops_cannot_manage_images(): void
    {
        // 1. Pending shop
        $sellerPending = User::factory()->seller()->create();
        $shopPending = Shop::factory()->create([
            'user_id' => $sellerPending->id,
            'status' => ShopStatus::PENDING,
        ]);
        $product1 = Product::factory()->create(['shop_id' => $shopPending->id]);

        $file = UploadedFile::fake()->image('test.jpg');
        $resPending = $this->actingAs($sellerPending)->post(route('seller.products.images.store', $product1), [
            'images' => [$file],
        ]);
        $resPending->assertForbidden();

        // 2. Suspended shop
        $sellerSuspended = User::factory()->seller()->create();
        $shopSuspended = Shop::factory()->suspended('Pelanggaran konten foto.')->create([
            'user_id' => $sellerSuspended->id,
        ]);
        $product2 = Product::factory()->create(['shop_id' => $shopSuspended->id]);

        $resSuspended = $this->actingAs($sellerSuspended)->post(route('seller.products.images.store', $product2), [
            'images' => [$file],
        ]);
        $resSuspended->assertForbidden();
    }
}
