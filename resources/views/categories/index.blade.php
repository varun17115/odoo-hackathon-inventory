@extends('layouts.app')
@section('page-title', 'Categories')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h2>Categories</h2>
        <p>Organize your product catalog into logical groups.</p>
    </div>
    <button class="btn-primary-sm" onclick="openModal()">
        <i class="fas fa-plus"></i> Add Category
    </button>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Description</th>
                <th>Products</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;color:#2563eb;flex-shrink:0;">
                            <i class="fas fa-tag" style="font-size:0.8rem;"></i>
                        </div>
                        <span style="font-weight:600;color:#0f172a;font-size:0.85rem;">{{ $category->name }}</span>
                    </div>
                </td>
                <td style="color:#64748b;font-size:0.82rem;max-width:260px;">
                    {{ $category->description ?? '—' }}
                </td>
                <td>
                    <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="badge badge-blue" style="text-decoration:none;">
                        {{ $category->products_count }} Products
                    </a>
                </td>
                <td style="text-align:right;">
                    <button class="icon-btn edit" onclick='openEditModal(@json($category))' title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <form method="POST" action="{{ route('categories.destroy', $category) }}" style="display:inline;" onsubmit="return confirmDelete(this)">
                        @csrf @method('DELETE')
                        <button type="submit" class="icon-btn delete" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4">
                    <div class="empty-state">
                        <i class="fas fa-tags"></i>
                        <p>No categories yet. Create your first one.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">{{ $categories->links() }}</div>

{{-- Modal --}}
<div id="catModal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)closeModal()">
    <div class="modal-box">
        <div class="modal-head">
            <h3 id="modalTitle">Add Category</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="catForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" id="catName" class="form-control" required placeholder="e.g. Electronics">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="catDesc" class="form-control" rows="3" placeholder="Optional description..."></textarea>
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
    document.getElementById('modalTitle').textContent = 'Add Category';
    document.getElementById('catForm').action = '{{ route('categories.store') }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('catName').value = '';
    document.getElementById('catDesc').value = '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-check"></i> Save';
    document.getElementById('catModal').style.display = 'flex';
}
function openEditModal(cat) {
    document.getElementById('modalTitle').textContent = 'Edit Category';
    document.getElementById('catForm').action = '/categories/' + cat.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('catName').value = cat.name;
    document.getElementById('catDesc').value = cat.description || '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-check"></i> Update';
    document.getElementById('catModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('catModal').style.display = 'none';
}
</script>
@endsection
