@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Market Dashboard')

@section('content')

{{-- KPI Row --}}
<div class="kpi-grid" style="grid-template-columns: repeat(5, 1fr);">

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eff6ff; color:#2563eb;">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="kpi-value">{{ number_format($totalProducts) }}</div>
        <div class="kpi-label">Total Products</div>
        <a href="{{ route('products.index') }}" class="kpi-link" style="color:#2563eb;">
            Manage <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i>
        </a>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fffbeb; color:#d97706;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="kpi-value" style="color:{{ $lowStockItems > 0 ? '#d97706' : '#0f172a' }};">{{ number_format($lowStockItems) }}</div>
        <div class="kpi-label">Low Stock Alerts</div>
        <a href="{{ route('alerts.index') }}" class="kpi-link" style="color:#d97706;">
            Restock <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i>
        </a>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#f0fdf4; color:#16a34a;">
            <i class="fas fa-file-invoice"></i>
        </div>
        <div class="kpi-value">{{ number_format($pendingReceipts) }}</div>
        <div class="kpi-label">Inbound Pending</div>
        <a href="{{ route('receipts.index') }}" class="kpi-link" style="color:#16a34a;">
            Verify <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i>
        </a>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fef2f2; color:#dc2626;">
            <i class="fas fa-truck"></i>
        </div>
        <div class="kpi-value">{{ number_format($pendingDeliveries) }}</div>
        <div class="kpi-label">Outbound Pending</div>
        <a href="{{ route('deliveries.index') }}" class="kpi-link" style="color:#dc2626;">
            Track <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i>
        </a>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#f5f3ff; color:#7c3aed;">
            <i class="fas fa-random"></i>
        </div>
        <div class="kpi-value">{{ number_format($pendingTransfers) }}</div>
        <div class="kpi-label">Pending Transfers</div>
        <a href="{{ route('transfers.index') }}" class="kpi-link" style="color:#7c3aed;">
            Manage <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i>
        </a>
    </div>

</div>

{{-- Main Grid --}}
<div class="section-grid" style="margin-bottom:16px;">

    {{-- Critical Stock --}}
    <div class="card">
        <div class="card-header">
            <h3 style="display:flex;align-items:center;gap:7px;">
                <i class="fas fa-exclamation-triangle" style="color:#d97706;"></i> Critical Stock Levels
            </h3>
            <a href="{{ route('alerts.index') }}" style="font-size:0.72rem;font-weight:600;color:#4f46e5;text-decoration:none;">View all</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Warehouse</th>
                        <th>Location</th>
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
                        <td style="font-size:0.8rem;color:#475569;">{{ $stock->warehouse->name }}</td>
                        <td>
                            @if($stock->location)
                                <span class="badge badge-blue">{{ $stock->location->name }}</span>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <span class="badge badge-yellow">{{ $stock->quantity }}</span>
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
            <h3 style="display:flex;align-items:center;gap:7px;">
                <i class="fas fa-history" style="color:#4f46e5;"></i> Recent Movements
            </h3>
            <a href="{{ route('stock-movements.index') }}" style="font-size:0.72rem;font-weight:600;color:#4f46e5;text-decoration:none;">View all</a>
        </div>
        <div style="padding:0;">
            @forelse($recentMovements as $movement)
            <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 18px;border-bottom:1px solid #f1f5f9;">
                <div style="width:34px;height:34px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    @if($movement->reference_type === 'Receipt')
                        <i class="fas fa-arrow-down" style="color:#16a34a;font-size:0.8rem;"></i>
                    @elseif($movement->reference_type === 'Delivery')
                        <i class="fas fa-arrow-up" style="color:#dc2626;font-size:0.8rem;"></i>
                    @elseif($movement->reference_type === 'Transfer')
                        <i class="fas fa-random" style="color:#7c3aed;font-size:0.8rem;"></i>
                    @else
                        <i class="fas fa-sliders-h" style="color:#d97706;font-size:0.8rem;"></i>
                    @endif
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                        <span style="font-size:0.68rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;">{{ $movement->reference_type }}</span>
                        <span style="font-size:0.68rem;color:#cbd5e1;">{{ $movement->created_at->diffForHumans() }}</span>
                    </div>
                    <a href="{{ route('stock-movements.show', $movement) }}" style="font-size:0.82rem;font-weight:600;color:#0f172a;text-decoration:none;display:block;margin-top:1px;">
                        {{ $movement->reference_type }} #{{ $movement->reference_id ?? 'N/A' }}
                    </a>
                    <div style="font-size:0.72rem;color:#94a3b8;margin-top:2px;">
                        <i class="fas fa-layer-group" style="font-size:0.6rem;"></i> {{ $movement->items->count() }} line items
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

{{-- Warehouse Breakdown --}}
<div class="card">
    <div class="card-header">
        <h3 style="display:flex;align-items:center;gap:7px;">
            <i class="fas fa-warehouse" style="color:#0891b2;"></i> Warehouse Stock Summary
        </h3>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
            @forelse($stockByWarehouse as $warehouse)
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;">
                <div style="font-size:0.7rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px;">
                    <i class="fas fa-warehouse" style="margin-right:4px;"></i>{{ $warehouse['name'] }}
                </div>
                <div style="display:flex;align-items:flex-end;justify-content:space-between;">
                    <div>
                        <div style="font-size:1.5rem;font-weight:800;color:#0f172a;line-height:1;">{{ number_format($warehouse['total_quantity']) }}</div>
                        <div style="font-size:0.65rem;font-weight:600;color:#94a3b8;margin-top:2px;">Total Units</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:1rem;font-weight:700;color:#4f46e5;">{{ $warehouse['count'] }}</div>
                        <div style="font-size:0.65rem;font-weight:600;color:#94a3b8;">SKUs</div>
                    </div>
                </div>
            </div>
            @empty
            <div style="color:#94a3b8;font-size:0.8rem;padding:12px;">No warehouse data available.</div>
            @endforelse
        </div>
    </div>
</div>

@endsection
