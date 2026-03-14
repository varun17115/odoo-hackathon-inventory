<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use Illuminate\Http\Request;
use Auth;

class ReceiptController extends Controller
{
    public function index()
    {
        $query = Receipt::with(['supplier', 'warehouse', 'user']);

        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Filter by warehouse
        if (request('warehouse_id')) {
            $query->where('warehouse_id', request('warehouse_id'));
        }

        // Filter by supplier
        if (request('supplier_id')) {
            $query->where('supplier_id', request('supplier_id'));
        }

        // Search by receipt number
        if (request('search')) {
            $query->where('receipt_number', 'like', '%' . request('search') . '%');
        }

        $receipts = $query->latest()->paginate(15);
        
        // Get filter options
        $warehouses = Warehouse::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        
        return view('receipts.index', compact('receipts', 'warehouses', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->with('racks')->get();
        $products = Product::where('is_active', true)->get();
        $receiptNumber = Receipt::generateReceiptNumber();
        
        return view('receipts.create', compact('suppliers', 'warehouses', 'products', 'receiptNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.rack_id' => 'nullable|exists:racks,id',
        ]);

        $receipt = Receipt::create([
            'receipt_number' => Receipt::generateReceiptNumber(),
            'supplier_id' => $validated['supplier_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'user_id' => Auth::id(),
            'receipt_date' => $validated['receipt_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $totalPrice = $item['quantity'] * $item['unit_price'];
            ReceiptItem::create([
                'receipt_id' => $receipt->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $totalPrice,
                'rack_id' => $item['rack_id'] ?? null,
            ]);
            $totalAmount += $totalPrice;
        }

        $receipt->update(['total_amount' => $totalAmount]);

        return redirect()->route('receipts.show', $receipt)->with('success', 'Receipt created successfully');
    }

    public function show(Receipt $receipt)
    {
        $receipt->load(['supplier', 'warehouse', 'user', 'items.product', 'items.rack']);
        return view('receipts.show', compact('receipt'));
    }

    public function verify(Receipt $receipt)
    {
        if ($receipt->status !== 'pending') {
            return redirect()->route('receipts.show', $receipt)->with('error', 'Only pending receipts can be verified');
        }

        // Create stock movement record
        $movement = StockMovement::createReceipt($receipt->id, Auth::id(), $receipt->notes);

        // Update stock for each item
        foreach ($receipt->items as $item) {
            $stock = Stock::getOrCreate(
                $item->product_id,
                $receipt->warehouse_id,
                $item->rack_id
            );

            $stock->addStock($item->quantity);

            // Log movement item
            StockMovementItem::create([
                'movement_id' => $movement->id,
                'product_id' => $item->product_id,
                'source_location_id' => null,
                'destination_location_id' => $item->rack_id,
                'quantity' => $item->quantity,
            ]);

            // Log the transaction
            \App\Models\InventoryLog::create([
                'product_id' => $item->product_id,
                'user_id' => Auth::id(),
                'type' => 'in',
                'quantity' => $item->quantity,
                'reference' => 'Receipt: ' . $receipt->receipt_number,
            ]);
        }

        $receipt->update(['status' => 'verified']);

        return redirect()->route('receipts.show', $receipt)->with('success', 'Receipt verified and stock updated');
    }

    public function destroy(Receipt $receipt)
    {
        if ($receipt->status !== 'pending') {
            return redirect()->route('receipts.index')->with('error', 'Only pending receipts can be deleted');
        }

        $receipt->delete();
        return redirect()->route('receipts.index')->with('success', 'Receipt deleted successfully');
    }
}
