<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\ReorderRule;
use App\Models\Product;
use App\Models\Warehouse;

class AlertController extends Controller
{
    public function index()
    {
        // Out of stock products (have stock records with 0 qty)
        $outOfStock = Stock::with(['product.category', 'warehouse', 'location'])
            ->where('quantity', 0)
            ->orderBy('warehouse_id')
            ->get();

        // Low stock (qty > 0 but < 10)
        $lowStock = Stock::with(['product.category', 'warehouse', 'location'])
            ->where('quantity', '>', 0)
            ->where('quantity', '<', 10)
            ->orderBy('quantity')
            ->get();

        // Triggered reorder rules
        $triggeredRules = ReorderRule::with(['product.category', 'warehouse', 'preferredSupplier'])
            ->where('is_active', true)
            ->get()
            ->filter(fn($rule) => $rule->isTriggered())
            ->values();

        // Products with NO stock records at all
        $noStockProducts = Product::where('is_active', true)
            ->whereDoesntHave('stocks')
            ->with('category')
            ->orderBy('name')
            ->get();

        // Per-warehouse low stock summary
        $warehouseSummary = Warehouse::where('is_active', true)
            ->withCount(['stocks as low_stock_count' => fn($q) => $q->where('quantity', '<', 10)])
            ->withCount(['stocks as out_of_stock_count' => fn($q) => $q->where('quantity', 0)])
            ->get();

        return view('alerts.index', compact(
            'outOfStock', 'lowStock', 'triggeredRules', 'noStockProducts', 'warehouseSummary'
        ));
    }

    /**
     * JSON endpoint for the header badge count
     */
    public function count()
    {
        $count = Stock::where('quantity', '<', 10)->count();
        return response()->json(['count' => $count]);
    }
}
