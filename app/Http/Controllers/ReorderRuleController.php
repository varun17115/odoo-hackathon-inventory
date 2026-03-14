<?php

namespace App\Http\Controllers;

use App\Models\ReorderRule;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Stock;
use Illuminate\Http\Request;

class ReorderRuleController extends Controller
{
    public function index()
    {
        $rules = ReorderRule::with(['product.category', 'warehouse', 'preferredSupplier'])
            ->latest()
            ->paginate(20);

        // Annotate each rule with current stock and triggered status
        foreach ($rules as $rule) {
            $rule->current_stock = $rule->warehouse_id
                ? Stock::getWarehouseTotal($rule->product_id, $rule->warehouse_id)
                : Stock::getProductTotal($rule->product_id);
            $rule->triggered = $rule->current_stock <= $rule->min_quantity;
        }

        $products   = Product::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('reorder-rules.index', compact('rules', 'products', 'warehouses', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'            => 'required|exists:products,id',
            'warehouse_id'          => 'nullable|exists:warehouses,id',
            'min_quantity'          => 'required|integer|min:0',
            'reorder_quantity'      => 'required|integer|min:1',
            'preferred_supplier_id' => 'nullable|exists:suppliers,id',
            'is_active'             => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // Enforce unique per product+warehouse
        $exists = ReorderRule::where('product_id', $validated['product_id'])
            ->where('warehouse_id', $validated['warehouse_id'] ?? null)
            ->exists();

        if ($exists) {
            return back()->with('error', 'A reorder rule already exists for this product/warehouse combination.');
        }

        ReorderRule::create($validated);
        return back()->with('success', 'Reorder rule created.');
    }

    public function update(Request $request, ReorderRule $reorderRule)
    {
        $validated = $request->validate([
            'min_quantity'          => 'required|integer|min:0',
            'reorder_quantity'      => 'required|integer|min:1',
            'preferred_supplier_id' => 'nullable|exists:suppliers,id',
            'is_active'             => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $reorderRule->update($validated);

        return back()->with('success', 'Reorder rule updated.');
    }

    public function destroy(ReorderRule $reorderRule)
    {
        $reorderRule->delete();
        return back()->with('success', 'Reorder rule deleted.');
    }

    public function triggered()
    {
        return redirect()->route('reorder-rules.index');
    }
}
