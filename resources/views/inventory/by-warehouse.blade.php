@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                {{ $warehouse->name }}
                <small class="text-muted">Warehouse</small>
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Warehouse Details</h6>
                    <p class="mb-2">
                        <strong>Manager:</strong> {{ $warehouse->manager_name ?? 'N/A' }}
                    </p>
                    <p class="mb-2">
                        <strong>Location:</strong> {{ $warehouse->location ?? 'N/A' }}
                    </p>
                    <p class="mb-0">
                        <strong>Total Racks:</strong> {{ $warehouse->racks->count() }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title text-white-50">Total Items in Stock</h6>
                    <h2 class="mb-0">{{ $stocks->total() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Stock by Product</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Location (Rack)</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                            <tr>
                                <td>
                                    <strong>{{ $stock->product->name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $stock->product->sku }}</span>
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
                                    <small class="text-muted">{{ $stock->product->unit_of_measure }}</small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('inventory.by-product', $stock->product->id) }}" class="btn btn-sm btn-outline-primary" title="View product">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No stock in this warehouse
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
