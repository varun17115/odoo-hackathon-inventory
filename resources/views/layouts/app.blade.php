<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Invento Market')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: { preflight: false },
            theme: { extend: { fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] } } }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: #f1f5f9; }
        [x-cloak] { display: none !important; }

        /* ── Sidebar ── */
        #app-sidebar {
            width: 240px; min-width: 240px; background: #0f172a;
            display: flex; flex-direction: column; height: 100vh;
            position: fixed; left: 0; top: 0; z-index: 40;
            overflow: hidden;
        }
        #app-brand {
            padding: 18px 20px 14px; border-bottom: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;
        }
        .brand-logo {
            display: flex; align-items: center; gap: 10px; text-decoration: none;
        }
        .brand-icon {
            width: 34px; height: 34px; background: #4f46e5; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 0.85rem; flex-shrink: 0;
        }
        .brand-name { font-size: 1rem; font-weight: 800; color: #fff; letter-spacing: -0.02em; }
        .brand-name span { color: #818cf8; }
        .brand-ver { font-size: 0.6rem; color: #475569; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; margin-top: 1px; }

        #app-nav { flex: 1; overflow-y: auto; padding: 6px 0 8px; }
        #app-nav::-webkit-scrollbar { width: 2px; }
        #app-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); }

        .nav-section {
            padding: 12px 16px 3px; font-size: 0.6rem; font-weight: 700;
            color: #334155; text-transform: uppercase; letter-spacing: 0.12em;
        }
        .nav-link {
            display: flex; align-items: center; gap: 9px;
            padding: 8px 16px; font-size: 0.82rem; font-weight: 500;
            color: #94a3b8; text-decoration: none;
            border-left: 2px solid transparent;
            transition: all 0.12s ease;
        }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: #cbd5e1; text-decoration: none; }
        .nav-link.active { background: rgba(79,70,229,0.12); color: #a5b4fc; border-left-color: #4f46e5; font-weight: 600; }
        .nav-link i { width: 15px; text-align: center; font-size: 0.75rem; flex-shrink: 0; opacity: 0.8; }
        .nav-badge {
            margin-left: auto; background: #ef4444; color: #fff;
            font-size: 0.6rem; font-weight: 700; padding: 1px 5px; border-radius: 999px;
        }

        #app-user {
            flex-shrink: 0; border-top: 1px solid rgba(255,255,255,0.07);
            padding: 12px 14px;
        }
        .user-row { display: flex; align-items: center; gap: 9px; }
        .user-avatar {
            width: 30px; height: 30px; background: #4f46e5; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 0.75rem; flex-shrink: 0;
        }
        .user-name { font-size: 0.78rem; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 120px; }
        .user-role { font-size: 0.6rem; color: #64748b; font-weight: 500; }
        .logout-btn {
            margin-left: auto; background: none; border: none; color: #475569;
            cursor: pointer; padding: 5px; border-radius: 6px; font-size: 0.85rem;
            transition: all 0.12s;
        }
        .logout-btn:hover { color: #ef4444; background: rgba(239,68,68,0.1); }

        /* ── Main ── */
        #app-main { margin-left: 240px; width: calc(100% - 240px); display: flex; flex-direction: column; min-height: 100vh; overflow-x: hidden; }

        /* ── Topbar ── */
        #app-topbar {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: 10px 24px; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 30;
        }
        .topbar-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0; }
        .topbar-right { display: flex; align-items: center; gap: 10px; }
        .role-pill {
            font-size: 0.65rem; font-weight: 700; padding: 2px 8px;
            border-radius: 999px; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .role-admin { background: #ede9fe; color: #4f46e5; }
        .role-staff { background: #dcfce7; color: #15803d; }
        .alert-pill {
            display: flex; align-items: center; gap: 5px;
            padding: 4px 10px; background: #fef2f2; color: #b91c1c;
            border-radius: 8px; font-size: 0.75rem; font-weight: 600;
            text-decoration: none; transition: background 0.12s;
        }
        .alert-pill:hover { background: #fee2e2; color: #991b1b; text-decoration: none; }
        .topbar-date { font-size: 0.75rem; color: #94a3b8; font-weight: 500; }

        /* ── Content ── */
        #app-content { flex: 1; padding: 24px; }

        /* ── Shared UI Components ── */
        .page-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .page-header-left h2 { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0; }
        .page-header-left p { font-size: 0.8rem; color: #64748b; margin: 3px 0 0; }

        .card { background: #fff; border-radius: 10px; border: 1px solid #e2e8f0; overflow: hidden; }
        .card-header { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
        .card-header h3 { font-size: 0.875rem; font-weight: 600; color: #0f172a; margin: 0; }
        .card-body { padding: 18px; }

        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 20px; }
        .kpi-card { background: #fff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 16px; }
        .kpi-icon { width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; margin-bottom: 10px; }
        .kpi-value { font-size: 1.6rem; font-weight: 800; color: #0f172a; line-height: 1; }
        .kpi-label { font-size: 0.7rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 3px; }
        .kpi-link { font-size: 0.72rem; font-weight: 600; margin-top: 8px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none; }
        .kpi-link:hover { text-decoration: underline; }

        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th { padding: 10px 14px; text-align: left; font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.08em; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        .data-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.1s; }
        .data-table tbody tr:last-child { border-bottom: none; }
        .data-table tbody tr:hover { background: #f8fafc; }
        .data-table td { padding: 11px 14px; font-size: 0.82rem; color: #374151; vertical-align: middle; }

        .btn-primary-sm {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; background: #4f46e5; color: #fff;
            border: none; border-radius: 7px; font-size: 0.8rem; font-weight: 600;
            cursor: pointer; text-decoration: none; transition: background 0.12s;
        }
        .btn-primary-sm:hover { background: #4338ca; color: #fff; text-decoration: none; }
        .btn-secondary-sm {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; background: #f1f5f9; color: #475569;
            border: 1px solid #e2e8f0; border-radius: 7px; font-size: 0.8rem; font-weight: 600;
            cursor: pointer; text-decoration: none; transition: all 0.12s;
        }
        .btn-secondary-sm:hover { background: #e2e8f0; color: #334155; text-decoration: none; }
        .btn-danger-sm {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; background: #fef2f2; color: #dc2626;
            border: 1px solid #fecaca; border-radius: 7px; font-size: 0.8rem; font-weight: 600;
            cursor: pointer; text-decoration: none; transition: all 0.12s;
        }
        .btn-danger-sm:hover { background: #fee2e2; color: #b91c1c; text-decoration: none; }

        .icon-btn { background: none; border: none; padding: 5px 7px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; color: #94a3b8; transition: all 0.12s; }
        .icon-btn:hover { background: #f1f5f9; }
        .icon-btn.edit:hover { color: #f59e0b; }
        .icon-btn.view:hover { color: #4f46e5; }
        .icon-btn.delete:hover { color: #ef4444; }
        .icon-btn.toggle:hover { color: #10b981; }

        .badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 999px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .badge-yellow { background: #fef9c3; color: #a16207; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-purple { background: #ede9fe; color: #6d28d9; }
        .badge-gray { background: #f1f5f9; color: #475569; }
        .badge-orange { background: #ffedd5; color: #c2410c; }
        .badge-indigo { background: #e0e7ff; color: #3730a3; }
        .badge-dark { background: #1e293b; color: #f1f5f9; }

        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 0.78rem; font-weight: 600; color: #374151; margin-bottom: 5px; }
        .form-control {
            width: 100%; padding: 8px 11px; font-size: 0.82rem; color: #0f172a;
            border: 1px solid #d1d5db; border-radius: 7px; background: #fff;
            transition: border-color 0.12s, box-shadow 0.12s; outline: none;
            font-family: 'Inter', sans-serif;
        }
        .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .form-control::placeholder { color: #9ca3af; }

        .filter-bar { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 18px; margin-bottom: 16px; }
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; align-items: end; }

        .modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.5); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 16px; }
        .modal-box { background: #fff; border-radius: 12px; width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        .modal-box-lg { max-width: 680px; }
        .modal-head { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
        .modal-head h3 { font-size: 0.95rem; font-weight: 700; color: #0f172a; margin: 0; }
        .modal-close { background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1rem; padding: 2px 6px; border-radius: 5px; }
        .modal-close:hover { color: #374151; background: #f1f5f9; }
        .modal-body { padding: 20px; }
        .modal-foot { padding: 14px 20px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: flex-end; gap: 8px; }

        .error-block { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px; margin-bottom: 16px; }
        .error-block p { font-size: 0.8rem; font-weight: 600; color: #991b1b; margin: 0 0 4px; }
        .error-block ul { margin: 0; padding-left: 16px; }
        .error-block li { font-size: 0.75rem; color: #b91c1c; }

        .empty-state { text-align: center; padding: 40px 20px; color: #94a3b8; }
        .empty-state i { font-size: 2rem; display: block; margin-bottom: 8px; opacity: 0.4; }
        .empty-state p { font-size: 0.82rem; margin: 0; }

        .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 600; color: #4f46e5; text-decoration: none; margin-bottom: 16px; }
        .back-link:hover { color: #4338ca; text-decoration: none; }

        .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; }
        .detail-item label { font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; display: block; margin-bottom: 3px; }
        .detail-item span { font-size: 0.875rem; font-weight: 600; color: #0f172a; }

        .section-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 768px) { .section-grid { grid-template-columns: 1fr; } }

        .table-wrapper { overflow-x: auto; border-radius: 10px; border: 1px solid #e2e8f0; background: #fff; }
    </style>
</head>
<body>
<div style="display:flex; min-height:100vh;">

    {{-- ── Sidebar ── --}}
    <aside id="app-sidebar">
        <div id="app-brand">
            <a href="{{ route('dashboard') }}" class="brand-logo">
                <div class="brand-icon"><i class="fas fa-cubes"></i></div>
                <div>
                    <div class="brand-name">Invento<span>Market</span></div>
                    <div class="brand-ver">IMS v1.0</div>
                </div>
            </a>
        </div>

        <nav id="app-nav">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>

            <div class="nav-section">Inventory</div>
            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="fas fa-box"></i> Products
            </a>
            <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                <i class="fas fa-cubes"></i> Inventory
            </a>
            <a href="{{ route('stock-movements.index') }}" class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt"></i> Stock Movements
            </a>

            <div class="nav-section">Operations</div>
            <a href="{{ route('receipts.index') }}" class="nav-link {{ request()->routeIs('receipts.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i> Receipts
            </a>
            <a href="{{ route('deliveries.index') }}" class="nav-link {{ request()->routeIs('deliveries.*') ? 'active' : '' }}">
                <i class="fas fa-truck"></i> Deliveries
            </a>
            <a href="{{ route('transfers.index') }}" class="nav-link {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
                <i class="fas fa-random"></i> Transfers
            </a>
            @php $alertCount = \App\Models\Stock::where('quantity', '<', 10)->count(); @endphp
            <a href="{{ route('alerts.index') }}" class="nav-link {{ request()->routeIs('alerts.*') ? 'active' : '' }}">
                <i class="fas fa-bell"></i>
                <span style="flex:1;">Alerts</span>
                @if($alertCount > 0)
                    <span class="nav-badge">{{ $alertCount }}</span>
                @endif
            </a>

            @if(auth()->user()->role === 'admin')
            <div class="nav-section">Management</div>
            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="fas fa-tag"></i> Categories
            </a>
            <a href="{{ route('warehouses.index') }}" class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
                <i class="fas fa-warehouse"></i> Warehouses
            </a>
            <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <i class="fas fa-truck-loading"></i> Suppliers
            </a>
            <a href="{{ route('reorder-rules.index') }}" class="nav-link {{ request()->routeIs('reorder-rules.*') ? 'active' : '' }}">
                <i class="fas fa-sync-alt"></i> Reorder Rules
            </a>
            <a href="{{ route('adjustments.index') }}" class="nav-link {{ request()->routeIs('adjustments.*') ? 'active' : '' }}">
                <i class="fas fa-sliders-h"></i> Adjustments
            </a>

            <div class="nav-section">System</div>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> Users
            </a>
            @endif
        </nav>

        <div id="app-user">
            <div class="user-row">
                <a href="{{ route('profile.show') }}" style="display:flex;align-items:center;gap:9px;text-decoration:none;flex:1;min-width:0;">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div style="min-width:0;">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Staff' }}</div>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn" title="Logout"><i class="fas fa-sign-out-alt"></i></button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── Main ── --}}
    <div id="app-main">
        <div id="app-topbar">
            <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
            <div class="topbar-right">
                @if(auth()->user()->role === 'admin')
                    <span class="role-pill role-admin">Admin</span>
                @else
                    <span class="role-pill role-staff">Staff</span>
                @endif
                @if(isset($alertCount) && $alertCount > 0 && !request()->routeIs('alerts.*'))
                    <a href="{{ route('alerts.index') }}" class="alert-pill">
                        <i class="fas fa-bell"></i> {{ $alertCount }} alert{{ $alertCount > 1 ? 's' : '' }}
                    </a>
                @endif
                <span class="topbar-date">{{ now()->format('M j, Y') }}</span>
            </div>
        </div>

        <div id="app-content">
            @if($errors->any())
            <div class="error-block">
                <p><i class="fas fa-circle-exclamation mr-1"></i> Please fix the following errors:</p>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
@if(session('success'))
Swal.fire({ icon:'success', title:'Success', text:@json(session('success')), timer:2200, showConfirmButton:false, timerProgressBar:true });
@endif
@if(session('error'))
Swal.fire({ icon:'error', title:'Error', text:@json(session('error')) });
@endif
function confirmDelete(form) {
    Swal.fire({
        title:'Delete this record?', text:'This cannot be undone.', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#ef4444', cancelButtonColor:'#64748b',
        confirmButtonText:'Yes, delete'
    }).then(r => { if(r.isConfirmed) form.submit(); });
    return false;
}
</script>
@stack('scripts')
</body>
</html>
