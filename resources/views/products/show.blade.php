@extends('layouts.app')

@section('title', $product->name . ' - Invento Market')
@section('page-title', $product->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Product Details -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">{{ $product->name }}</h2>
                    <p class="text-gray-600 mt-1">SKU: <span class="font-semibold">{{ $product->sku }}</span></p>
                </div>
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition" data-bs-toggle="modal" data-bs-target="#productModal" onclick="editProduct({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->sku }}', {{ $product->category_id }}, '{{ $product->unit }}', {{ $product->price }}, {{ $product->cost ?? 'null' }}, '{{ addslashes($product->description) }}')">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                    <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" onclick="confirmDelete(this.closest('form'))">
                            <i class="fas fa-trash mr-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <p class="text-gray-600 text-sm">Category</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $product->category->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Unit of Measure</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $product->unit }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Selling Price</p>
                    <p class="text-lg font-semibold text-gray-800">${{ number_format($product->price, 2) }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Cost Price</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $product->cost ? '$' . number_format($product->cost, 2) : 'N/A' }}</p>
                </div>
            </div>

            @if($product->description)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                    <p class="text-gray-600">{{ $product->description }}</p>
                </div>
            @endif

            <div class="border-t pt-6">
                <p class="text-gray-600 text-sm">Status</p>
                <div class="mt-2">
                    @if($product->is_active)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Stock Info -->
    <div>
        <!-- Total Stock Summary -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-boxes mr-2 text-blue-500"></i>Stock Summary
            </h3>
            <div class="space-y-4">
                <div>
                    <p class="text-gray-600 text-sm">Total Stock</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($totalStock) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $product->unit }}</p>
                </div>
                <div class="pt-4 border-t">
                    <p class="text-gray-600 text-sm mb-2">Stock Status</p>
                    @if($totalStock == 0)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i>Out of Stock
                        </span>
                    @elseif($totalStock < 10)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Low Stock
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>In Stock
                        </span>
                    @endif
                </div>
                <div class="pt-4 border-t">
                    <p class="text-gray-600 text-sm">Locations</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $product->stocks->count() }}</p>
                    <p class="text-xs text-gray-500">Warehouse locations</p>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <a href="{{ route('inventory.index', ['product_id' => $product->id]) }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-warehouse mr-2"></i>View All Locations
                </a>
                <a href="{{ route('stock-movements.by-product', $product->id) }}" class="block w-full text-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-history mr-2"></i>Movement History
                </a>
            </div>
        </div>

        <!-- Stock by Location -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-map-marker-alt mr-2 text-green-500"></i>Stock by Location
            </h3>
            @if($product->stocks->count() > 0)
                <div class="space-y-3">
                    @foreach($product->stocks as $stock)
                        <div class="pb-3 border-b last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">
                                        <i class="fas fa-warehouse text-gray-400 mr-1"></i>
                                        {{ $stock->warehouse->name }}
                                    </p>
                                    @if($stock->location)
                                        <p class="text-xs text-gray-600 ml-4 mt-0.5">
                                            <i class="fas fa-layer-group text-gray-400 mr-1"></i>
                                            {{ $stock->location->name }}
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-400 ml-4 mt-0.5 italic">No specific rack</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold {{ $stock->quantity < 10 ? 'text-yellow-600' : 'text-gray-800' }}">
                                        {{ number_format($stock->quantity) }}
                                    </span>
                                    <p class="text-xs text-gray-500">{{ $product->unit }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <i class="fas fa-box-open text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-600 text-sm">No stock in any location</p>
                    <p class="text-gray-500 text-xs mt-1">Stock appears here after receipts are verified</p>
                </div>
            @endif
        </div>

        <!-- Recent Movements -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-exchange-alt mr-2 text-purple-500"></i>Recent Movements
            </h3>
            @if($recentMovements->count() > 0)
                <div class="space-y-3">
                    @foreach($recentMovements as $movement)
                        @foreach($movement->items as $item)
                            <div class="pb-3 border-b last:border-b-0">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            @if($movement->movement_type === 'receipt')
                                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-arrow-down"></i> Receipt
                                                </span>
                                            @elseif($movement->movement_type === 'delivery')
                                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-arrow-up"></i> Delivery
                                                </span>
                                            @elseif($movement->movement_type === 'transfer')
                                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                    <i class="fas fa-exchange-alt"></i> Transfer
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="fas fa-edit"></i> Adjustment
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $movement->reference_number }}</p>
                                        <p class="text-xs text-gray-400">{{ $movement->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-semibold {{ $movement->movement_type === 'receipt' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $movement->movement_type === 'receipt' ? '+' : '-' }}{{ number_format($item->quantity) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
                <a href="{{ route('stock-movements.by-product', $product->id) }}" class="mt-4 block text-center text-sm text-blue-600 hover:text-blue-800">
                    View All Movements →
                </a>
            @else
                <div class="text-center py-6">
                    <i class="fas fa-history text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-600 text-sm">No movements yet</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-1"></i>Back to Products
    </a>
</div>

<!-- Reorder Rules Section -->
<div class="mt-6 bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-redo mr-2 text-orange-500"></i>Reorder Rules
        </h3>
        <button class="px-3 py-1.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition text-sm"
            data-bs-toggle="modal" data-bs-target="#reorderModal" onclick="resetReorderForm()">
            <i class="fas fa-plus mr-1"></i>Add Rule
        </button>
    </div>

    @if($product->reorderRules->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Warehouse</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Current Stock</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Min Qty</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Reorder Qty</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Supplier</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($product->reorderRules as $rule)
                        @php
                            $currentStock = $rule->warehouse_id
                                ? \App\Models\Stock::getWarehouseTotal($product->id, $rule->warehouse_id)
                                : $totalStock;
                            $triggered = $currentStock <= $rule->min_quantity;
                        @endphp
                        <tr class="{{ $triggered && $rule->is_active ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-3 text-gray-700">{{ $rule->warehouse->name ?? 'All Warehouses' }}</td>
                            <td class="px-4 py-3 font-semibold {{ $triggered ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($currentStock) }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ number_format($rule->min_quantity) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ number_format($rule->reorder_quantity) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $rule->preferredSupplier->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if(!$rule->is_active)
                                    <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">Inactive</span>
                                @elseif($triggered)
                                    <span class="px-2 py-0.5 rounded text-xs bg-red-100 text-red-700">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Triggered
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-xs bg-green-100 text-green-700">OK</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button class="text-yellow-600 hover:text-yellow-800 text-xs"
                                        data-bs-toggle="modal" data-bs-target="#reorderModal"
                                        onclick="editReorderRule({{ $rule->id }}, {{ $rule->warehouse_id ?? 'null' }}, {{ $rule->min_quantity }}, {{ $rule->reorder_quantity }}, {{ $rule->preferred_supplier_id ?? 'null' }}, {{ $rule->is_active ? 'true' : 'false' }})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" action="{{ route('reorder-rules.destroy', $rule) }}" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="text-red-600 hover:text-red-800 text-xs"
                                            onclick="confirmDeleteRule(this.closest('form'))">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-redo text-gray-300 text-3xl mb-2"></i>
            <p class="text-sm">No reorder rules set for this product.</p>
            <button class="mt-2 text-blue-600 hover:underline text-sm"
                data-bs-toggle="modal" data-bs-target="#reorderModal" onclick="resetReorderForm()">
                Add a reorder rule
            </button>
        </div>
    @endif
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="productForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" id="product_name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SKU *</label>
                            <input type="text" class="form-control" id="product_sku" name="sku" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category *</label>
                            <select class="form-control" id="product_category" name="category_id" required>
                                <option value="">-- Select Category --</option>
                                @foreach(\App\Models\Category::all() as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Unit of Measure *</label>
                            <select class="form-control" id="product_unit" name="unit" required>
                                <option value="">-- Select Unit --</option>
                                <option value="Piece">Piece</option>
                                <option value="Box">Box</option>
                                <option value="Kg">Kilogram (Kg)</option>
                                <option value="Liter">Liter (L)</option>
                                <option value="Meter">Meter (m)</option>
                                <option value="Dozen">Dozen</option>
                                <option value="Pack">Pack</option>
                                <option value="Carton">Carton</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Selling Price *</label>
                            <input type="number" class="form-control" id="product_price" name="price" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cost Price</label>
                            <input type="number" class="form-control" id="product_cost" name="cost" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="product_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editProduct(id, name, sku, categoryId, unit, price, cost, description) {
    document.getElementById('product_name').value = name;
    document.getElementById('product_sku').value = sku;
    document.getElementById('product_category').value = categoryId;
    document.getElementById('product_unit').value = unit;
    document.getElementById('product_price').value = price;
    document.getElementById('product_cost').value = cost || '';
    document.getElementById('product_description').value = description;
    document.getElementById('productForm').action = `/products/${id}`;
}

function confirmDelete(form) {
    Swal.fire({
        title: 'Delete Product?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
}
</script>

<!-- Reorder Rule Modal -->
<div class="modal fade" id="reorderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reorderModalTitle">Add Reorder Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reorderForm" method="POST" action="{{ route('reorder-rules.store') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Warehouse <span class="text-muted small">(leave blank for all)</span></label>
                        <select class="form-control" id="rr_warehouse" name="warehouse_id">
                            <option value="">All Warehouses</option>
                            @foreach(\App\Models\Warehouse::orderBy('name')->get() as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Min Quantity * <span class="text-muted small">(trigger)</span></label>
                            <input type="number" class="form-control" id="rr_min_qty" name="min_quantity" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reorder Quantity *</label>
                            <input type="number" class="form-control" id="rr_reorder_qty" name="reorder_quantity" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Preferred Supplier</label>
                        <select class="form-control" id="rr_supplier" name="preferred_supplier_id">
                            <option value="">— None —</option>
                            @foreach(\App\Models\Supplier::orderBy('name')->get() as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rr_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="rr_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="rrSubmitBtn">Save Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetReorderForm() {
    document.getElementById('reorderForm').reset();
    document.getElementById('reorderForm').action = '{{ route('reorder-rules.store') }}';
    document.getElementById('reorderModalTitle').textContent = 'Add Reorder Rule';
    document.getElementById('rrSubmitBtn').textContent = 'Save Rule';
    document.querySelector('#reorderForm input[name="_method"]')?.remove();
    document.getElementById('rr_warehouse').disabled = false;
    document.getElementById('rr_active').checked = true;
}

function editReorderRule(id, warehouseId, minQty, reorderQty, supplierId, isActive) {
    document.getElementById('rr_warehouse').value = warehouseId || '';
    document.getElementById('rr_warehouse').disabled = true;
    document.getElementById('rr_min_qty').value = minQty;
    document.getElementById('rr_reorder_qty').value = reorderQty;
    document.getElementById('rr_supplier').value = supplierId || '';
    document.getElementById('rr_active').checked = isActive;
    document.getElementById('reorderModalTitle').textContent = 'Edit Reorder Rule';
    document.getElementById('rrSubmitBtn').textContent = 'Update Rule';

    document.querySelector('#reorderForm input[name="_method"]')?.remove();
    const m = document.createElement('input');
    m.type = 'hidden'; m.name = '_method'; m.value = 'PUT';
    document.getElementById('reorderForm').appendChild(m);
    document.getElementById('reorderForm').action = `/reorder-rules/${id}`;
}

document.getElementById('reorderModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('rr_warehouse').disabled = false;
});

function confirmDeleteRule(form) {
    Swal.fire({
        title: 'Delete Rule?', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete'
    }).then(r => { if (r.isConfirmed) form.submit(); });
}
</script>
@endsection
