<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Stock;
use App\Models\InventoryLog;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use Illuminate\Http\Request;
use Auth;

class TransferController extends Controller
{
    public function index()
    {
        $query = Transfer::with(['fromWarehouse', 'toWarehouse', 'user']);

        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Filter by from warehouse
        if (request('from_warehouse_id')) {
            $query->where('from_warehouse_id', request('from_warehouse_id'));
        }

        // Filter by to warehouse
        if (request('to_warehouse_id')) {
            $query->where('to_warehouse_id', request('to_warehouse_id'));
        }

        // Search by transfer number
        if (request('search')) {
            $query->where('transfer_number', 'like', '%' . request('search') . '%');
        }

        $transfers = $query->latest()->paginate(15);
        
        // Get filter options
        $warehouses = Warehouse::where('is_active', true)->get();
        
        return view('transfers.index', compact('transfers', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->with('racks')->get();
        $products = Product::where('is_active', true)->get();
        $transferNumber = Transfer::generateTransferNumber();
        
        return view('transfers.create', compact('warehouses', 'products', 'transferNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.from_rack_id' => 'nullable|exists:racks,id',
            'items.*.to_rack_id' => 'nullable|exists:racks,id',
        ]);

        // Validate stock availability
        foreach ($validated['items'] as $item) {
            $stock = Stock::where('product_id', $item['product_id'])
                ->where('warehouse_id', $validated['from_warehouse_id'])
                ->where('location_id', $item['from_rack_id'] ?? null)
                ->first();
            
            if (!$stock || $stock->quantity < $item['quantity']) {
                return back()->withErrors(['error' => 'Insufficient stock for product ID ' . $item['product_id']]);
            }
        }

        $transfer = Transfer::create([
            'transfer_number' => Transfer::generateTransferNumber(),
            'from_warehouse_id' => $validated['from_warehouse_id'],
            'to_warehouse_id' => $validated['to_warehouse_id'],
            'user_id' => Auth::id(),
            'transfer_date' => $validated['transfer_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            TransferItem::create([
                'transfer_id' => $transfer->id,
                'product_id' => $item['product_id'],
                'from_rack_id' => $item['from_rack_id'] ?? null,
                'to_rack_id' => $item['to_rack_id'] ?? null,
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('transfers.show', $transfer)->with('success', 'Transfer created successfully');
    }

    public function show(Transfer $transfer)
    {
        $transfer->load(['fromWarehouse', 'toWarehouse', 'user', 'items.product', 'items.fromRack', 'items.toRack']);
        return view('transfers.show', compact('transfer'));
    }

    public function complete(Transfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return redirect()->route('transfers.show', $transfer)->with('error', 'Only pending transfers can be completed');
        }

        // Create stock movement record
        $movement = StockMovement::createTransfer($transfer->id, Auth::id(), $transfer->notes);

        // Process each transfer item
        foreach ($transfer->items as $item) {
            // Remove from source location
            $sourceStock = Stock::where('product_id', $item->product_id)
                ->where('warehouse_id', $transfer->from_warehouse_id)
                ->where('location_id', $item->from_rack_id)
                ->first();

            if ($sourceStock) {
                $sourceStock->removeStock($item->quantity);
            }

            // Add to destination location
            $destStock = Stock::getOrCreate(
                $item->product_id,
                $transfer->to_warehouse_id,
                $item->to_rack_id
            );
            $destStock->addStock($item->quantity);

            // Log movement item
            StockMovementItem::create([
                'movement_id' => $movement->id,
                'product_id' => $item->product_id,
                'source_location_id' => $item->from_rack_id,
                'destination_location_id' => $item->to_rack_id,
                'quantity' => $item->quantity,
            ]);

            // Log the transaction
            InventoryLog::create([
                'product_id' => $item->product_id,
                'user_id' => Auth::id(),
                'type' => 'transfer',
                'quantity' => $item->quantity,
                'reference' => 'Transfer: ' . $transfer->transfer_number,
            ]);
        }

        $transfer->update(['status' => 'completed']);

        return redirect()->route('transfers.show', $transfer)->with('success', 'Transfer completed successfully');
    }

    public function cancel(Transfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return redirect()->route('transfers.show', $transfer)->with('error', 'Only pending transfers can be cancelled');
        }

        $transfer->update(['status' => 'cancelled']);
        return redirect()->route('transfers.show', $transfer)->with('success', 'Transfer cancelled successfully');
    }

    public function destroy(Transfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return redirect()->route('transfers.index')->with('error', 'Only pending transfers can be deleted');
        }

        $transfer->delete();
        return redirect()->route('transfers.index')->with('success', 'Transfer deleted successfully');
    }
}
