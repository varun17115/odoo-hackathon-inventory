@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Stock Movement Ledger</h1>
            <small class="text-muted">Complete audit trail of all stock movements</small>
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
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Product</th>
                            <th>From</th>
                            <th>To</th>
                            <th class="text-end">Qty</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            @foreach($movement->items as $item)
                                <tr>
                                    <td>
                                        <small>{{ $movement->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $movement->getMovementTypeBadgeColor() }}">
                                            {{ substr($movement->getMovementTypeLabel(), 0, 3) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <a href="{{ route('stock-movements.show', $movement) }}" class="text-decoration-none">
                                                {{ $movement->reference_type }}
                                                @if($movement->reference_id)
                                                    #{{ $movement->reference_id }}
                                                @endif
                                            </a>
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            <strong>{{ $item->product->name }}</strong>
                                            <br>
                                            <span class="text-muted">{{ $item->product->sku }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            @if($item->sourceLocation)
                                                {{ $item->sourceLocation->name }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            @if($item->destinationLocation)
                                                {{ $item->destinationLocation->name }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <small><strong>{{ $item->quantity }}</strong></small>
                                    </td>
                                    <td>
                                        <small>{{ $movement->user->name }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No movements recorded
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
