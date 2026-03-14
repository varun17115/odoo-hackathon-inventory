@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                Delivery {{ $delivery->delivery_number }}
                <span class="badge bg-{{ $delivery->getStatusBadgeColor() }}">
                    {{ $delivery->getStatusLabel() }}
                </span>
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('deliveries.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Name:</strong> {{ $delivery->customer_name }}
                    </p>
                    @if($delivery->customer_phone)
                        <p class="mb-2">
                            <strong>Phone:</strong> {{ $delivery->customer_phone }}
                        </p>
                    @endif
                    @if($delivery->customer_email)
                        <p class="mb-2">
                            <strong>Email:</strong> {{ $delivery->customer_email }}
                        </p>
                    @endif
                    <p class="mb-0">
                        <strong>Address:</strong><br>
                        {{ $delivery->delivery_address }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Details</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Warehouse:</strong> {{ $delivery->warehouse->name }}
                    </p>
                    <p class="mb-2">
                        <strong>Delivery Date:</strong> {{ $delivery->delivery_date->format('M d, Y') }}
                    </p>
                    <p class="mb-2">
                        <strong>Created By:</strong> {{ $delivery->user->name }}
                    </p>
                    <p class="mb-2">
                        <strong>Total Amount:</strong> ${{ number_format($delivery->total_amount, 2) }}
                    </p>
                    @if($delivery->notes)
                        <p class="mb-0">
                            <strong>Notes:</strong> {{ $delivery->notes }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Delivery Items</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Rack</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Picked</th>
                            <th class="text-center">Packed</th>
                            @if($delivery->status === 'picking')
                                <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($delivery->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $item->product->sku }}</span>
                                </td>
                                <td>
                                    @if($item->rack)
                                        <span class="badge bg-primary">{{ $item->rack->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong>{{ $item->quantity }}</strong>
                                </td>
                                <td class="text-end">
                                    ${{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="text-end">
                                    <strong>${{ number_format($item->total_price, 2) }}</strong>
                                </td>
                                <td class="text-center">
                                    @if($item->is_picked)
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-circle text-muted"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->is_packed)
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-circle text-muted"></i>
                                    @endif
                                </td>
                                @if($delivery->status === 'picking')
                                    <td class="text-center">
                                        @if(!$item->is_picked)
                                            <form action="{{ route('deliveries.mark-picked', [$delivery, $item]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Pick
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Workflow Actions</h5>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                @if($delivery->canPick())
                    <button type="button" class="btn btn-info" onclick="confirmAction('{{ route('deliveries.start-picking', $delivery) }}', 'Start Picking', 'Start picking items for this delivery?')">
                        <i class="fas fa-hand-paper"></i> Start Picking
                    </button>
                @endif

                @if($delivery->canPack())
                    <button type="button" class="btn btn-primary" onclick="confirmAction('{{ route('deliveries.start-packing', $delivery) }}', 'Pack Items', 'Mark all items as packed?')">
                        <i class="fas fa-box"></i> Pack Items
                    </button>
                @endif

                @if($delivery->canValidate())
                    <button type="button" class="btn btn-success" onclick="confirmValidate('{{ route('deliveries.validate', $delivery) }}')">
                        <i class="fas fa-check-double"></i> Validate & Update Stock
                    </button>
                @endif

                @if($delivery->status === 'validated')
                    <button type="button" class="btn btn-dark" onclick="confirmAction('{{ route('deliveries.ship', $delivery) }}', 'Ship Delivery', 'Mark this delivery as shipped?')">
                        <i class="fas fa-shipping-fast"></i> Mark as Shipped
                    </button>
                @endif

                @if($delivery->canCancel())
                    <button type="button" class="btn btn-danger" onclick="confirmCancel('{{ route('deliveries.cancel', $delivery) }}')">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                @endif

                @if($delivery->status === 'draft')
                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('{{ route('deliveries.destroy', $delivery) }}')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        showConfirmButton: true
    });
</script>
@endif

<script>
function confirmAction(url, title, text) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmValidate(url) {
    Swal.fire({
        title: 'Validate Delivery',
        html: '<strong>This will reduce stock quantities!</strong><br>This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, validate and reduce stock!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmCancel(url) {
    Swal.fire({
        title: 'Cancel Delivery',
        text: 'Are you sure you want to cancel this delivery?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmDelete(url) {
    Swal.fire({
        title: 'Delete Delivery',
        text: 'This will permanently delete this delivery. Continue?',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
