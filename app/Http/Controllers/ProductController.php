<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::with(['category', 'stocks', 'reorderRules']);

        if (request('category_id')) {
            $query->where('category_id', request('category_id'));
        }

        if (request('is_active') !== null && request('is_active') !== '') {
            $query->where('is_active', request('is_active'));
        }

        if (request('search')) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . request('search') . '%')
                  ->orWhere('sku', 'like', '%' . request('search') . '%');
            });
        }

        $products   = $query->paginate(15);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'unit' => 'required|string',
        ]);

        Product::create($validated);
        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        $product->load([
            'category',
            'stocks.warehouse',
            'stocks.location',
            'reorderRules.warehouse',
            'reorderRules.preferredSupplier',
        ]);
        
        // Get total stock across all locations
        $totalStock = $product->stocks->sum('quantity');
        
        // Get recent stock movements for this product
        $recentMovements = \App\Models\StockMovement::whereHas('items', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })
        ->with(['user', 'items' => function ($query) use ($product) {
            $query->where('product_id', $product->id);
        }])
        ->latest()
        ->take(10)
        ->get();
        
        return view('products.show', compact('product', 'totalStock', 'recentMovements'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'unit' => 'required|string',
        ]);

        $product->update($validated);
        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
