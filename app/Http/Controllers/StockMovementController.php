<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index()
    {
        $movements = StockMovement::with(['user', 'items.product', 'items.sourceLocation', 'items.destinationLocation'])
            ->latest()
            ->paginate(20);

        return view('stock-movements.index', compact('movements'));
    }

    public function show(StockMovement $movement)
    {
        $movement->load(['user', 'items.product', 'items.sourceLocation', 'items.destinationLocation']);
        return view('stock-movements.show', compact('movement'));
    }

    public function byProduct($productId)
    {
        $product = Product::findOrFail($productId);
        
        $movements = StockMovement::whereHas('items', function ($query) use ($productId) {
            $query->where('product_id', $productId);
        })
        ->with(['user', 'items.product', 'items.sourceLocation', 'items.destinationLocation'])
        ->latest()
        ->paginate(20);

        return view('stock-movements.by-product', compact('product', 'movements'));
    }

    public function byType($type)
    {
        $validTypes = ['receipt', 'delivery', 'transfer', 'adjustment'];
        if (!in_array($type, $validTypes)) {
            abort(404);
        }

        $movements = StockMovement::where('movement_type', $type)
            ->with(['user', 'items.product', 'items.sourceLocation', 'items.destinationLocation'])
            ->latest()
            ->paginate(20);

        return view('stock-movements.by-type', compact('movements', 'type'));
    }

    public function ledger()
    {
        $movements = StockMovement::with(['user', 'items.product', 'items.sourceLocation', 'items.destinationLocation'])
            ->latest()
            ->get();

        return view('stock-movements.ledger', compact('movements'));
    }
}
