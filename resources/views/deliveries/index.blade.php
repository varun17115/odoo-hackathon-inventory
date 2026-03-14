@extends('layouts.app')

@section('page-title', 'Outbound Deliveries')

@section('content')
    {{-- ── Action Bar ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Deliveries</h2>
            <p class="text-sm font-medium text-gray-500 mt-1">Manage customer shipments, picking lists, and logistics status.</p>
        </div>
        <a href="{{ route('deliveries.create') }}">
            <x-button class="shadow-blue-500/25 py-2.5">
                <i class="fas fa-plus mr-2 text-[10px]"></i>
                Create Delivery
            </x-button>
        </a>
    </div>

    {{-- ── Filters ── --}}
    <x-card class="mb-8 overflow-visible">
        <form method="GET" action="{{ route('deliveries.index') }}" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-6">
            <x-input label="Search" name="search" :value="request('search')" placeholder="Order # or Customer..." />
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                <select name="status" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                    <option value="">All Statuses</option>
                    @foreach(['draft', 'picking', 'packed', 'validated', 'shipped', 'cancelled'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Origin Warehouse</label>
                <select name="warehouse_id" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3 pb-4">
                <x-button class="flex-1 justify-center py-2.5">
                    <i class="fas fa-filter mr-2 text-[10px]"></i> Apply
                </x-button>
                @if(request()->hasAny(['search', 'status', 'warehouse_id']))
                    <a href="{{ route('deliveries.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors text-sm font-bold">
                        <i class="fas fa-undo"></i>
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    {{-- ── Table ── --}}
    <x-table 
        :headers="['Delivery #', 'Customer Details', 'Source Site', 'Value / Items', 'Current Status', 'Actions']"
        :empty="$deliveries->isEmpty()"
        emptyMessage="No deliveries found."
    >
        @foreach($deliveries as $delivery)
            <tr class="group hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4 text-sm font-black text-gray-900">{{ $delivery->delivery_number }}</td>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-700 leading-tight">{{ $delivery->customer_name }}</div>
                    <div class="text-[10px] font-medium text-gray-400 mt-0.5 tracking-tighter">{{ $delivery->customer_phone ?? 'No contact provided' }}</div>
                </td>
                <td class="px-6 py-4 text-sm font-bold text-gray-700">{{ $delivery->warehouse->name }}</td>
                <td class="px-6 py-4">
                    <div class="text-sm font-black text-gray-900">${{ number_format($delivery->total_amount, 2) }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $delivery->items->count() }} line items</div>
                </td>
                <td class="px-6 py-4 text-center">
                    @php
                        $color = match($delivery->status) {
                            'draft' => 'bg-gray-100 text-gray-600',
                            'picking' => 'bg-indigo-50 text-indigo-600',
                            'packed' => 'bg-blue-50 text-blue-600',
                            'validated' => 'bg-emerald-50 text-emerald-600',
                            'shipped' => 'bg-purple-50 text-purple-600',
                            'cancelled' => 'bg-rose-50 text-rose-600',
                            default => 'bg-gray-50 text-gray-400'
                        };
                    @endphp
                    <span class="px-2.5 py-1 {{ $color }} text-[10px] font-black rounded-lg uppercase tracking-wider">
                        {{ $delivery->status }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('deliveries.show', $delivery) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="View Progress">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        @if($delivery->status === 'draft')
                            <form method="POST" action="{{ route('deliveries.destroy', $delivery) }}" onsubmit="return confirmDelete(this)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 transition-colors" title="Cancel Delivery">
                                    <i class="fas fa-ban text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-8">
        {{ $deliveries->links() }}
    </div>
@endsection
