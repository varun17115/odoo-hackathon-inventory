@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Stock Summary</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-list"></i> Detailed View
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-end">Total Stock</th>
                            @foreach($warehouses as $warehouse)
                                <th class="text-end">{{ $warehouse->name }}</th>
                            @endforeach
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($summary as $productId => $data)
                            <tr>
                                <td>
                                    <strong>{{ $data['product']->name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $data['product']->sku }}</span>
                                </td>
                                <td class="text-end">
                                    <strong>{{ $data['total'] }}</strong>
                                    <small class="text-muted">{{ $data['product']->unit_of_measure }}</small>
                                </td>
                                @foreach($warehouses as $warehouse)
                                    <td class="text-end">
                                        {{ $data['by_warehouse'][$warehouse->id] ?? 0 }}
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    <a href="{{ route('inventory.by-product', $data['product']->id) }}" class="btn btn-sm btn-outline-primary" title="View details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + count($warehouses) }}" class="text-center text-muted py-4">
                                    No products found
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
