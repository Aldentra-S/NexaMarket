<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories for administrator.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = Category::withCount('products');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.categories.index', [
            'categories' => $categories,
            'search' => $search,
            'totalCount' => Category::count(),
            'activeCount' => Category::where('is_active', true)->count(),
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $rawSlug = ! empty($data['slug']) ? $data['slug'] : $data['name'];
        $baseSlug = Str::slug($rawSlug);
        $slug = $baseSlug;
        $count = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        $data['slug'] = $slug;
        $data['is_active'] = $request->boolean('is_active', true);

        $category = Category::create($data);

        $message = "Kategori \"{$category->name}\" berhasil ditambahkan.";

        return redirect()->route('admin.categories.index')
            ->with('status', $message)
            ->with('success', $message);
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        $category->loadCount('products');

        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['slug'])) {
            $slug = Str::slug($data['slug']);
        } elseif ($category->name !== $data['name']) {
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $count = 1;

            while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = "{$baseSlug}-{$count}";
                $count++;
            }
        } else {
            $slug = $category->slug;
        }

        $data['slug'] = $slug;
        $data['is_active'] = $request->boolean('is_active', true);

        $category->update($data);

        $message = "Kategori \"{$category->name}\" berhasil diperbarui.";

        return redirect()->route('admin.categories.index')
            ->with('status', $message)
            ->with('success', $message);
    }

    /**
     * Remove the specified category from storage safely.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $productCount = $category->products()->count();

        if ($productCount > 0) {
            return redirect()->back()->withErrors([
                'category' => "Kategori \"{$category->name}\" tidak dapat dihapus karena masih digunakan oleh {$productCount} produk. Pindahkan atau ubah kategori produk terkait terlebih dahulu.",
            ]);
        }

        $categoryName = $category->name;
        $category->delete();

        $message = "Kategori \"{$categoryName}\" telah berhasil dihapus.";

        return redirect()->route('admin.categories.index')
            ->with('status', $message)
            ->with('success', $message);
    }
}
