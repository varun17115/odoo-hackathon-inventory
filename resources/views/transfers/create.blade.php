@extends('layouts.app')

@section('title', 'Create Transfer - Invento Market')
@section('page-title', 'Create Transfer')

@section('content')
<div class="max-w-6xl">
    <div class="bg-white rounded-lg shadow p-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Create New Transfer</h3>

        <form method="POST" action="{{ route('transfers.store') }}" id="transferForm">
            @csrf
            
            <!-- Transfer Header -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label for="transfer_number" class="block text-gray-700 font-semibold mb-2">Transfer Number</label>
                    <input type="text" id="transfer_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" value="{{ $transferNumber }}" disabled>
                </div>
                <div>
                    <label for="from_warehouse_id" class="block text-gray-700 font-semibold mb-2">From Warehouse *</label>
                    <select id="from_warehouse_id" name="from_warehouse_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                        <option value="">-- Select Warehouse --</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="to_warehouse_id" class="block text-gray-700 font-semibold mb-2">To Warehouse *</label>
                    <select id="to_warehouse_id" name="to_warehouse_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                        <option value="">-- Select Warehouse --</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="transfer_date" class="block text-gray-700 font-semibold mb-2">Transfer Date *</label>
                    <input type="date" id="transfer_date" name="transfer_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" value="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label for="notes" class="block text-gray-700 font-semibold mb-2">Notes</label>
                    <input type="text" id="notes" name="notes" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="Optional notes">
                </div>
            </div>

            <!-- Transfer Items -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Transfer Items</h4>
                    <button type="button" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition" onclick="addTransferItem()">
                        + Add Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Product</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Quantity</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">From Rack</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">To Rack</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            <!-- Items will be added here -->
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <p class="text-lg font-semibold text-gray-800">Total Quantity: <span id="totalQuantity">0</span></p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    Create Transfer
                </button>
                <a href="{{ route('transfers.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-semibold">
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

function addTransferItem() {
    itemCount++;
    
    // Build racks options
    let fromRacksOptions = '<option value="">-- Select Rack --</option>';
    let toRacksOptions = '<option value="">-- Select Rack --</option>';
    
    warehouses.forEach(w => {
        if (w.racks && w.racks.length > 0) {
            w.racks.forEach(r => {
                fromRacksOptions += `<option value="${r.id}" data-warehouse="${w.id}">${w.name} - ${r.name}</option>`;
                toRacksOptions += `<option value="${r.id}" data-warehouse="${w.id}">${w.name} - ${r.name}</option>`;
            });
        }
    });
    
    const html = `
        <tr class="border-b border-gray-300" id="item-${itemCount}">
            <td class="px-4 py-2">
                <select name="items[${itemCount}][product_id]" class="w-full px-2 py-1 border border-gray-300 rounded" required>
                    <option value="">-- Select Product --</option>
                    ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                </select>
            </td>
            <td class="px-4 py-2">
                <input type="number" name="items[${itemCount}][quantity]" class="w-full px-2 py-1 border border-gray-300 rounded" min="1" value="1" required onchange="calculateTotal()">
            </td>
            <td class="px-4 py-2">
                <select name="items[${itemCount}][from_rack_id]" class="w-full px-2 py-1 border border-gray-300 rounded">
                    ${fromRacksOptions}
                </select>
            </td>
            <td class="px-4 py-2">
                <select name="items[${itemCount}][to_rack_id]" class="w-full px-2 py-1 border border-gray-300 rounded">
                    ${toRacksOptions}
                </select>
            </td>
            <td class="px-4 py-2 text-center">
                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeTransferItem(${itemCount})">Remove</button>
            </td>
        </tr>
    `;
    document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', html);
}

function removeTransferItem(itemId) {
    document.getElementById(`item-${itemId}`).remove();
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('input[name*="[quantity]"]').forEach(el => {
        total += parseInt(el.value) || 0;
    });
    document.getElementById('totalQuantity').textContent = total;
}

// Add first item on load
window.addEventListener('load', () => {
    addTransferItem();
});
</script>
@endsection
