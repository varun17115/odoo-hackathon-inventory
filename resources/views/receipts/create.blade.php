@extends('layouts.app')

@section('title', 'Create Receipt - Invento Market')
@section('page-title', 'Create Receipt')

@section('content')
<div class="max-w-6xl">
    <div class="bg-white rounded-lg shadow p-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Create New Receipt</h3>

        <form method="POST" action="{{ route('receipts.store') }}" id="receiptForm">
            @csrf
            
            <!-- Receipt Header -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label for="receipt_number" class="block text-gray-700 font-semibold mb-2">Receipt Number</label>
                    <input type="text" id="receipt_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" value="{{ $receiptNumber }}" disabled>
                </div>
                <div>
                    <label for="supplier_id" class="block text-gray-700 font-semibold mb-2">Supplier *</label>
                    <select id="supplier_id" name="supplier_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                        <option value="">-- Select Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="warehouse_id" class="block text-gray-700 font-semibold mb-2">Warehouse</label>
                    <select id="warehouse_id" name="warehouse_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">-- Select Warehouse --</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="receipt_date" class="block text-gray-700 font-semibold mb-2">Receipt Date *</label>
                    <input type="date" id="receipt_date" name="receipt_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" value="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label for="notes" class="block text-gray-700 font-semibold mb-2">Notes</label>
                    <input type="text" id="notes" name="notes" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="Optional notes">
                </div>
            </div>

            <!-- Receipt Items -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Receipt Items</h4>
                    <button type="button" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition" onclick="addReceiptItem()">
                        + Add Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Product</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Quantity</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Unit Price</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Total</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Rack</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            <!-- Items will be added here -->
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <p class="text-lg font-semibold text-gray-800">Total Amount: <span id="totalAmount">$0.00</span></p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    Create Receipt
                </button>
                <a href="{{ route('receipts.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-semibold">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const products = @json($products);
const warehouses = @json($warehouses);
let itemCount = 0;

function addReceiptItem() {
    itemCount++;
    
    // Build racks options
    let racksOptions = '<option value="">-- Select Rack --</option>';
    warehouses.forEach(w => {
        if (w.racks && w.racks.length > 0) {
            w.racks.forEach(r => {
                racksOptions += `<option value="${r.id}">${w.name} - ${r.name}</option>`;
            });
        }
    });
    
    const html = `
        <tr class="border-b border-gray-300" id="item-${itemCount}">
            <td class="px-4 py-2">
                <select name="items[${itemCount}][product_id]" class="w-full px-2 py-1 border border-gray-300 rounded" required onchange="updateItemTotal(${itemCount})">
                    <option value="">-- Select Product --</option>
                    ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                </select>
            </td>
            <td class="px-4 py-2">
                <input type="number" name="items[${itemCount}][quantity]" class="w-full px-2 py-1 border border-gray-300 rounded" min="1" value="1" required onchange="updateItemTotal(${itemCount})">
            </td>
            <td class="px-4 py-2">
                <input type="number" name="items[${itemCount}][unit_price]" class="w-full px-2 py-1 border border-gray-300 rounded" step="0.01" min="0" value="0" required onchange="updateItemTotal(${itemCount})">
            </td>
            <td class="px-4 py-2">
                <input type="text" class="w-full px-2 py-1 border border-gray-300 rounded bg-gray-100" id="total-${itemCount}" value="$0.00" disabled>
            </td>
            <td class="px-4 py-2">
                <select name="items[${itemCount}][rack_id]" class="w-full px-2 py-1 border border-gray-300 rounded">
                    ${racksOptions}
                </select>
            </td>
            <td class="px-4 py-2 text-center">
                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeReceiptItem(${itemCount})">Remove</button>
            </td>
        </tr>
    `;
    document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', html);
}

function removeReceiptItem(itemId) {
    document.getElementById(`item-${itemId}`).remove();
    calculateTotal();
}

function updateItemTotal(itemId) {
    const quantity = document.querySelector(`input[name="items[${itemId}][quantity]"]`).value || 0;
    const unitPrice = document.querySelector(`input[name="items[${itemId}][unit_price]"]`).value || 0;
    const total = quantity * unitPrice;
    document.getElementById(`total-${itemId}`).value = '$' + parseFloat(total).toFixed(2);
    calculateTotal();
}

function calculateTotal() {
    let grandTotal = 0;
    document.querySelectorAll('[id^="total-"]').forEach(el => {
        const value = parseFloat(el.value.replace('$', '')) || 0;
        grandTotal += value;
    });
    document.getElementById('totalAmount').textContent = '$' + grandTotal.toFixed(2);
}

// Add first item on load
window.addEventListener('load', () => {
    addReceiptItem();
});
</script>
@endsection
