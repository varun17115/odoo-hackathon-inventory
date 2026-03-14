<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $lowStockProducts = Inventory::where('quantity', '<=', \DB::raw('min_quantity'))->count();
        $totalInventoryValue = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->selectRaw('SUM(inventories.quantity * products.price) as total')
            ->value('total') ?? 0;

        $recentLogs = InventoryLog::with(['product', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        $topProducts = Product::withCount('logs')
            ->orderByDesc('logs_count')
            ->limit(5)
            ->get();

        $inventoryStatus = Inventory::with('product')
            ->get()
            ->map(function ($inv) {
                return [
                    'product' => $inv->product->name,
                    'quantity' => $inv->quantity,
                    'min' => $inv->min_quantity,
                    'max' => $inv->max_quantity,
                    'status' => $inv->isLowStock() ? 'low' : ($inv->isOverStock() ? 'over' : 'normal'),
                ];
            });

        return view('dashboard.index', [
            'totalProducts' => $totalProducts,
            'totalCategories' => $totalCategories,
            'lowStockProducts' => $lowStockProducts,
            'totalInventoryValue' => $totalInventoryValue,
            'recentLogs' => $recentLogs,
            'topProducts' => $topProducts,
            'inventoryStatus' => $inventoryStatus,
        ]);
    }
}
