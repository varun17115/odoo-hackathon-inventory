@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                {{ ucfirst($type) }} Movements
            </h1>
            <small class="text-muted">All {{ $type }} transactions</small>
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
                            <th>Reference</th>
                            <th>Product</th>
                            <th>From Location</th>
                            <th>To Location</th>
                            <th class="text-end">Quantity</th>
                            <th>User</th>
                            <th class="text-center">Actions</th>
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
                                        <a href="{{ route('stock-movements.show', $movement) }}" class="text-decoration-none">
                                            {{ $movement->reference_type }}
                                            @if($movement->reference_id)
                                                #{{ $movement->reference_id }}
                                            @endif
                                        </a>
                                    </td>
                                    <td>
                                        <strong>{{ $item->product->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $item->product->sku }}</small>
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
                                    <td class="text-center">
                                        <a href="{{ route('stock-movements.show', $movement) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No {{ $type }} movements recorded
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
