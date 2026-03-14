@extends('layouts.app')
@section('page-title', 'Warehouses')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h2>Warehouses</h2>
        <p>Manage physical storage locations and rack organizations.</p>
    </div>
    <button class="btn-primary-sm" onclick="openModal()">
        <i class="fas fa-plus"></i> Add Warehouse
    </button>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Warehouse</th>
                <th>Location</th>
                <th>Manager</th>
                <th>Racks</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($warehouses as $warehouse)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;color:#2563eb;flex-shrink:0;">
                            <i class="fas fa-warehouse" style="font-size:0.8rem;"></i>
                        </div>
                        <span style="font-weight:600;color:#0f172a;font-size:0.85rem;">{{ $warehouse->name }}</span>
                    </div>
                </td>
                <td style="font-size:0.82rem;color:#475569;">{{ $warehouse->location ?? '—' }}</td>
                <td>
                    <div style="font-size:0.82rem;font-weight:600;color:#0f172a;">{{ $warehouse->manager_name ?? 'Unassigned' }}</div>
                    @if($warehouse->manager_phone)
                        <div style="font-size:0.72rem;color:#94a3b8;">{{ $warehouse->manager_phone }}</div>
                    @endif
                </td>
                <td>
                    <span class="badge badge-blue">{{ $warehouse->racks_count }} Racks</span>
                </td>
                <td>
                    @if($warehouse->is_active)
                        <span class="badge badge-green"><i class="fas fa-circle" style="font-size:0.4rem;margin-right:4px;"></i>Active</span>
                    @else
                        <span class="badge badge-gray">Inactive</span>
                    @endif
                </td>
                <td style="text-align:right;">
                    <a href="{{ route('warehouses.show', $warehouse) }}" class="icon-btn view" title="View Racks">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button class="icon-btn edit" onclick='openEditModal(@json($warehouse))' title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <form method="POST" action="{{ route('warehouses.destroy', $warehouse) }}" style="display:inline;" onsubmit="return confirmDelete(this)">
                        @csrf @method('DELETE')
                        <button type="submit" class="icon-btn delete" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="fas fa-warehouse"></i>
                        <p>No warehouses configured yet.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">{{ $warehouses->links() }}</div>

{{-- Modal --}}
<div id="whModal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)closeModal()">
    <div class="modal-box modal-box-lg">
        <div class="modal-head">
            <h3 id="modalTitle">Add Warehouse</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="whForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Warehouse Name *</label>
                    <input type="text" name="name" id="whName" class="form-control" required>
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Physical Location</label>
                    <input type="text" name="location" id="whLocation" class="form-control" placeholder="e.g. South Logistics Park, Zone B">
                </div>
                <div class="form-group">
                    <label class="form-label">Site Manager</label>
                    <input type="text" name="manager_name" id="whManager" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Manager Phone</label>
                    <input type="text" name="manager_phone" id="whPhone" class="form-control">
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="whDesc" class="form-control" rows="2" placeholder="Facility details, access hours..."></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-secondary-sm" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary-sm" id="submitBtn">
                    <i class="fas fa-check"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('modalTitle').textContent = 'Add Warehouse';
    document.getElementById('whForm').action = '{{ route('warehouses.store') }}';
    document.getElementById('formMethod').value = 'POST';
    ['whName','whLocation','whManager','whPhone','whDesc'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-check"></i> Save';
    document.getElementById('whModal').style.display = 'flex';
}
function openEditModal(wh) {
    document.getElementById('modalTitle').textContent = 'Edit Warehouse';
    document.getElementById('whForm').action = '/warehouses/' + wh.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('whName').value = wh.name || '';
    document.getElementById('whLocation').value = wh.location || '';
    document.getElementById('whManager').value = wh.manager_name || '';
    document.getElementById('whPhone').value = wh.manager_phone || '';
    document.getElementById('whDesc').value = wh.description || '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-check"></i> Update';
    document.getElementById('whModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('whModal').style.display = 'none';
}
</script>
@endsection
