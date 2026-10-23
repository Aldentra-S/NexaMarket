<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\ReorderProductImagesRequest;
use App\Http\Requests\Seller\StoreProductImageRequest;
use App\Http\Requests\Seller\UpdateProductImageRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Store one or more newly uploaded images for the product.
     */
    public function store(StoreProductImageRequest $request, Product $product): RedirectResponse
    {
        Gate::authorize('update', $product);

        $files = [];
        if ($request->hasFile('images')) {
            $files = array_merge($files, (array) $request->file('images'));
        }
        if ($request->hasFile('image')) {
            $files[] = $request->file('image');
        }

        $uploadedPaths = [];

        try {
            DB::transaction(function () use ($product, $files, &$uploadedPaths) {
                $hasPrimary = $product->images()->where('is_primary', true)->exists();
                $maxSortOrder = (int) $product->images()->max('sort_order');

                foreach ($files as $file) {
                    $path = $file->store('products', 'public');
                    $uploadedPaths[] = $path;

                    $isPrimary = ! $hasPrimary;
                    $maxSortOrder++;

                    $product->images()->create([
                        'file_path' => $path,
                        'is_primary' => $isPrimary,
                        'sort_order' => $maxSortOrder,
                    ]);

                    if ($isPrimary) {
                        $hasPrimary = true;
                    }
                }
            });
        } catch (\Throwable $e) {
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }

        $count = count($files);
        $message = "Berhasil mengunggah {$count} gambar produk.";

        return redirect()->back()
            ->with('status', $message)
            ->with('success', $message);
    }

    /**
     * Replace an existing product image with a newly uploaded file.
     */
    public function update(UpdateProductImageRequest $request, Product $product, ProductImage $image): RedirectResponse
    {
        Gate::authorize('update', $product);

        if ($image->product_id !== $product->id) {
            abort(404);
        }

        $newFile = $request->file('image');
        $oldPath = $image->file_path;
        $newPath = $newFile->store('products', 'public');

        try {
            DB::transaction(function () use ($image, $newPath) {
                $image->file_path = $newPath;
                $image->save();
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($newPath);
            throw $e;
        }

        // Clean up old file if no longer referenced elsewhere
        if (! empty($oldPath) && ! ProductImage::where('file_path', $oldPath)->where('id', '!=', $image->id)->exists()) {
            Storage::disk('public')->delete($oldPath);
        }

        $message = 'Gambar produk berhasil diganti.';

        return redirect()->back()
            ->with('status', $message)
            ->with('success', $message);
    }

    /**
     * Set the specified image as primary for the product.
     */
    public function setPrimary(Request $request, Product $product, ProductImage $image): RedirectResponse
    {
        Gate::authorize('update', $product);

        if ($image->product_id !== $product->id) {
            abort(404);
        }

        DB::transaction(function () use ($product, $image) {
            $product->images()->where('id', '!=', $image->id)->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        $message = 'Gambar utama produk berhasil ditetapkan.';

        return redirect()->back()
            ->with('status', $message)
            ->with('success', $message);
    }

    /**
     * Reorder images for the product.
     */
    public function reorder(ReorderProductImagesRequest $request, Product $product): RedirectResponse
    {
        Gate::authorize('update', $product);

        if ($request->has('order')) {
            $order = (array) $request->input('order');
            DB::transaction(function () use ($product, $order) {
                foreach ($order as $index => $imageId) {
                    $product->images()->where('id', $imageId)->update(['sort_order' => (int) $index]);
                }
            });
        } elseif ($request->has('orders')) {
            $orders = (array) $request->input('orders');
            DB::transaction(function () use ($product, $orders) {
                foreach ($orders as $item) {
                    if (isset($item['id'], $item['sort_order'])) {
                        $product->images()->where('id', $item['id'])->update(['sort_order' => (int) $item['sort_order']]);
                    }
                }
            });
        }

        $message = 'Urutan gambar berhasil diperbarui.';

        return redirect()->back()
            ->with('status', $message)
            ->with('success', $message);
    }

    /**
     * Remove the specified image from storage.
     */
    public function destroy(Request $request, Product $product, ProductImage $image): RedirectResponse
    {
        Gate::authorize('update', $product);

        if ($image->product_id !== $product->id) {
            abort(404);
        }

        $wasPrimary = $image->is_primary;

        DB::transaction(function () use ($product, $image, $wasPrimary) {
            // Delete image model (triggers deleted lifecycle callback to remove physical file)
            $image->delete();

            // If the deleted image was primary, promote next remaining image to primary
            if ($wasPrimary) {
                $next = $product->images()->first();
                if ($next) {
                    $next->update(['is_primary' => true]);
                }
            }
        });

        $message = 'Gambar produk berhasil dihapus.';

        return redirect()->back()
            ->with('status', $message)
            ->with('success', $message);
    }
}
