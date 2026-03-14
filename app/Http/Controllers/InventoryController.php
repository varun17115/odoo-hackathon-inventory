<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with('product')->paginate(15);
        return view('inventory.index', compact('inventories'));
    }

    public function show(Inventory $inventory)
    {
        $inventory->load('product', 'product.logs');
        return view('inventory.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        return view('inventory.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'min_quantity' => 'required|integer|min:0',
            'max_quantity' => 'required|integer|min:0',
            'warehouse_location' => 'nullable|string',
        ]);

        $inventory->update($validated);
        return redirect()->route('inventory.show', $inventory)->with('success', 'Inventory settings updated');
    }

    public function adjust(Inventory $inventory)
    {
        return view('inventory.adjust', compact('inventory'));
    }

    public function processAdjustment(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:in,out,adjustment',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $oldQuantity = $inventory->quantity;
        $newQuantity = $oldQuantity + $validated['quantity'];

        if ($newQuantity < 0) {
            return back()->with('error', 'Insufficient stock for this operation');
        }

        $inventory->update(['quantity' => $newQuantity]);

        InventoryLog::create([
            'product_id' => $inventory->product_id,
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'reference' => $validated['reference'],
            'notes' => $validated['notes'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('inventory.show', $inventory)->with('success', 'Inventory adjusted successfully');
    }
}
