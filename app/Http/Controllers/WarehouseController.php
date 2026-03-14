<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Rack;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::withCount('racks')->paginate(15);
        return view('warehouses.index', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:warehouses',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'manager_name' => 'nullable|string',
            'manager_phone' => 'nullable|string',
        ]);

        Warehouse::create($validated);
        return redirect()->route('warehouses.index')->with('success', 'Warehouse created successfully');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:warehouses,name,' . $warehouse->id,
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'manager_name' => 'nullable|string',
            'manager_phone' => 'nullable|string',
        ]);

        $warehouse->update($validated);
        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully');
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->racks()->count() > 0) {
            return redirect()->route('warehouses.index')->with('error', 'Cannot delete warehouse with racks');
        }

        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted successfully');
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load('racks');
        return view('warehouses.show', compact('warehouse'));
    }

    public function storeRack(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:racks,name,NULL,id,warehouse_id,' . $warehouse->id,
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $validated['warehouse_id'] = $warehouse->id;
        Rack::create($validated);

        return redirect()->route('warehouses.show', $warehouse)->with('success', 'Rack created successfully');
    }

    public function updateRack(Request $request, Warehouse $warehouse, Rack $rack)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:racks,name,' . $rack->id . ',id,warehouse_id,' . $warehouse->id,
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $rack->update($validated);
        return redirect()->route('warehouses.show', $warehouse)->with('success', 'Rack updated successfully');
    }

    public function destroyRack(Warehouse $warehouse, Rack $rack)
    {
        $rack->delete();
        return redirect()->route('warehouses.show', $warehouse)->with('success', 'Rack deleted successfully');
    }
}
