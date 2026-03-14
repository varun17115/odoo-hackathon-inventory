<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use Illuminate\Http\Request;
use Auth;

class DeliveryController extends Controller
{
    public function index()
    {
        $query = Delivery::with(['warehouse', 'user']);

        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Filter by warehouse
        if (request('warehouse_id')) {
            $query->where('warehouse_id', request('warehouse_id'));
        }

        // Search by delivery number or customer name
        if (request('search')) {
            $query->where(function($q) {
                $q->where('delivery_number', 'like', '%' . request('search') . '%')
                  ->orWhere('customer_name', 'like', '%' . request('search') . '%');
            });
        }

        $deliveries = $query->latest()->paginate(15);
        
        // Get filter options
        $warehouses = Warehouse::where('is_active', true)->get();
        
        return view('deliveries.index', compact('deliveries', 'warehouses'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->with('racks')->get();
        $products = Product::where('is_active', true)->get();
        $deliveryNumber = Delivery::generateDeliveryNumber();
        
        return view('deliveries.create', compact('warehouses', 'products', 'deliveryNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'delivery_address' => 'required|string',
            'warehouse_id' => 'required|exists:warehouses,id',
            'delivery_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.rack_id' => 'nullable|exists:racks,id',
        ]);

        // Validate stock availability
        foreach ($validated['items'] as $item) {
            $stock = Stock::where('product_id', $item['product_id'])
                ->where('warehouse_id', $validated['warehouse_id'])
                ->where('location_id', $item['rack_id'] ?? null)
                ->first();
            
            if (!$stock || $stock->quantity < $item['quantity']) {
                return back()->withErrors(['error' => 'Insufficient stock for product ID ' . $item['product_id']])->withInput();
            }
        }

        $delivery = Delivery::create([
            'delivery_number' => Delivery::generateDeliveryNumber(),
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'customer_email' => $validated['customer_email'] ?? null,
            'delivery_address' => $validated['delivery_address'],
            'warehouse_id' => $validated['warehouse_id'],
            'user_id' => Auth::id(),
            'delivery_date' => $validated['delivery_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'draft',
        ]);

        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $totalPrice = $item['quantity'] * $item['unit_price'];
            DeliveryItem::create([
                'delivery_id' => $delivery->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $totalPrice,
                'rack_id' => $item['rack_id'] ?? null,
            ]);
            $totalAmount += $totalPrice;
        }

        $delivery->update(['total_amount' => $totalAmount]);

        return redirect()->route('deliveries.show', $delivery)->with('success', 'Delivery created successfully');
    }

    public function show(Delivery $delivery)
    {
        $delivery->load(['warehouse', 'user', 'items.product', 'items.rack']);
        return view('deliveries.show', compact('delivery'));
    }

    public function startPicking(Delivery $delivery)
    {
        if (!$delivery->canPick()) {
            return redirect()->route('deliveries.show', $delivery)->with('error', 'Cannot start picking for this delivery');
        }

        $delivery->update(['status' => 'picking']);
        return redirect()->route('deliveries.show', $delivery)->with('success', 'Picking started');
    }

    public function markItemPicked(Delivery $delivery, DeliveryItem $item)
    {
        if ($delivery->status !== 'picking') {
            return redirect()->route('deliveries.show', $delivery)->with('error', 'Delivery must be in picking status');
        }

        $item->update(['is_picked' => true]);
        return redirect()->route('deliveries.show', $delivery)->with('success', 'Item marked as picked');
    }

    public function startPacking(Delivery $delivery)
    {
        if (!$delivery->canPack()) {
            return redirect()->route('deliveries.show', $delivery)->with('error', 'All items must be picked first');
        }

        $delivery->update(['status' => 'packed']);
        
        // Mark all items as packed
        $delivery->items()->update(['is_packed' => true]);
        
        return redirect()->route('deliveries.show', $delivery)->with('success', 'All items packed');
    }

    public function validate(Delivery $delivery)
    {
        if (!$delivery->canValidate()) {
            return redirect()->route('deliveries.show', $delivery)->with('error', 'All items must be packed first');
        }

        try {
            // Create stock movement record
            $movement = StockMovement::createDelivery($delivery->id, Auth::id(), $delivery->notes);

            // Reduce stock for each item
            foreach ($delivery->items as $item) {
                // Find stock record - handle null rack_id properly
                $query = Stock::where('product_id', $item->product_id)
                    ->where('warehouse_id', $delivery->warehouse_id);
                
                if ($item->rack_id) {
                    $query->where('location_id', $item->rack_id);
                } else {
                    $query->whereNull('location_id');
                }
                
                $stock = $query->first();

                if (!$stock) {
                    throw new \Exception("Stock not found for product ID {$item->product_id}");
                }

                // Remove stock (will throw exception if insufficient)
                $stock->removeStock($item->quantity);

                // Log movement item
                StockMovementItem::create([
                    'movement_id' => $movement->id,
                    'product_id' => $item->product_id,
                    'source_location_id' => $item->rack_id,
                    'destination_location_id' => null,
                    'quantity' => $item->quantity,
                ]);

                // Log the transaction
                \App\Models\InventoryLog::create([
                    'product_id' => $item->product_id,
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'quantity' => $item->quantity,
                    'reference' => 'Delivery: ' . $delivery->delivery_number,
                ]);
            }

            $delivery->update(['status' => 'validated']);

            return redirect()->route('deliveries.show', $delivery)->with('success', 'Delivery validated and stock reduced successfully');
        } catch (\Exception $e) {
            return redirect()->route('deliveries.show', $delivery)->with('error', 'Failed to validate delivery: ' . $e->getMessage());
        }
    }

    public function ship(Delivery $delivery)
    {
        if ($delivery->status !== 'validated') {
            return redirect()->route('deliveries.show', $delivery)->with('error', 'Delivery must be validated first');
        }

        $delivery->update(['status' => 'shipped']);
        return redirect()->route('deliveries.show', $delivery)->with('success', 'Delivery marked as shipped');
    }

    public function cancel(Delivery $delivery)
    {
        if (!$delivery->canCancel()) {
            return redirect()->route('deliveries.show', $delivery)->with('error', 'Cannot cancel this delivery');
        }

        $delivery->update(['status' => 'cancelled']);
        return redirect()->route('deliveries.show', $delivery)->with('success', 'Delivery cancelled');
    }

    public function destroy(Delivery $delivery)
    {
        if ($delivery->status !== 'draft') {
            return redirect()->route('deliveries.index')->with('error', 'Only draft deliveries can be deleted');
        }

        $delivery->delete();
        return redirect()->route('deliveries.index')->with('success', 'Delivery deleted successfully');
    }
}
