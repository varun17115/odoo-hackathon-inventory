@extends('layouts.app')
@section('page-title', 'Products')

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h2>Products</h2>
        <p>Manage your global inventory and product catalog.</p>
    </div>
    <button class="btn-primary-sm" onclick="openModal()">
        <i class="fas fa-plus"></i> New Product
    </button>
</div>

{{-- Filters --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('products.index') }}">
        <div class="filter-grid">
            <div>
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name or SKU...">
            </div>
            <div>
                <label class="form-label">Category</label>
                <select name="category_id" class="form-control">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="is_active" class="form-control">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div style="display:flex;align-items:flex-end;gap:8px;">
                <button type="submit" class="btn-primary-sm" style="flex:1;justify-content:center;">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search','category_id','is_active']))
                    <a href="{{ route('products.index') }}" class="btn-secondary-sm"><i class="fas fa-times"></i></a>
                @endif
            </div>
        </div>
    </form>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            @php $totalStock = $product->stocks->sum('quantity'); @endphp
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;color:#2563eb;flex-shrink:0;">
                            <i class="fas fa-box" style="font-size:0.8rem;"></i>
                        </div>
                        <div>
                            <div style="font-weight:600;color:#0f172a;font-size:0.85rem;">{{ $product->name }}</div>
                            <div style="font-size:0.7rem;color:#94a3b8;font-family:monospace;">{{ $product->sku }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge badge-gray">{{ $product->category->name ?? 'Uncategorized' }}</span>
                </td>
                <td>
                    <div style="font-weight:600;color:#0f172a;font-size:0.85rem;">${{ number_format($product->price, 2) }}</div>
                    @if($product->cost)
                        <div style="font-size:0.72rem;color:#94a3b8;">Cost: ${{ number_format($product->cost, 2) }}</div>
                    @endif
                </td>
                <td>
                    @php $rule = $product->reorderRules->first(); @endphp
                    <span class="badge {{ $totalStock == 0 ? 'badge-red' : ($totalStock < 10 ? 'badge-yellow' : 'badge-green') }}">
                        {{ number_format($totalStock) }} {{ $product->unit }}
                    </span>
                    @if($rule && $totalStock <= $rule->min_quantity)
                        <i class="fas fa-exclamation-triangle" style="color:#ef4444;font-size:0.7rem;margin-left:4px;" title="Below reorder point"></i>
                    @endif
                </td>
                <td>
                    @if($product->is_active)
                        <span class="badge badge-green"><i class="fas fa-circle" style="font-size:0.4rem;margin-right:4px;"></i>Active</span>
                    @else
                        <span class="badge badge-gray">Inactive</span>
                    @endif
                </td>
                <td style="text-align:right;">
                    <a href="{{ route('products.show', $product) }}" class="icon-btn view" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button class="icon-btn edit" onclick='openEditModal(@json($product))' title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <form method="POST" action="{{ route('products.destroy', $product) }}" style="display:inline;" onsubmit="return confirmDelete(this)">
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
                        <i class="fas fa-box-open"></i>
                        <p>No products found. Add your first product.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">{{ $products->links() }}</div>

{{-- Product Modal --}}
<div id="prodModal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)closeModal()">
    <div class="modal-box modal-box-lg">
        <div class="modal-head">
            <h3 id="modalTitle">New Product</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="prodForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div class="form-group">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" id="pName" class="form-control" required placeholder="e.g. Wireless Mouse">
                </div>
                <div class="form-group">
                    <label class="form-label">SKU / Barcode *</label>
                    <input type="text" name="sku" id="pSku" class="form-control" required placeholder="e.g. WM-001">
                </div>
                <div class="form-group">
                    <label class="form-label">Category *</label>
                    <select name="category_id" id="pCategory" class="form-control" required>
                        <option value="">-- Select --</option>
                        @foreach(\App\Models\Category::all() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Unit *</label>
                    <select name="unit" id="pUnit" class="form-control" required>
                        <option value="">-- Select --</option>
                        @foreach(['Piece','Box','Kg','Liter','Meter','Dozen','Pack','Carton'] as $u)
                            <option value="{{ $u }}">{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Selling Price *</label>
                    <input type="number" name="price" id="pPrice" class="form-control" required step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label class="form-label">Cost Price</label>
                    <input type="number" name="cost" id="pCost" class="form-control" step="0.01" min="0" placeholder="0.00">
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="pDesc" class="form-control" rows="2" placeholder="Optional product notes..."></textarea>
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
    document.getElementById('modalTitle').textContent = 'New Product';
    document.getElementById('prodForm').action = '{{ route('products.store') }}';
    document.getElementById('formMethod').value = 'POST';
    ['pName','pSku','pPrice','pCost','pDesc'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('pCategory').value = '';
    document.getElementById('pUnit').value = '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-check"></i> Save';
    document.getElementById('prodModal').style.display = 'flex';
}
function openEditModal(p) {
    document.getElementById('modalTitle').textContent = 'Edit Product';
    document.getElementById('prodForm').action = '/products/' + p.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('pName').value = p.name || '';
    document.getElementById('pSku').value = p.sku || '';
    document.getElementById('pPrice').value = p.price || '';
    document.getElementById('pCost').value = p.cost || '';
    document.getElementById('pDesc').value = p.description || '';
    document.getElementById('pCategory').value = p.category_id || '';
    document.getElementById('pUnit').value = p.unit || '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-check"></i> Update';
    document.getElementById('prodModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('prodModal').style.display = 'none';
}
</script>
@endsection
