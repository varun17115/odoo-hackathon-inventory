@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                {{ $product->name }}
                <small class="text-muted">({{ $product->sku }})</small>
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Total Stock</h6>
                    <h2 class="mb-0">{{ $totalStock }}</h2>
                    <small>{{ $product->unit_of_measure }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Unit Price</h6>
                    <h2 class="mb-0">${{ number_format($product->price, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Stock Value</h6>
                    <h2 class="mb-0">${{ number_format($totalStock * $product->price, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Locations</h6>
                    <h2 class="mb-0">{{ $stocks->total() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Stock by Location</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Warehouse</th>
                            <th>Location (Rack)</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                            <tr>
                                <td>
                                    <strong>{{ $stock->warehouse->name }}</strong>
                                </td>
                                <td>
                                    @if($stock->location)
                                        <span class="badge bg-primary">{{ $stock->location->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong>{{ $stock->quantity }}</strong>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('inventory.by-warehouse', $stock->warehouse->id) }}" class="btn btn-sm btn-outline-secondary" title="View warehouse">
                                        <i class="fas fa-warehouse"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No stock records for this product
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $stocks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
