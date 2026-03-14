@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Create Delivery</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('deliveries.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: '<ul class="text-start">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                showConfirmButton: true
            });
        </script>
    @endif

    <form action="{{ route('deliveries.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Delivery Number</label>
                            <input type="text" class="form-control" value="{{ $deliveryNumber }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Customer Name *</label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Delivery Address *</label>
                            <textarea name="delivery_address" class="form-control" rows="3" required>{{ old('delivery_address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Delivery Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Warehouse *</label>
                            <select name="warehouse_id" id="warehouse_id" class="form-select" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Delivery Date *</label>
                            <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Delivery Items</h5>
                <button type="button" class="btn btn-sm btn-success" onclick="addDeliveryItem()">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
            <div class="card-body">
                <div id="delivery-items">
                    <!-- Items will be added here -->
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Delivery
            </button>
        </div>
    </form>
</div>

<script>
let itemIndex = 0;
const warehouses = @json($warehouses);
const products = @json($products);

function getCurrentRacks() {
    const warehouseId = document.getElementById('warehouse_id').value;
    const warehouse = warehouses.find(w => w.id == warehouseId);
    return warehouse?.racks || [];
}

function addDeliveryItem() {
    const warehouseId = document.getElementById('warehouse_id').value;
    if (!warehouseId) {
        Swal.fire({ icon: 'warning', title: 'No warehouse selected', text: 'Please select a warehouse first.', timer: 2000, showConfirmButton: false });
        return;
    }

    const racks = getCurrentRacks();
    const racksOptions = racks.map(r => `<option value="${r.id}">${r.name}</option>`).join('');
    const productsOptions = products.map(p => `<option value="${p.id}" data-price="${p.price}">${p.name} (${p.sku})</option>`).join('');

    const itemHtml = `
        <div class="row mb-3 delivery-item" id="item-${itemIndex}">
            <div class="col-md-3">
                <label class="form-label">Product *</label>
                <select name="items[${itemIndex}][product_id]" class="form-select" required onchange="updatePrice(${itemIndex})">
                    <option value="">Select Product</option>
                    ${productsOptions}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantity *</label>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Unit Price *</label>
                <input type="number" name="items[${itemIndex}][unit_price]" class="form-control" step="0.01" min="0" required id="price-${itemIndex}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Rack</label>
                <select name="items[${itemIndex}][rack_id]" class="form-select rack-select-${itemIndex}">
                    <option value="">No specific rack</option>
                    ${racksOptions}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-danger w-100" onclick="removeItem(${itemIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    document.getElementById('delivery-items').insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
}

// When warehouse changes, refresh rack dropdowns in existing rows
document.getElementById('warehouse_id').addEventListener('change', function() {
    const racks = getCurrentRacks();
    const racksOptions = '<option value="">No specific rack</option>' + racks.map(r => `<option value="${r.id}">${r.name}</option>`).join('');
    document.querySelectorAll('[class^="rack-select-"]').forEach(sel => { sel.innerHTML = racksOptions; });
});

function updatePrice(index) {
    const select = document.querySelector(`select[name="items[${index}][product_id]"]`);
    const priceInput = document.getElementById(`price-${index}`);
    const price = select.options[select.selectedIndex].getAttribute('data-price');
    if (price) priceInput.value = price;
}

function removeItem(index) {
    document.getElementById(`item-${index}`).remove();
}
</script>
@endsection
