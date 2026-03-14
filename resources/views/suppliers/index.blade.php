@extends('layouts.app')
@section('page-title', 'Suppliers')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h2>Suppliers</h2>
        <p>Manage your procurement network and contact points.</p>
    </div>
    <button class="btn-primary-sm" onclick="openModal()">
        <i class="fas fa-plus"></i> Add Supplier
    </button>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Contact Info</th>
                <th>Location</th>
                <th>Receipts</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $supplier)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;color:#16a34a;flex-shrink:0;">
                            <i class="fas fa-industry" style="font-size:0.8rem;"></i>
                        </div>
                        <div>
                            <div style="font-weight:600;color:#0f172a;font-size:0.85rem;">{{ $supplier->name }}</div>
                            <div style="font-size:0.72rem;color:#94a3b8;">{{ $supplier->contact_person ?? 'No contact' }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-size:0.82rem;font-weight:600;color:#374151;">{{ $supplier->email ?? '—' }}</div>
                    <div style="font-size:0.72rem;color:#94a3b8;">{{ $supplier->phone ?? '—' }}</div>
                </td>
                <td>
                    <div style="font-size:0.82rem;font-weight:600;color:#374151;">{{ $supplier->city ?? '—' }}</div>
                    <div style="font-size:0.72rem;color:#94a3b8;">{{ $supplier->country ?? '—' }}</div>
                </td>
                <td>
                    <span class="badge badge-blue">{{ $supplier->receipts_count }} Receipts</span>
                </td>
                <td>
                    @if($supplier->is_active)
                        <span class="badge badge-green"><i class="fas fa-circle" style="font-size:0.4rem;margin-right:4px;"></i>Active</span>
                    @else
                        <span class="badge badge-gray">Inactive</span>
                    @endif
                </td>
                <td style="text-align:right;">
                    <button class="icon-btn edit" onclick='openEditModal(@json($supplier))' title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" style="display:inline;" onsubmit="return confirmDelete(this)">
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
                        <i class="fas fa-industry"></i>
                        <p>No suppliers registered yet.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">{{ $suppliers->links() }}</div>

{{-- Modal --}}
<div id="supModal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)closeModal()">
    <div class="modal-box modal-box-lg">
        <div class="modal-head">
            <h3 id="modalTitle">Add Supplier</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="supForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Supplier Name *</label>
                    <input type="text" name="name" id="supName" class="form-control" required placeholder="Company or individual name">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Person</label>
                    <input type="text" name="contact_person" id="supContact" class="form-control" placeholder="Primary contact name">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" id="supEmail" class="form-control" placeholder="email@example.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" id="supPhone" class="form-control" placeholder="+1 234 567 8900">
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" id="supAddress" class="form-control" placeholder="Street address">
                </div>
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" name="city" id="supCity" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" id="supCountry" class="form-control">
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" id="supNotes" class="form-control" rows="2" placeholder="Business notes, terms..."></textarea>
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
const fields = ['supName','supContact','supEmail','supPhone','supAddress','supCity','supCountry','supNotes'];
function openModal() {
    document.getElementById('modalTitle').textContent = 'Add Supplier';
    document.getElementById('supForm').action = '{{ route('suppliers.store') }}';
    document.getElementById('formMethod').value = 'POST';
    fields.forEach(id => document.getElementById(id).value = '');
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-check"></i> Save';
    document.getElementById('supModal').style.display = 'flex';
}
function openEditModal(s) {
    document.getElementById('modalTitle').textContent = 'Edit Supplier';
    document.getElementById('supForm').action = '/suppliers/' + s.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('supName').value = s.name || '';
    document.getElementById('supContact').value = s.contact_person || '';
    document.getElementById('supEmail').value = s.email || '';
    document.getElementById('supPhone').value = s.phone || '';
    document.getElementById('supAddress').value = s.address || '';
    document.getElementById('supCity').value = s.city || '';
    document.getElementById('supCountry').value = s.country || '';
    document.getElementById('supNotes').value = s.notes || '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-check"></i> Update';
    document.getElementById('supModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('supModal').style.display = 'none';
}
</script>
@endsection
