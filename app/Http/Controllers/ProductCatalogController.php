<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductCatalogController extends Controller
{
    /**
     * Display a listing of publicly available products.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q') ?? $request->query('search');
        $sort = $request->query('sort', 'terbaru');

        $query = Product::published()
            ->forActiveShop()
            ->with('shop');

        if ($search) {
            $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $operator) {
                $q->where('name', $operator, "%{$search}%")
                  ->orWhere('description', $operator, "%{$search}%");
            });
        }

        match ($sort) {
            'termurah' => $query->orderBy('price', 'asc'),
            'termahal' => $query->orderBy('price', 'desc'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('catalog.index', [
            'products' => $products,
            'search' => $search,
            'currentSort' => $sort,
        ]);
    }

    /**
     * Display the specified public product details.
     */
    public function show(string $slug): View
    {
        $product = Product::published()
            ->forActiveShop()
            ->with(['shop.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('catalog.show', [
            'product' => $product,
        ]);
    }
}
