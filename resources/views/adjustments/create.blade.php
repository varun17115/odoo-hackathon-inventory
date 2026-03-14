@extends('layouts.app')
@section('title', 'New Stock Adjustment')
@section('page-title', 'New Stock Adjustment')

@section('content')
<div class="mb-4">
    <a href="{{ route('adjustments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
        <i class="fas fa-arrow-left mr-1"></i>Back to Adjustments
    </a>
</div>

<form method="POST" action="{{ route('adjustments.store') }}" id="adjustmentForm">
@csrf
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: header --}}
    <div class="lg:col-span-2 space-y-6">

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Adjustment Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warehouse *</label>
                    <select name="warehouse_id" id="warehouseSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" required onchange="loadWarehouseRacks(this.value)">
                        <option value="">-- Select Warehouse --</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" data-racks="{{ $wh->racks->toJson() }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <input type="text" name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" placeholder="Reason for adjustment (e.g. physical count, damage)">
                </div>
            </div>
        </div>

        {{-- Items --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-semibold text-gray-800">Products to Adjust</h3>
                <button type="button" onclick="addRow()" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                    <i class="fas fa-plus mr-1"></i>Add Product
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="itemsTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase">Product</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase">Rack / Location</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 uppercase">System Qty</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 uppercase">Counted Qty *</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-600 uppercase">Difference</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        {{-- rows added by JS --}}
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-gray-500 mt-3"><i class="fas fa-info-circle mr-1"></i>Enter the physically counted quantity. The system will calculate the difference and update stock accordingly.</p>
        </div>

    </div>

    {{-- Right: summary --}}
    <div>
        <div class="bg-white rounded-lg shadow p-6 sticky top-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Summary</h3>
            <div id="summaryBox" class="space-y-2 text-sm text-gray-600 mb-6">
                <p>Add products to see the adjustment summary.</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4 text-xs text-yellow-800">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                This will immediately update stock quantities. This action cannot be undone.
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold">
                <i class="fas fa-check mr-2"></i>Apply Adjustment
            </button>
        </div>
    </div>

</div>
</form>

<script>
const products = @json($products);
let racks = [];
let rowIndex = 0;

function loadWarehouseRacks(warehouseId) {
    const sel = document.getElementById('warehouseSelect');
    const opt = sel.options[sel.selectedIndex];
    racks = warehouseId ? JSON.parse(opt.dataset.racks || '[]') : [];
    // Refresh rack dropdowns in existing rows
    document.querySelectorAll('.rack-select').forEach(s => {
        const current = s.value;
        s.innerHTML = rackOptions();
        s.value = current;
    });
}

function rackOptions() {
    let html = '<option value="">No specific rack</option>';
    racks.forEach(r => { html += `<option value="${r.id}">${r.name}</option>`; });
    return html;
}

function addRow() {
    const tbody = document.getElementById('itemsBody');
    const i = rowIndex++;
    const productOpts = products.map(p => `<option value="${p.id}" data-unit="${p.unit}">${p.name} (${p.sku})</option>`).join('');

    const tr = document.createElement('tr');
    tr.className = 'border-t border-gray-100';
    tr.id = `row_${i}`;
    tr.innerHTML = `
        <td class="px-3 py-2">
            <select name="items[${i}][product_id]" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm product-select" required onchange="fetchSystemQty(${i})">
                <option value="">-- Select --</option>
                ${productOpts}
            </select>
        </td>
        <td class="px-3 py-2">
            <select name="items[${i}][rack_id]" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm rack-select" onchange="fetchSystemQty(${i})">
                ${rackOptions()}
            </select>
        </td>
        <td class="px-3 py-2 text-right">
            <span id="sys_${i}" class="text-gray-500 font-mono">—</span>
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[${i}][counted_quantity]" id="counted_${i}" min="0" class="w-24 px-2 py-1.5 border border-gray-300 rounded text-sm text-right font-mono" required oninput="updateDiff(${i})">
        </td>
        <td class="px-3 py-2 text-right">
            <span id="diff_${i}" class="font-mono font-semibold text-gray-400">—</span>
        </td>
        <td class="px-3 py-2 text-center">
            <button type="button" onclick="removeRow(${i})" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
        </td>
    `;
    tbody.appendChild(tr);
    updateSummary();
}

function removeRow(i) {
    document.getElementById(`row_${i}`)?.remove();
    updateSummary();
}

async function fetchSystemQty(i) {
    const productId = document.querySelector(`[name="items[${i}][product_id]"]`)?.value;
    const rackId    = document.querySelector(`[name="items[${i}][rack_id]"]`)?.value;
    const warehouseId = document.getElementById('warehouseSelect').value;

    if (!productId || !warehouseId) return;

    try {
        const res = await fetch(`/api/stock?product_id=${productId}&warehouse_id=${warehouseId}&rack_id=${rackId}`);
        const data = await res.json();
        const qty = data.quantity ?? 0;
        document.getElementById(`sys_${i}`).textContent = qty;
        document.getElementById(`sys_${i}`).dataset.qty = qty;
        updateDiff(i);
    } catch(e) {
        document.getElementById(`sys_${i}`).textContent = '0';
        document.getElementById(`sys_${i}`).dataset.qty = 0;
    }
}

function updateDiff(i) {
    const sysEl    = document.getElementById(`sys_${i}`);
    const countedEl = document.getElementById(`counted_${i}`);
    const diffEl   = document.getElementById(`diff_${i}`);

    const sys     = parseInt(sysEl.dataset.qty ?? 0);
    const counted = parseInt(countedEl.value);

    if (isNaN(counted)) { diffEl.textContent = '—'; diffEl.className = 'font-mono font-semibold text-gray-400'; return; }

    const diff = counted - sys;
    diffEl.textContent = (diff >= 0 ? '+' : '') + diff;
    diffEl.className = 'font-mono font-semibold ' + (diff > 0 ? 'text-green-600' : diff < 0 ? 'text-red-600' : 'text-gray-400');
    updateSummary();
}

function updateSummary() {
    const rows = document.querySelectorAll('#itemsBody tr');
    if (rows.length === 0) {
        document.getElementById('summaryBox').innerHTML = '<p>Add products to see the adjustment summary.</p>';
        return;
    }
    let increases = 0, decreases = 0, unchanged = 0;
    rows.forEach(row => {
        const i = row.id.replace('row_', '');
        const diffEl = document.getElementById(`diff_${i}`);
        if (!diffEl || diffEl.textContent === '—') { unchanged++; return; }
        const v = parseInt(diffEl.textContent);
        if (v > 0) increases++;
        else if (v < 0) decreases++;
        else unchanged++;
    });
    document.getElementById('summaryBox').innerHTML = `
        <div class="flex justify-between"><span>Products:</span><span class="font-semibold">${rows.length}</span></div>
        <div class="flex justify-between text-green-600"><span>Increases:</span><span class="font-semibold">${increases}</span></div>
        <div class="flex justify-between text-red-600"><span>Decreases:</span><span class="font-semibold">${decreases}</span></div>
        <div class="flex justify-between text-gray-500"><span>No change:</span><span class="font-semibold">${unchanged}</span></div>
    `;
}

document.getElementById('adjustmentForm').addEventListener('submit', function(e) {
    const rows = document.querySelectorAll('#itemsBody tr');
    if (rows.length === 0) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'No products', text: 'Add at least one product to adjust.' });
        return;
    }
    if (!document.getElementById('warehouseSelect').value) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'No warehouse', text: 'Please select a warehouse.' });
        return;
    }
    Swal.fire({
        title: 'Apply Adjustment?',
        text: 'This will immediately update stock quantities.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ea580c',
        confirmButtonText: 'Yes, apply it'
    }).then(r => { if (r.isConfirmed) this.submit(); });
    e.preventDefault();
});
</script>
@endsection
