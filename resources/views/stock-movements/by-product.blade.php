@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                {{ $product->name }}
                <small class="text-muted">({{ $product->sku }})</small>
            </h1>
            <small class="text-muted">Movement history</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>From Location</th>
                            <th>To Location</th>
                            <th class="text-end">Quantity</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            @foreach($movement->items as $item)
                                @if($item->product_id === $product->id)
                                    <tr>
                                        <td>
                                            <small>{{ $movement->created_at->format('M d, Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $movement->getMovementTypeBadgeColor() }}">
                                                {{ $movement->getMovementTypeLabel() }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('stock-movements.show', $movement) }}" class="text-decoration-none">
                                                {{ $movement->reference_type }}
                                                @if($movement->reference_id)
                                                    #{{ $movement->reference_id }}
                                                @endif
                                            </a>
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
                                        </td>
                                        <td>
                                            {{ $movement->user->name }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No movements for this product
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
