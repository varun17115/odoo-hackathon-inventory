@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Market Dashboard')

@section('content')

{{-- ── Dynamic Filter Bar ──────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('dashboard') }}" id="dashFilterForm">
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 18px;margin-bottom:18px;display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;">

    <div style="display:flex;flex-direction:column;gap:4px;min-width:150px;">
        <label style="font-size:0.65rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;">Document Type</label>
        <select name="doc_type" class="form-select form-select-sm" onchange="document.getElementById('dashFilterForm').submit()" style="font-size:0.8rem;border-color:#e2e8f0;">
            <option value="">All Types</option>
            <option value="receipts"    {{ $filterDocType==='receipts'    ? 'selected' : '' }}>Receipts</option>
            <option value="deliveries"  {{ $filterDocType==='deliveries'  ? 'selected' : '' }}>Deliveries</option>
            <option value="transfers"   {{ $filterDocType==='transfers'   ? 'selected' : '' }}>Transfers</option>
            <option value="adjustments" {{ $filterDocType==='adjustments' ? 'selected' : '' }}>Adjustments</option>
        </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:4px;min-width:140px;">
        <label style="font-size:0.65rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;">Status</label>
        <select name="status" class="form-select form-select-sm" onchange="document.getElementById('dashFilterForm').submit()" style="font-size:0.8rem;border-color:#e2e8f0;">
            <option value="">All Statuses</option>
            <option value="draft"    {{ $filterStatus==='draft'    ? 'selected' : '' }}>Draft</option>
            <option value="waiting"  {{ $filterStatus==='waiting'  ? 'selected' : '' }}>Waiting</option>
            <option value="pending"  {{ $filterStatus==='pending'  ? 'selected' : '' }}>Pending</option>
            <option value="ready"    {{ $filterStatus==='ready'    ? 'selected' : '' }}>Ready</option>
            <option value="done"     {{ $filterStatus==='done'     ? 'selected' : '' }}>Done</option>
            <option value="canceled" {{ $filterStatus==='canceled' ? 'selected' : '' }}>Canceled</option>
        </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:4px;min-width:160px;">
        <label style="font-size:0.65rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;">Warehouse</label>
        <select name="warehouse_id" class="form-select form-select-sm" onchange="document.getElementById('dashFilterForm').submit()" style="font-size:0.8rem;border-color:#e2e8f0;">
            <option value="">All Warehouses</option>
            @foreach($warehouses as $wh)
            <option value="{{ $wh->id }}" {{ $filterWarehouse == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
            @endforeach
        </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:4px;min-width:160px;">
        <label style="font-size:0.65rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;">Category</label>
        <select name="category_id" class="form-select form-select-sm" onchange="document.getElementById('dashFilterForm').submit()" style="font-size:0.8rem;border-color:#e2e8f0;">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ $filterCategory == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    @if($filterDocType || $filterStatus || $filterWarehouse || $filterCategory)
    <div style="display:flex;flex-direction:column;gap:4px;justify-content:flex-end;">
        <a href="{{ route('dashboard') }}" style="font-size:0.75rem;font-weight:600;color:#ef4444;text-decoration:none;padding:6px 12px;border:1px solid #fecaca;border-radius:6px;background:#fff5f5;white-space:nowrap;">
            <i class="fas fa-times" style="margin-right:4px;"></i>Clear Filters
        </a>
    </div>
    @endif

</div>
</form>

{{-- ── KPI Row 1 ────────────────────────────────────────────────────────────── --}}
<div class="kpi-grid" style="grid-template-columns:repeat(5,1fr);margin-bottom:14px;">

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-boxes"></i></div>
        <div class="kpi-value">{{ number_format($totalProducts) }}</div>
        <div class="kpi-label">Total Products</div>
        <a href="{{ route('products.index') }}" class="kpi-link" style="color:#2563eb;">Manage <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i></a>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fffbeb;color:#d97706;"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="kpi-value" style="color:{{ $lowStockItems > 0 ? '#d97706' : '#0f172a' }};">{{ number_format($lowStockItems) }}</div>
        <div class="kpi-label">Low Stock Items</div>
        <a href="{{ route('alerts.index') }}" class="kpi-link" style="color:#d97706;">Restock <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i></a>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fef2f2;color:#dc2626;"><i class="fas fa-ban"></i></div>
        <div class="kpi-value" style="color:{{ $outOfStockItems > 0 ? '#dc2626' : '#0f172a' }};">{{ number_format($outOfStockItems) }}</div>
        <div class="kpi-label">Out of Stock</div>
        <a href="{{ route('alerts.index') }}" class="kpi-link" style="color:#dc2626;">View <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i></a>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#f0fdf4;color:#16a34a;"><i class="fas fa-file-invoice"></i></div>
        <div class="kpi-value">{{ number_format($pendingReceipts) }}</div>
        <div class="kpi-label">Pending Receipts</div>
        <a href="{{ route('receipts.index') }}" class="kpi-link" style="color:#16a34a;">Verify <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i></a>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fef2f2;color:#dc2626;"><i class="fas fa-truck"></i></div>
        <div class="kpi-value">{{ number_format($pendingDeliveries) }}</div>
        <div class="kpi-label">Pending Deliveries</div>
        <a href="{{ route('deliveries.index') }}" class="kpi-link" style="color:#dc2626;">Track <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i></a>
    </div>

</div>

{{-- ── KPI Row 2 ────────────────────────────────────────────────────────────── --}}
<div class="kpi-grid" style="grid-template-columns:repeat(5,1fr);margin-bottom:18px;">

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="fas fa-random"></i></div>
        <div class="kpi-value">{{ number_format($pendingTransfers) }}</div>
        <div class="kpi-label">Pending Transfers</div>
        <a href="{{ route('transfers.index') }}" class="kpi-link" style="color:#7c3aed;">Manage <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i></a>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#f0f9ff;color:#0891b2;"><i class="fas fa-warehouse"></i></div>
        <div class="kpi-value">{{ number_format($totalWarehouses) }}</div>
        <div class="kpi-label">Active Warehouses</div>
        <a href="{{ route('warehouses.index') }}" class="kpi-link" style="color:#0891b2;">View <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i></a>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fdf4ff;color:#a21caf;"><i class="fas fa-tags"></i></div>
        <div class="kpi-value">{{ number_format($totalCategories) }}</div>
        <div class="kpi-label">Categories</div>
        <a href="{{ route('categories.index') }}" class="kpi-link" style="color:#a21caf;">View <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i></a>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#f0fdf4;color:#15803d;"><i class="fas fa-cubes"></i></div>
        <div class="kpi-value">{{ number_format($totalStockQuantity) }}</div>
        <div class="kpi-label">Total Units in Stock</div>
        <a href="{{ route('inventory.index') }}" class="kpi-link" style="color:#15803d;">View <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i></a>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fff7ed;color:#ea580c;"><i class="fas fa-sliders-h"></i></div>
        <div class="kpi-value">{{ number_format($totalAdjustments) }}</div>
        <div class="kpi-label">Total Adjustments</div>
        <a href="{{ route('adjustments.index') }}" class="kpi-link" style="color:#ea580c;">View <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i></a>
    </div>

</div>

{{-- ── Status Breakdown Row ─────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px;">

    {{-- Receipts by Status --}}
    <div class="card">
        <div class="card-header">
            <h3 style="display:flex;align-items:center;gap:7px;"><i class="fas fa-file-invoice" style="color:#16a34a;"></i> Receipts by Status</h3>
            <a href="{{ route('receipts.index') }}" style="font-size:0.72rem;font-weight:600;color:#4f46e5;text-decoration:none;">View all</a>
        </div>
        <div style="padding:12px 16px;display:flex;flex-direction:column;gap:8px;">
            @php $rStatuses = ['pending'=>['#d97706','Pending'],'verified'=>['#16a34a','Verified'],'cancelled'=>['#dc2626','Cancelled']]; @endphp
            @foreach($rStatuses as $key => [$color, $label])
            @php $cnt = $receiptStatusCounts[$key] ?? 0; $total = $receiptStatusCounts->sum() ?: 1; @endphp
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:3px;">
                    <span style="font-size:0.75rem;font-weight:600;color:#475569;">{{ $label }}</span>
                    <span style="font-size:0.75rem;font-weight:700;color:{{ $color }};">{{ $cnt }}</span>
                </div>
                <div style="height:5px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ round($cnt/$total*100) }}%;background:{{ $color }};border-radius:99px;transition:width 0.4s;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Deliveries by Status --}}
    <div class="card">
        <div class="card-header">
            <h3 style="display:flex;align-items:center;gap:7px;"><i class="fas fa-truck" style="color:#dc2626;"></i> Deliveries by Status</h3>
            <a href="{{ route('deliveries.index') }}" style="font-size:0.72rem;font-weight:600;color:#4f46e5;text-decoration:none;">View all</a>
        </div>
        <div style="padding:12px 16px;display:flex;flex-direction:column;gap:8px;">
            @php $dStatuses = ['draft'=>['#94a3b8','Draft'],'picking'=>['#0891b2','Picking'],'packed'=>['#4f46e5','Packed'],'validated'=>['#16a34a','Validated'],'shipped'=>['#0f172a','Shipped'],'cancelled'=>['#dc2626','Cancelled']]; @endphp
            @foreach($dStatuses as $key => [$color, $label])
            @php $cnt = $deliveryStatusCounts[$key] ?? 0; $total = $deliveryStatusCounts->sum() ?: 1; @endphp
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:3px;">
                    <span style="font-size:0.75rem;font-weight:600;color:#475569;">{{ $label }}</span>
                    <span style="font-size:0.75rem;font-weight:700;color:{{ $color }};">{{ $cnt }}</span>
                </div>
                <div style="height:5px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ round($cnt/$total*100) }}%;background:{{ $color }};border-radius:99px;transition:width 0.4s;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Transfers by Status --}}
    <div class="card">
        <div class="card-header">
            <h3 style="display:flex;align-items:center;gap:7px;"><i class="fas fa-random" style="color:#7c3aed;"></i> Transfers by Status</h3>
            <a href="{{ route('transfers.index') }}" style="font-size:0.72rem;font-weight:600;color:#4f46e5;text-decoration:none;">View all</a>
        </div>
        <div style="padding:12px 16px;display:flex;flex-direction:column;gap:8px;">
            @php $tStatuses = ['pending'=>['#d97706','Pending'],'in_transit'=>['#0891b2','In Transit'],'completed'=>['#16a34a','Completed'],'cancelled'=>['#dc2626','Cancelled']]; @endphp
            @foreach($tStatuses as $key => [$color, $label])
            @php $cnt = $transferStatusCounts[$key] ?? 0; $total = $transferStatusCounts->sum() ?: 1; @endphp
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:3px;">
                    <span style="font-size:0.75rem;font-weight:600;color:#475569;">{{ $label }}</span>
                    <span style="font-size:0.75rem;font-weight:700;color:{{ $color }};">{{ $cnt }}</span>
                </div>
                <div style="height:5px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ round($cnt/$total*100) }}%;background:{{ $color }};border-radius:99px;transition:width 0.4s;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ── Main Grid: Critical Stock + Recent Movements ────────────────────────── --}}
<div class="section-grid" style="margin-bottom:16px;">

    {{-- Critical Stock --}}
    <div class="card">
        <div class="card-header">
            <h3 style="display:flex;align-items:center;gap:7px;"><i class="fas fa-exclamation-triangle" style="color:#d97706;"></i> Critical Stock Levels</h3>
            <a href="{{ route('alerts.index') }}" style="font-size:0.72rem;font-weight:600;color:#4f46e5;text-decoration:none;">View all</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Warehouse</th>
                        <th style="text-align:right;">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockProducts as $stock)
                    <tr>
                        <td>
                            <div style="font-weight:600;color:#0f172a;font-size:0.82rem;">{{ $stock->product->name }}</div>
                            <div style="font-size:0.68rem;color:#94a3b8;font-family:monospace;">{{ $stock->product->sku }}</div>
                        </td>
                        <td>
                            @if($stock->product->category)
                                <span class="badge badge-blue" style="font-size:0.65rem;">{{ $stock->product->category->name }}</span>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                        <td style="font-size:0.8rem;color:#475569;">{{ $stock->warehouse->name }}</td>
                        <td style="text-align:right;">
                            @if($stock->quantity <= 0)
                                <span class="badge badge-red">Out</span>
                            @else
                                <span class="badge badge-yellow">{{ $stock->quantity }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:28px;color:#94a3b8;font-size:0.8rem;">
                            <i class="fas fa-check-circle" style="color:#22c55e;display:block;font-size:1.4rem;margin-bottom:6px;"></i>
                            All stock levels are optimal.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Movements --}}
    <div class="card">
        <div class="card-header">
            <h3 style="display:flex;align-items:center;gap:7px;"><i class="fas fa-history" style="color:#4f46e5;"></i> Recent Movements
                @if($filterDocType)
                    <span style="font-size:0.65rem;background:#eff6ff;color:#2563eb;padding:2px 7px;border-radius:99px;font-weight:700;text-transform:capitalize;">{{ $filterDocType }}</span>
                @endif
            </h3>
            <a href="{{ route('stock-movements.index') }}" style="font-size:0.72rem;font-weight:600;color:#4f46e5;text-decoration:none;">View all</a>
        </div>
        <div style="padding:0;">
            @forelse($recentMovements as $movement)
            <div style="display:flex;align-items:flex-start;gap:12px;padding:11px 18px;border-bottom:1px solid #f1f5f9;">
                <div style="width:32px;height:32px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    @if($movement->reference_type === 'Receipt')
                        <i class="fas fa-arrow-down" style="color:#16a34a;font-size:0.75rem;"></i>
                    @elseif($movement->reference_type === 'Delivery')
                        <i class="fas fa-arrow-up" style="color:#dc2626;font-size:0.75rem;"></i>
                    @elseif($movement->reference_type === 'Transfer')
                        <i class="fas fa-random" style="color:#7c3aed;font-size:0.75rem;"></i>
                    @else
                        <i class="fas fa-sliders-h" style="color:#d97706;font-size:0.75rem;"></i>
                    @endif
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                        <span style="font-size:0.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;">{{ $movement->reference_type }}</span>
                        <span style="font-size:0.65rem;color:#cbd5e1;">{{ $movement->created_at->diffForHumans() }}</span>
                    </div>
                    <a href="{{ route('stock-movements.show', $movement) }}" style="font-size:0.8rem;font-weight:600;color:#0f172a;text-decoration:none;display:block;margin-top:1px;">
                        {{ $movement->reference_type }} #{{ $movement->reference_id ?? 'N/A' }}
                    </a>
                    <div style="font-size:0.7rem;color:#94a3b8;margin-top:2px;">
                        <i class="fas fa-layer-group" style="font-size:0.6rem;"></i> {{ $movement->items->count() }} line items
                        @if($movement->user) · <i class="fas fa-user" style="font-size:0.6rem;"></i> {{ $movement->user->name }}@endif
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:32px;color:#94a3b8;font-size:0.8rem;">
                <i class="fas fa-inbox" style="font-size:1.4rem;display:block;margin-bottom:6px;opacity:0.4;"></i>
                No recent movements.
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── Recent Receipts + Deliveries + Transfers ────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:16px;">

    {{-- Recent Receipts --}}
    <div class="card">
        <div class="card-header">
            <h3 style="display:flex;align-items:center;gap:7px;"><i class="fas fa-file-invoice" style="color:#16a34a;"></i> Recent Receipts</h3>
            <a href="{{ route('receipts.index') }}" style="font-size:0.72rem;font-weight:600;color:#4f46e5;text-decoration:none;">View all</a>
        </div>
        <div style="padding:0;">
            @forelse($recentReceipts as $receipt)
            <div style="padding:10px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <div style="min-width:0;">
                    <a href="{{ route('receipts.show', $receipt) }}" style="font-size:0.8rem;font-weight:600;color:#0f172a;text-decoration:none;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $receipt->receipt_number }}</a>
                    <div style="font-size:0.7rem;color:#94a3b8;margin-top:1px;">{{ $receipt->supplier->name ?? '—' }}</div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    @php $sc = match($receipt->status){ 'verified'=>'badge-green','cancelled'=>'badge-red',default=>'badge-yellow' }; @endphp
                    <span class="badge {{ $sc }}" style="font-size:0.65rem;">{{ ucfirst($receipt->status) }}</span>
                    <div style="font-size:0.65rem;color:#cbd5e1;margin-top:2px;">{{ $receipt->receipt_date?->format('d M') }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:24px;color:#94a3b8;font-size:0.78rem;">No receipts found.</div>
            @endforelse
        </div>
    </div>

    {{-- Recent Deliveries --}}
    <div class="card">
        <div class="card-header">
            <h3 style="display:flex;align-items:center;gap:7px;"><i class="fas fa-truck" style="color:#dc2626;"></i> Recent Deliveries</h3>
            <a href="{{ route('deliveries.index') }}" style="font-size:0.72rem;font-weight:600;color:#4f46e5;text-decoration:none;">View all</a>
        </div>
        <div style="padding:0;">
            @forelse($recentDeliveries as $delivery)
            <div style="padding:10px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <div style="min-width:0;">
                    <a href="{{ route('deliveries.show', $delivery) }}" style="font-size:0.8rem;font-weight:600;color:#0f172a;text-decoration:none;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $delivery->delivery_number }}</a>
                    <div style="font-size:0.7rem;color:#94a3b8;margin-top:1px;">{{ $delivery->customer_name ?? '—' }}</div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    @php $dc = match($delivery->status){ 'shipped'=>'badge-dark','validated'=>'badge-green','cancelled'=>'badge-red','packed'=>'badge-blue',default=>'badge-yellow' }; @endphp
                    <span class="badge {{ $dc }}" style="font-size:0.65rem;">{{ ucfirst($delivery->status) }}</span>
                    <div style="font-size:0.65rem;color:#cbd5e1;margin-top:2px;">{{ $delivery->delivery_date?->format('d M') }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:24px;color:#94a3b8;font-size:0.78rem;">No deliveries found.</div>
            @endforelse
        </div>
    </div>

    {{-- Recent Transfers --}}
    <div class="card">
        <div class="card-header">
            <h3 style="display:flex;align-items:center;gap:7px;"><i class="fas fa-random" style="color:#7c3aed;"></i> Recent Transfers</h3>
            <a href="{{ route('transfers.index') }}" style="font-size:0.72rem;font-weight:600;color:#4f46e5;text-decoration:none;">View all</a>
        </div>
        <div style="padding:0;">
            @forelse($recentTransfers as $transfer)
            <div style="padding:10px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <div style="min-width:0;">
                    <a href="{{ route('transfers.show', $transfer) }}" style="font-size:0.8rem;font-weight:600;color:#0f172a;text-decoration:none;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $transfer->transfer_number }}</a>
                    <div style="font-size:0.7rem;color:#94a3b8;margin-top:1px;">{{ $transfer->fromWarehouse->name ?? '—' }} → {{ $transfer->toWarehouse->name ?? '—' }}</div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    @php $tc = match($transfer->status){ 'completed'=>'badge-green','cancelled'=>'badge-red','in_transit'=>'badge-blue',default=>'badge-yellow' }; @endphp
                    <span class="badge {{ $tc }}" style="font-size:0.65rem;">{{ ucfirst(str_replace('_',' ',$transfer->status)) }}</span>
                    <div style="font-size:0.65rem;color:#cbd5e1;margin-top:2px;">{{ $transfer->transfer_date?->format('d M') }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:24px;color:#94a3b8;font-size:0.78rem;">No transfers found.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── Movement Type Breakdown (30 days) ───────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">

    <div class="card">
        <div class="card-header">
            <h3 style="display:flex;align-items:center;gap:7px;"><i class="fas fa-chart-bar" style="color:#4f46e5;"></i> Activity (Last 30 Days)</h3>
        </div>
        <div style="padding:16px;display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            @php
                $actTypes = [
                    'receipt'    => ['#16a34a','fas fa-arrow-down','Receipts'],
                    'delivery'   => ['#dc2626','fas fa-arrow-up','Deliveries'],
                    'transfer'   => ['#7c3aed','fas fa-random','Transfers'],
                    'adjustment' => ['#d97706','fas fa-sliders-h','Adjustments'],
                ];
                $actTotal = $movementBreakdown->sum() ?: 1;
            @endphp
            @foreach($actTypes as $key => [$color, $icon, $label])
            @php $cnt = $movementBreakdown[$key] ?? 0; @endphp
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:8px;background:{{ $color }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="{{ $icon }}" style="color:{{ $color }};font-size:0.85rem;"></i>
                </div>
                <div>
                    <div style="font-size:1.2rem;font-weight:800;color:#0f172a;line-height:1;">{{ $cnt }}</div>
                    <div style="font-size:0.65rem;font-weight:600;color:#94a3b8;margin-top:1px;">{{ $label }}</div>
                </div>
                <div style="margin-left:auto;font-size:0.7rem;font-weight:700;color:{{ $color }};">{{ round($cnt/$actTotal*100) }}%</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Stock by Category --}}
    <div class="card">
        <div class="card-header">
            <h3 style="display:flex;align-items:center;gap:7px;"><i class="fas fa-tags" style="color:#a21caf;"></i> Stock by Category</h3>
            <a href="{{ route('categories.index') }}" style="font-size:0.72rem;font-weight:600;color:#4f46e5;text-decoration:none;">Manage</a>
        </div>
        <div style="padding:12px 16px;display:flex;flex-direction:column;gap:8px;">
            @php $catTotal = $stockByCategory->sum('quantity') ?: 1; @endphp
            @forelse($stockByCategory->take(6) as $cat)
            @php $pct = round($cat['quantity']/$catTotal*100); @endphp
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:3px;">
                    <span style="font-size:0.75rem;font-weight:600;color:#475569;">{{ $cat['name'] }}</span>
                    <span style="font-size:0.72rem;color:#94a3b8;">{{ number_format($cat['quantity']) }} units · {{ $cat['products'] }} SKUs</span>
                </div>
                <div style="height:5px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:#a21caf;border-radius:99px;opacity:0.7;transition:width 0.4s;"></div>
                </div>
            </div>
            @empty
            <div style="color:#94a3b8;font-size:0.8rem;padding:8px 0;">No category data available.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── Warehouse Stock Summary ───────────────────────────────────────────────── --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-header">
        <h3 style="display:flex;align-items:center;gap:7px;"><i class="fas fa-warehouse" style="color:#0891b2;"></i> Warehouse Stock Summary</h3>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;">
            @forelse($stockByWarehouse as $warehouse)
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;">
                <div style="font-size:0.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:10px;">
                    <i class="fas fa-warehouse" style="margin-right:4px;"></i>{{ $warehouse['name'] }}
                </div>
                <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:8px;">
                    <div>
                        <div style="font-size:1.5rem;font-weight:800;color:#0f172a;line-height:1;">{{ number_format($warehouse['total_quantity']) }}</div>
                        <div style="font-size:0.65rem;font-weight:600;color:#94a3b8;margin-top:2px;">Total Units</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:1rem;font-weight:700;color:#4f46e5;">{{ $warehouse['count'] }}</div>
                        <div style="font-size:0.65rem;font-weight:600;color:#94a3b8;">SKUs</div>
                    </div>
                </div>
                @if($warehouse['low_stock'] > 0)
                <div style="display:flex;align-items:center;gap:5px;background:#fffbeb;border:1px solid #fde68a;border-radius:5px;padding:4px 8px;">
                    <i class="fas fa-exclamation-triangle" style="color:#d97706;font-size:0.65rem;"></i>
                    <span style="font-size:0.68rem;font-weight:600;color:#d97706;">{{ $warehouse['low_stock'] }} low stock</span>
                </div>
                @else
                <div style="display:flex;align-items:center;gap:5px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:5px;padding:4px 8px;">
                    <i class="fas fa-check-circle" style="color:#16a34a;font-size:0.65rem;"></i>
                    <span style="font-size:0.68rem;font-weight:600;color:#16a34a;">Stock healthy</span>
                </div>
                @endif
            </div>
            @empty
            <div style="color:#94a3b8;font-size:0.8rem;padding:12px;">No warehouse data available.</div>
            @endforelse
        </div>
    </div>
</div>

@endsection
