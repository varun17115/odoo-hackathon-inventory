<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Auth;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = StockMovement::where('movement_type', 'adjustment')
            ->with(['user', 'items.product', 'items.sourceLocation', 'items.destinationLocation'])
            ->latest()
            ->paginate(20);

        return view('adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $products   = Product::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->with('racks')->get();

        return view('adjustments.create', compact('products', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id'    => 'required|exists:warehouses,id',
            'notes'           => 'nullable|string|max:500',
            'items'           => 'required|array|min:1',
            'items.*.product_id'       => 'required|exists:products,id',
            'items.*.rack_id'          => 'nullable|exists:racks,id',
            'items.*.counted_quantity' => 'required|integer|min:0',
        ]);

        // Build adjustment lines — calculate diff per item
        $lines = [];
        foreach ($validated['items'] as $item) {
            $stock = Stock::getOrCreate(
                $item['product_id'],
                $validated['warehouse_id'],
                $item['rack_id'] ?? null
            );

            $diff = $item['counted_quantity'] - $stock->quantity;
            $lines[] = [
                'stock'            => $stock,
                'product_id'       => $item['product_id'],
                'rack_id'          => $item['rack_id'] ?? null,
                'counted_quantity' => $item['counted_quantity'],
                'system_quantity'  => $stock->quantity,
                'diff'             => $diff,
            ];
        }

        // Create the movement record
        $movement = StockMovement::createAdjustment(Auth::id(), $validated['notes'] ?? null);

        foreach ($lines as $line) {
            // Update stock to counted quantity
            $line['stock']->update(['quantity' => $line['counted_quantity']]);

            // Log movement item (source = system qty, destination = counted qty conceptually)
            StockMovementItem::create([
                'movement_id'          => $movement->id,
                'product_id'           => $line['product_id'],
                'source_location_id'   => $line['rack_id'],
                'destination_location_id' => $line['rack_id'],
                'quantity'             => abs($line['diff']),
            ]);

            // Inventory log
            if ($line['diff'] !== 0) {
                InventoryLog::create([
                    'product_id' => $line['product_id'],
                    'user_id'    => Auth::id(),
                    'type'       => $line['diff'] > 0 ? 'in' : 'out',
                    'quantity'   => abs($line['diff']),
                    'reference'  => 'Adjustment #' . $movement->id,
                ]);
            }
        }

        return redirect()->route('adjustments.show', $movement)
            ->with('success', 'Stock adjustment applied successfully.');
    }

    public function show(StockMovement $adjustment)
    {
        if ($adjustment->movement_type !== 'adjustment') {
            abort(404);
        }
        $adjustment->load(['user', 'items.product', 'items.sourceLocation']);
        return view('adjustments.show', compact('adjustment'));
    }
}
