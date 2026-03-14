<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\Receipt;
use App\Models\Delivery;
use App\Models\Transfer;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ── Dynamic filter inputs ──────────────────────────────────────────
        $filterDocType   = $request->input('doc_type');   // receipts|deliveries|transfers|adjustments
        $filterStatus    = $request->input('status');     // draft|waiting|ready|done|canceled
        $filterWarehouse = $request->input('warehouse_id');
        $filterCategory  = $request->input('category_id');

        // ── KPI Counts ────────────────────────────────────────────────────
        $totalProducts      = Product::where('is_active', true)->count();
        $lowStockItems      = Stock::where('quantity', '>', 0)->where('quantity', '<', 10)->count();
        $outOfStockItems    = Stock::where('quantity', '<=', 0)->count();
        $pendingReceipts    = Receipt::where('status', 'pending')->count();
        $pendingDeliveries  = Delivery::whereNotIn('status', ['shipped', 'cancelled'])->count();
        $pendingTransfers   = Transfer::where('status', 'pending')->count();

        // ── Secondary Stats ───────────────────────────────────────────────
        $totalCategories    = Category::count();
        $totalWarehouses    = Warehouse::where('is_active', true)->count();
        $totalStockQuantity = Stock::sum('quantity');
        $totalAdjustments   = StockMovement::where('movement_type', 'adjustment')->count();

        // ── Status Breakdown (for filter bar) ────────────────────────────
        $receiptStatusCounts = Receipt::selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $deliveryStatusCounts = Delivery::selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $transferStatusCounts = Transfer::selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        // ── Filter helpers ────────────────────────────────────────────────
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        // ── Activity Feed (filtered) ──────────────────────────────────────
        $movementsQuery = StockMovement::with(['user', 'items.product'])->latest();
        if ($filterDocType === 'receipts')     $movementsQuery->where('movement_type', 'receipt');
        elseif ($filterDocType === 'deliveries')  $movementsQuery->where('movement_type', 'delivery');
        elseif ($filterDocType === 'transfers')   $movementsQuery->where('movement_type', 'transfer');
        elseif ($filterDocType === 'adjustments') $movementsQuery->where('movement_type', 'adjustment');
        $recentMovements = $movementsQuery->limit(10)->get();

        // ── Low / Out-of-Stock Products ───────────────────────────────────
        $lowStockQuery = Stock::with(['product.category', 'warehouse', 'location'])
            ->where('quantity', '<', 10)
            ->orderBy('quantity', 'asc');
        if ($filterWarehouse) $lowStockQuery->where('warehouse_id', $filterWarehouse);
        if ($filterCategory)  $lowStockQuery->whereHas('product', fn($q) => $q->where('category_id', $filterCategory));
        $lowStockProducts = $lowStockQuery->limit(10)->get();

        // ── Recent Receipts ───────────────────────────────────────────────
        $receiptsQuery = Receipt::with(['supplier', 'warehouse'])->latest();
        if ($filterWarehouse) $receiptsQuery->where('warehouse_id', $filterWarehouse);
        if ($filterStatus)    $receiptsQuery->where('status', $filterStatus);
        $recentReceipts = $receiptsQuery->limit(5)->get();

        // ── Recent Deliveries ─────────────────────────────────────────────
        $deliveriesQuery = Delivery::with('warehouse')->latest();
        if ($filterWarehouse) $deliveriesQuery->where('warehouse_id', $filterWarehouse);
        if ($filterStatus)    $deliveriesQuery->where('status', $filterStatus);
        $recentDeliveries = $deliveriesQuery->limit(5)->get();

        // ── Recent Transfers ──────────────────────────────────────────────
        $transfersQuery = Transfer::with(['fromWarehouse', 'toWarehouse'])->latest();
        if ($filterWarehouse) {
            $transfersQuery->where(function ($q) use ($filterWarehouse) {
                $q->where('from_warehouse_id', $filterWarehouse)
                  ->orWhere('to_warehouse_id', $filterWarehouse);
            });
        }
        if ($filterStatus) $transfersQuery->where('status', $filterStatus);
        $recentTransfers = $transfersQuery->limit(5)->get();

        // ── Stock by Warehouse ────────────────────────────────────────────
        $stockByWarehouse = Warehouse::where('is_active', true)
            ->withCount('stocks')
            ->get()
            ->map(fn($w) => [
                'name'           => $w->name,
                'count'          => $w->stocks_count,
                'total_quantity' => Stock::where('warehouse_id', $w->id)->sum('quantity'),
                'low_stock'      => Stock::where('warehouse_id', $w->id)->where('quantity', '<', 10)->count(),
            ]);

        // ── Stock by Category ─────────────────────────────────────────────
        $stockByCategory = Category::withCount('products')
            ->get()
            ->map(fn($c) => [
                'name'     => $c->name,
                'products' => $c->products_count,
                'quantity' => Stock::whereHas('product', fn($q) => $q->where('category_id', $c->id))->sum('quantity'),
            ])
            ->filter(fn($c) => $c['products'] > 0)
            ->values();

        // ── Movement Type Breakdown (last 30 days) ────────────────────────
        $movementBreakdown = StockMovement::selectRaw('movement_type, count(*) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('movement_type')
            ->pluck('total', 'movement_type');

        return view('dashboard.index', compact(
            'totalProducts', 'lowStockItems', 'outOfStockItems',
            'pendingReceipts', 'pendingDeliveries', 'pendingTransfers',
            'totalCategories', 'totalWarehouses', 'totalStockQuantity', 'totalAdjustments',
            'receiptStatusCounts', 'deliveryStatusCounts', 'transferStatusCounts',
            'warehouses', 'categories',
            'recentMovements', 'lowStockProducts',
            'recentReceipts', 'recentDeliveries', 'recentTransfers',
            'stockByWarehouse', 'stockByCategory', 'movementBreakdown',
            'filterDocType', 'filterStatus', 'filterWarehouse', 'filterCategory'
        ));
    }
}
