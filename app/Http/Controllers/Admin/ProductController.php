<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of all products across all shops for platform oversight.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $categoryFilter = $request->query('category_id');
        $search = $request->query('search');

        $query = Product::with(['shop', 'category', 'primaryImage'])->latest();

        if ($statusFilter && in_array($statusFilter, ProductStatus::values(), true)) {
            $query->where('status', $statusFilter);
        }

        if ($categoryFilter) {
            $query->where('category_id', $categoryFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
            'currentStatus' => $statusFilter,
            'currentCategory' => $categoryFilter,
            'search' => $search,
            'totalCount' => Product::count(),
            'publishedCount' => Product::where('status', ProductStatus::PUBLISHED->value)->count(),
            'draftCount' => Product::where('status', ProductStatus::DRAFT->value)->count(),
            'archivedCount' => Product::where('status', ProductStatus::ARCHIVED->value)->count(),
        ]);
    }
}
