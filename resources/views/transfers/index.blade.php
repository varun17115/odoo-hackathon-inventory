@extends('layouts.app')

@section('page-title', 'Inventory Transfers')

@section('content')
    {{-- ── Action Bar ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Transfers</h2>
            <p class="text-sm font-medium text-gray-500 mt-1">Manage stock movements between your internal warehouse network.</p>
        </div>
        <a href="{{ route('transfers.create') }}">
            <x-button class="shadow-blue-500/25 py-2.5">
                <i class="fas fa-plus mr-2 text-[10px]"></i>
                New Transfer
            </x-button>
        </a>
    </div>

    {{-- ── Filters ── --}}
    <x-card class="mb-8 overflow-visible">
        <form method="GET" action="{{ route('transfers.index') }}" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-6">
            <x-input label="Transfer Number" name="search" :value="request('search')" placeholder="TRN-00000" />
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                <select name="status" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                    <option value="">All Statuses</option>
                    @foreach(['pending', 'completed', 'cancelled'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Source Site</label>
                <select name="from_warehouse_id" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ request('from_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Destination Site</label>
                <select name="to_warehouse_id" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ request('to_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3 pb-4">
                <x-button class="flex-1 justify-center py-2.5">
                    <i class="fas fa-filter mr-2 text-[10px]"></i> Apply
                </x-button>
                @if(request()->hasAny(['search', 'status', 'from_warehouse_id', 'to_warehouse_id']))
                    <a href="{{ route('transfers.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors text-sm font-bold">
                        <i class="fas fa-undo"></i>
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    {{-- ── Table ── --}}
    <x-table 
        :headers="['Transfer Details', 'Source / Destination', 'Items / Units', 'Transfer Date', 'Status', 'Actions']"
        :empty="$transfers->isEmpty()"
        emptyMessage="No transfers found."
    >
        @foreach($transfers as $transfer)
            <tr class="group hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4">
                    <div class="text-sm font-black text-gray-900">{{ $transfer->transfer_number }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">System ID: {{ $transfer->id }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <div class="text-xs font-bold text-gray-700">{{ $transfer->fromWarehouse->name }}</div>
                        <i class="fas fa-arrow-right text-[10px] text-gray-300"></i>
                        <div class="text-xs font-bold text-gray-700">{{ $transfer->toWarehouse->name }}</div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-black text-gray-900">{{ number_format($transfer->getTotalQuantity()) }} Units</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $transfer->items->count() }} Line Items</div>
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-600">
                    {{ $transfer->transfer_date->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 text-center">
                    @php
                        $color = match($transfer->status) {
                            'pending' => 'bg-amber-50 text-amber-600',
                            'completed' => 'bg-emerald-50 text-emerald-600',
                            'cancelled' => 'bg-rose-50 text-rose-600',
                            default => 'bg-gray-50 text-gray-400'
                        };
                    @endphp
                    <span class="px-2.5 py-1 {{ $color }} text-[10px] font-black rounded-lg uppercase tracking-wider">
                        {{ $transfer->status }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('transfers.show', $transfer) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="View Transfer">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        @if($transfer->status === 'pending')
                            <form method="POST" action="{{ route('transfers.destroy', $transfer) }}" onsubmit="return confirmDelete(this)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 transition-colors" title="Delete Draft">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-8">
        {{ $transfers->links() }}
    </div>
@endsection
