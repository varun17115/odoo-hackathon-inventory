@extends('layouts.app')
@section('title', 'Stock Alerts')
@section('page-title', 'Stock Alerts')

@section('content')
<style>
    .al-grid4 { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
    .al-grid2 { display:grid; grid-template-columns:repeat(2,1fr); gap:20px; }
    .al-grid-wh { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
    @media(max-width:900px){ .al-grid4{grid-template-columns:repeat(2,1fr);} .al-grid2{grid-template-columns:1fr;} .al-grid-wh{grid-template-columns:repeat(2,1fr);} }
    @media(max-width:500px){ .al-grid4{grid-template-columns:repeat(2,1fr);} }

    .al-kpi { background:#fff; border-radius:12px; border:1px solid #e5e7eb; padding:16px; display:flex; align-items:center; gap:14px; }
    .al-kpi-icon { width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.2rem; }
    .al-kpi-label { font-size:0.72rem; color:#6b7280; font-weight:500; margin:0 0 2px; }
    .al-kpi-value { font-size:1.8rem; font-weight:800; margin:0; line-height:1; }

    .al-card { background:#fff; border-radius:12px; border:1px solid #e5e7eb; overflow:hidden; }
    .al-card-head { display:flex; align-items:center; justify-content:space-between; padding:12px 18px; border-bottom:1px solid #f3f4f6; }
    .al-card-head-left { display:flex; align-items:center; gap:8px; }
    .al-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
    .al-card-title { font-size:0.85rem; font-weight:700; margin:0; }
    .al-badge { font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:999px; }
    .al-card-action { font-size:0.75rem; font-weight:600; text-decoration:none; }
    .al-card-action:hover { text-decoration:underline; }

    .al-list { max-height:280px; overflow-y:auto; }
    .al-list::-webkit-scrollbar { width:3px; }
    .al-list::-webkit-scrollbar-thumb { background:#e5e7eb; border-radius:2px; }
    .al-row { display:flex; align-items:center; justify-content:space-between; padding:10px 18px; border-bottom:1px solid #f9fafb; gap:12px; }
    .al-row:last-child { border-bottom:none; }
    .al-row:hover { background:#f9fafb; }
    .al-row-info { min-width:0; flex:1; }
    .al-row-name { font-size:0.85rem; font-weight:600; color:#111827; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .al-row-sub { font-size:0.72rem; color:#9ca3af; margin:2px 0 0; }
    .al-row-right { display:flex; align-items:center; gap:10px; flex-shrink:0; }
    .al-qty { font-size:0.75rem; font-weight:700; padding:2px 8px; border-radius:6px; }
    .al-link { font-size:0.75rem; font-weight:600; text-decoration:none; color:#3b82f6; }
    .al-link:hover { text-decoration:underline; color:#1d4ed8; }

    .al-empty { padding:40px 20px; text-align:center; }
    .al-empty i { font-size:2rem; color:#86efac; display:block; margin-bottom:8px; }
    .al-empty p { font-size:0.82rem; color:#9ca3af; margin:0; }

    .al-wh-card { border-radius:8px; border:1px solid; padding:12px; }
    .al-wh-name { font-size:0.82rem; font-weight:700; color:#111827; margin:0 0 6px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .al-wh-counts { display:flex; gap:12px; font-size:0.72rem; margin-bottom:6px; }
    .al-wh-link { font-size:0.72rem; font-weight:600; text-decoration:none; color:#3b82f6; }
    .al-wh-link:hover { text-decoration:underline; }

    .al-reorder-row { padding:10px 18px; border-bottom:1px solid #f9fafb; }
    .al-reorder-row:last-child { border-bottom:none; }
    .al-reorder-row:hover { background:#f9fafb; }
    .al-reorder-top { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
    .al-reorder-qtys { display:flex; align-items:center; gap:6px; flex-shrink:0; }
    .al-reorder-hint { font-size:0.72rem; color:#ea580c; margin:4px 0 0; }
</style>

{{-- KPI Cards --}}
<div class="al-grid4">
    <div class="al-kpi">
        <div class="al-kpi-icon" style="background:#fee2e2;">
            <i class="fas fa-times-circle" style="color:#ef4444;"></i>
        </div>
        <div>
            <p class="al-kpi-label">Out of Stock</p>
            <p class="al-kpi-value" style="color:#dc2626;">{{ $outOfStock->count() }}</p>
        </div>
    </div>
    <div class="al-kpi">
        <div class="al-kpi-icon" style="background:#fef9c3;">
            <i class="fas fa-exclamation-triangle" style="color:#ca8a04;"></i>
        </div>
        <div>
            <p class="al-kpi-label">Low Stock</p>
            <p class="al-kpi-value" style="color:#ca8a04;">{{ $lowStock->count() }}</p>
        </div>
    </div>
    <div class="al-kpi">
        <div class="al-kpi-icon" style="background:#ffedd5;">
            <i class="fas fa-redo" style="color:#ea580c;"></i>
        </div>
        <div>
            <p class="al-kpi-label">Reorder Triggered</p>
            <p class="al-kpi-value" style="color:#ea580c;">{{ $triggeredRules->count() }}</p>
        </div>
    </div>
    <div class="al-kpi">
        <div class="al-kpi-icon" style="background:#f3f4f6;">
            <i class="fas fa-box-open" style="color:#6b7280;"></i>
        </div>
        <div>
            <p class="al-kpi-label">Never Stocked</p>
            <p class="al-kpi-value" style="color:#374151;">{{ $noStockProducts->count() }}</p>
        </div>
    </div>
</div>

{{-- Warehouse Summary --}}
@if($warehouseSummary->count() > 0)
<div style="background:#fff; border-radius:12px; border:1px solid #e5e7eb; padding:18px; margin-bottom:24px;">
    <p style="font-size:0.82rem; font-weight:700; color:#374151; margin:0 0 12px; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-warehouse" style="color:#3b82f6;"></i> Alert Summary by Warehouse
    </p>
    <div class="al-grid-wh">
        @foreach($warehouseSummary as $wh)
        @php
            $whBorder = $wh->out_of_stock_count > 0 ? '#fca5a5' : ($wh->low_stock_count > 0 ? '#fde68a' : '#bbf7d0');
            $whBg     = $wh->out_of_stock_count > 0 ? '#fff5f5' : ($wh->low_stock_count > 0 ? '#fffbeb' : '#f0fdf4');
        @endphp
        <div class="al-wh-card" style="border-color:{{ $whBorder }}; background:{{ $whBg }};">
            <p class="al-wh-name">{{ $wh->name }}</p>
            <div class="al-wh-counts">
                <span style="color:#dc2626; font-weight:600;"><i class="fas fa-times-circle" style="margin-right:3px;"></i>{{ $wh->out_of_stock_count }} out</span>
                <span style="color:#ca8a04; font-weight:600;"><i class="fas fa-exclamation-triangle" style="margin-right:3px;"></i>{{ $wh->low_stock_count }} low</span>
            </div>
            <a href="{{ route('inventory.by-warehouse', $wh->id) }}" class="al-wh-link">View inventory →</a>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- 4 alert panels --}}
<div class="al-grid2">

    {{-- Out of Stock --}}
    <div class="al-card">
        <div class="al-card-head" style="background:#fff5f5;">
            <div class="al-card-head-left">
                <span class="al-dot" style="background:#ef4444;"></span>
                <span class="al-card-title" style="color:#b91c1c;">Out of Stock</span>
                <span class="al-badge" style="background:#fee2e2; color:#dc2626;">{{ $outOfStock->count() }}</span>
            </div>
            <a href="{{ route('receipts.create') }}" class="al-card-action" style="color:#dc2626;">
                <i class="fas fa-plus" style="margin-right:4px;"></i>New Receipt
            </a>
        </div>
        @if($outOfStock->count() > 0)
        <div class="al-list">
            @foreach($outOfStock as $stock)
            <div class="al-row">
                <div class="al-row-info">
                    <p class="al-row-name">{{ $stock->product->name }}</p>
                    <p class="al-row-sub">{{ $stock->product->sku }} · {{ $stock->warehouse->name }}{{ $stock->location ? ' · '.$stock->location->name : '' }}</p>
                </div>
                <div class="al-row-right">
                    <span class="al-qty" style="background:#fee2e2; color:#dc2626;">0</span>
                    <a href="{{ route('products.show', $stock->product_id) }}" class="al-link">View</a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="al-empty">
            <i class="fas fa-check-circle"></i>
            <p>No out-of-stock items</p>
        </div>
        @endif
    </div>

    {{-- Low Stock --}}
    <div class="al-card">
        <div class="al-card-head" style="background:#fffbeb;">
            <div class="al-card-head-left">
                <span class="al-dot" style="background:#eab308;"></span>
                <span class="al-card-title" style="color:#92400e;">Low Stock</span>
                <span class="al-badge" style="background:#fef9c3; color:#ca8a04;">{{ $lowStock->count() }}</span>
            </div>
            <a href="{{ route('inventory.index', ['low_stock' => 1]) }}" class="al-card-action" style="color:#92400e;">
                <i class="fas fa-external-link-alt" style="margin-right:4px;"></i>View All
            </a>
        </div>
        @if($lowStock->count() > 0)
        <div class="al-list">
            @foreach($lowStock as $stock)
            <div class="al-row">
                <div class="al-row-info">
                    <p class="al-row-name">{{ $stock->product->name }}</p>
                    <p class="al-row-sub">{{ $stock->product->sku }} · {{ $stock->warehouse->name }}</p>
                </div>
                <div class="al-row-right">
                    <span class="al-qty" style="background:#fef9c3; color:#92400e; border:1px solid #fde68a;">{{ $stock->quantity }}</span>
                    <a href="{{ route('products.show', $stock->product_id) }}" class="al-link">View</a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="al-empty">
            <i class="fas fa-check-circle"></i>
            <p>No low stock items</p>
        </div>
        @endif
    </div>

    {{-- Triggered Reorder Rules --}}
    <div class="al-card">
        <div class="al-card-head" style="background:#fff7ed;">
            <div class="al-card-head-left">
                <span class="al-dot" style="background:#f97316;"></span>
                <span class="al-card-title" style="color:#9a3412;">Reorder Triggered</span>
                <span class="al-badge" style="background:#ffedd5; color:#ea580c;">{{ $triggeredRules->count() }}</span>
            </div>
            <a href="{{ route('reorder-rules.index') }}" class="al-card-action" style="color:#9a3412;">
                <i class="fas fa-cog" style="margin-right:4px;"></i>Manage Rules
            </a>
        </div>
        @if($triggeredRules->count() > 0)
        <div class="al-list">
            @foreach($triggeredRules as $rule)
            @php
                $currentStock = $rule->warehouse_id
                    ? \App\Models\Stock::getWarehouseTotal($rule->product_id, $rule->warehouse_id)
                    : \App\Models\Stock::getProductTotal($rule->product_id);
            @endphp
            <div class="al-reorder-row">
                <div class="al-reorder-top">
                    <div class="al-row-info">
                        <p class="al-row-name">{{ $rule->product->name }}</p>
                        <p class="al-row-sub">{{ $rule->warehouse->name ?? 'All warehouses' }}{{ $rule->preferredSupplier ? ' · '.$rule->preferredSupplier->name : '' }}</p>
                    </div>
                    <div class="al-reorder-qtys">
                        <span class="al-qty" style="background:#fee2e2; color:#dc2626;">{{ number_format($currentStock) }}</span>
                        <span style="font-size:0.72rem; color:#9ca3af;">/</span>
                        <span class="al-qty" style="background:#f3f4f6; color:#374151;">min {{ number_format($rule->min_quantity) }}</span>
                    </div>
                </div>
                <p class="al-reorder-hint"><i class="fas fa-arrow-up" style="margin-right:3px;"></i>Reorder {{ number_format($rule->reorder_quantity) }} units</p>
            </div>
            @endforeach
        </div>
        @else
        <div class="al-empty">
            <i class="fas fa-check-circle"></i>
            <p>No reorder rules triggered</p>
        </div>
        @endif
    </div>

    {{-- Never Stocked --}}
    <div class="al-card">
        <div class="al-card-head" style="background:#f9fafb;">
            <div class="al-card-head-left">
                <span class="al-dot" style="background:#9ca3af;"></span>
                <span class="al-card-title" style="color:#374151;">Never Stocked</span>
                <span class="al-badge" style="background:#e5e7eb; color:#4b5563;">{{ $noStockProducts->count() }}</span>
            </div>
        </div>
        @if($noStockProducts->count() > 0)
        <div class="al-list">
            @foreach($noStockProducts as $product)
            <div class="al-row">
                <div class="al-row-info">
                    <p class="al-row-name">{{ $product->name }}</p>
                    <p class="al-row-sub">{{ $product->sku }}{{ $product->category ? ' · '.$product->category->name : '' }}</p>
                </div>
                <a href="{{ route('products.show', $product) }}" class="al-link">View</a>
            </div>
            @endforeach
        </div>
        @else
        <div class="al-empty">
            <i class="fas fa-check-circle"></i>
            <p>All products have stock records</p>
        </div>
        @endif
    </div>

</div>
@endsection
