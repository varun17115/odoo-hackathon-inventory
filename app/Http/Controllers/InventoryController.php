<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $query = Stock::with(['product.category', 'warehouse', 'location'])
            ->orderBy('warehouse_id')
            ->orderBy('product_id');

        // Filter by warehouse
        if (request('warehouse_id')) {
            $query->where('warehouse_id', request('warehouse_id'));
        }

        // Filter by product
        if (request('product_id')) {
            $query->where('product_id', request('product_id'));
        }

        // SKU search
        if (request('sku')) {
            $query->whereHas('product', fn($q) =>
                $q->where('sku', 'like', '%' . request('sku') . '%')
            );
        }

        // Product name search
        if (request('search')) {
            $query->whereHas('product', fn($q) =>
                $q->where('name', 'like', '%' . request('search') . '%')
                  ->orWhere('sku', 'like', '%' . request('search') . '%')
            );
        }

        // Low stock filter
        if (request('low_stock')) {
            $query->where('quantity', '>', 0)->where('quantity', '<', 10);
        }

        // Out of stock filter
        if (request('out_of_stock')) {
            $query->where('quantity', 0);
        }

        // Stock range filter
        if (request('qty_min') !== null && request('qty_min') !== '') {
            $query->where('quantity', '>=', request('qty_min'));
        }
        if (request('qty_max') !== null && request('qty_max') !== '') {
            $query->where('quantity', '<=', request('qty_max'));
        }

        // Category filter
        if (request('category_id')) {
            $query->whereHas('product', fn($q) =>
                $q->where('category_id', request('category_id'))
            );
        }

        $stocks = $query->paginate(20)->withQueryString();

        $warehouses = Warehouse::where('is_active', true)->get();
        $products   = Product::where('is_active', true)->orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();

        // Stats
        $totalLowStock  = Stock::where('quantity', '>', 0)->where('quantity', '<', 10)->count();
        $totalOutOfStock = Stock::where('quantity', 0)->count();

        return view('inventory.index', compact(
            'stocks', 'warehouses', 'products', 'categories',
            'totalLowStock', 'totalOutOfStock'
        ));
    }

    public function byWarehouse($warehouseId)
    {
        $warehouse = Warehouse::findOrFail($warehouseId);
        $stocks = Stock::where('warehouse_id', $warehouseId)
            ->with(['product.category', 'location'])
            ->orderBy('product_id')
            ->paginate(20);

        return view('inventory.by-warehouse', compact('warehouse', 'stocks'));
    }

    public function byProduct($productId)
    {
        $product = Product::findOrFail($productId);
        $stocks = Stock::where('product_id', $productId)
            ->with(['warehouse', 'location'])
            ->orderBy('warehouse_id')
            ->paginate(20);

        $totalStock = Stock::getProductTotal($productId);

        return view('inventory.by-product', compact('product', 'stocks', 'totalStock'));
    }

    public function summary()
    {
        $warehouses = Warehouse::where('is_active', true)->with('racks')->get();
        $products   = Product::where('is_active', true)->get();

        $summary = [];
        foreach ($products as $product) {
            $summary[$product->id] = [
                'product'      => $product,
                'total'        => Stock::getProductTotal($product->id),
                'by_warehouse' => [],
            ];
            foreach ($warehouses as $warehouse) {
                $summary[$product->id]['by_warehouse'][$warehouse->id] =
                    Stock::getWarehouseTotal($product->id, $warehouse->id);
            }
        }

        return view('inventory.summary', compact('summary', 'warehouses', 'products'));
    }
}
