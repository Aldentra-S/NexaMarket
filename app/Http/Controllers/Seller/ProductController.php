<?php

namespace App\Http\Controllers\Seller;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreProductRequest;
use App\Http\Requests\Seller\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the seller's products.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Product::class);

        $shop = $request->user()->shop;
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        $query = $shop->products()->with(['primaryImage', 'images', 'category'])->latest();

        if ($statusFilter && in_array($statusFilter, ProductStatus::values(), true)) {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $operator) {
                $q->where('name', $operator, "%{$search}%")
                  ->orWhere('description', $operator, "%{$search}%");
            });
        }

        $products = $query->paginate(10)->withQueryString();

        return view('seller.products.index', [
            'shop' => $shop,
            'products' => $products,
            'currentFilter' => $statusFilter,
            'search' => $search,
            'totalCount' => $shop->products()->count(),
            'publishedCount' => $shop->products()->where('status', ProductStatus::PUBLISHED)->count(),
            'draftCount' => $shop->products()->where('status', ProductStatus::DRAFT)->count(),
            'archivedCount' => $shop->products()->where('status', ProductStatus::ARCHIVED)->count(),
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        Gate::authorize('create', Product::class);

        $categories = Category::active()->orderBy('name')->get();

        return view('seller.products.create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $shop = $request->user()->shop;
        $uploadedPaths = [];

        try {
            DB::transaction(function () use ($request, $shop, &$uploadedPaths) {
                $baseSlug = Str::slug($request->validated('name'));
                $slug = $baseSlug;
                $count = 1;

                while (Product::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$count}";
                    $count++;
                }

                $product = new Product($request->safe()->except('images'));
                $product->slug = $slug;
                $product->shop_id = $shop->id;
                $product->save();

                if ($request->hasFile('images')) {
                    $isFirst = true;
                    $sort = 0;
                    foreach ($request->file('images') as $file) {
                        $path = $file->store('products', 'public');
                        $uploadedPaths[] = $path;

                        $product->images()->create([
                            'file_path' => $path,
                            'is_primary' => $isFirst,
                            'sort_order' => $sort,
                        ]);

                        $isFirst = false;
                        $sort++;
                    }
                }
            });
        } catch (\Throwable $e) {
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }

        $message = 'Produk "' . $request->validated('name') . '" berhasil ditambahkan ke toko Anda.';
        return redirect()->route('seller.products.index')
            ->with('status', $message)
            ->with('success', $message);
    }

    /**
     * Display the specified product details for seller.
     */
    public function show(Product $product): View
    {
        Gate::authorize('view', $product);

        $product->load(['shop', 'images', 'primaryImage', 'category']);

        return view('seller.products.show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        Gate::authorize('update', $product);

        $product->load(['images', 'category']);
        $categories = Category::active()->orderBy('name')->get();

        return view('seller.products.edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        Gate::authorize('update', $product);

        $uploadedPaths = [];

        try {
            DB::transaction(function () use ($request, $product, &$uploadedPaths) {
                if ($product->name !== $request->validated('name')) {
                    $baseSlug = Str::slug($request->validated('name'));
                    $slug = $baseSlug;
                    $count = 1;

                    while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                        $slug = "{$baseSlug}-{$count}";
                        $count++;
                    }

                    $product->slug = $slug;
                }

                $product->fill($request->safe()->except('images'));
                $product->save();

                if ($request->hasFile('images')) {
                    $hasPrimary = $product->images()->where('is_primary', true)->exists();
                    $maxSort = (int) $product->images()->max('sort_order');

                    foreach ($request->file('images') as $file) {
                        $path = $file->store('products', 'public');
                        $uploadedPaths[] = $path;
                        $maxSort++;

                        $product->images()->create([
                            'file_path' => $path,
                            'is_primary' => ! $hasPrimary,
                            'sort_order' => $maxSort,
                        ]);

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

        $message = 'Produk "' . $product->name . '" berhasil diperbarui.';
        return redirect()->route('seller.products.index')
            ->with('status', $message)
            ->with('success', $message);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Request $request, Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);

        $productName = $product->name;
        $product->delete();

        $message = 'Produk "' . $productName . '" telah berhasil dihapus dari toko Anda.';
        return redirect()->route('seller.products.index')
            ->with('status', $message)
            ->with('success', $message);
    }
}
