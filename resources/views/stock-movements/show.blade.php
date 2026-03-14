@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                Movement Details
                <span class="badge bg-{{ $movement->getMovementTypeBadgeColor() }}">
                    {{ $movement->getMovementTypeLabel() }}
                </span>
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Movement Information</h6>
                    <p class="mb-2">
                        <strong>Type:</strong> {{ $movement->getMovementTypeLabel() }}
                    </p>
                    <p class="mb-2">
                        <strong>Reference:</strong> {{ $movement->reference_type }}
                        @if($movement->reference_id)
                            #{{ $movement->reference_id }}
                        @endif
                    </p>
                    <p class="mb-2">
                        <strong>Created By:</strong> {{ $movement->user->name }}
                    </p>
                    <p class="mb-0">
                        <strong>Date:</strong> {{ $movement->created_at->format('M d, Y H:i:s') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Summary</h6>
                    <p class="mb-2">
                        <strong>Total Items:</strong> {{ $movement->items->count() }}
                    </p>
                    <p class="mb-2">
                        <strong>Total Quantity:</strong> {{ $movement->items->sum('quantity') }}
                    </p>
                    @if($movement->notes)
                        <p class="mb-0">
                            <strong>Notes:</strong> {{ $movement->notes }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Movement Items</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>From Location</th>
                            <th>To Location</th>
                            <th class="text-end">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movement->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $item->product->sku }}</span>
                                </td>
                                <td>
                                    @if($item->sourceLocation)
                                        <span class="badge bg-info">{{ $item->sourceLocation->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->destinationLocation)
                                        <span class="badge bg-success">{{ $item->destinationLocation->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong>{{ $item->quantity }}</strong>
                                    <small class="text-muted">{{ $item->product->unit_of_measure }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No items in this movement
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
