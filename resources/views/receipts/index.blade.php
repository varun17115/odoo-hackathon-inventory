@extends('layouts.app')

@section('page-title', 'Inbound Receipts')

@section('content')
    {{-- ── Action Bar ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Receipts</h2>
            <p class="text-sm font-medium text-gray-500 mt-1">Track incoming inventory shipments and verify supplier deliveries.</p>
        </div>
        <a href="{{ route('receipts.create') }}">
            <x-button class="shadow-blue-500/25 py-2.5">
                <i class="fas fa-plus mr-2 text-[10px]"></i>
                Log New Receipt
            </x-button>
        </a>
    </div>

    {{-- ── Filters ── --}}
    <x-card class="mb-8 overflow-visible">
        <form method="GET" action="{{ route('receipts.index') }}" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-6">
            <x-input label="Receipt Number" name="search" :value="request('search')" placeholder="REC-00000" />
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                <select name="status" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Supplier</label>
                <select name="supplier_id" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Destination</label>
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
                @if(request()->hasAny(['search', 'status', 'warehouse_id', 'supplier_id']))
                    <a href="{{ route('receipts.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors text-sm font-bold">
                        <i class="fas fa-undo"></i>
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    {{-- ── Table ── --}}
    <x-table 
        :headers="['ID / Date', 'Supplier', 'Destination', 'Value', 'Status', 'Actions']"
        :empty="$receipts->isEmpty()"
        emptyMessage="No receipts match your search criteria."
    >
        @foreach($receipts as $receipt)
            <tr class="group hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4">
                    <div class="text-sm font-black text-gray-900">{{ $receipt->receipt_number }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $receipt->receipt_date->format('M d, Y') }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-700">{{ $receipt->supplier->name }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-700">{{ $receipt->warehouse->name ?? 'N/A' }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-black text-gray-900">${{ number_format($receipt->total_amount, 2) }}</div>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($receipt->status === 'pending')
                        <span class="px-2.5 py-1 bg-amber-50 text-amber-600 text-[10px] font-black rounded-lg uppercase tracking-wider">Pending Verification</span>
                    @elseif($receipt->status === 'verified')
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-lg uppercase tracking-wider">Verified</span>
                    @else
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-600 text-[10px] font-black rounded-lg uppercase tracking-wider">{{ ucfirst($receipt->status) }}</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('receipts.show', $receipt) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="View Details">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        @if($receipt->status === 'pending')
                            <form method="POST" action="{{ route('receipts.destroy', $receipt) }}" onsubmit="return confirmDelete(this)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 transition-colors" title="Delete">
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
        {{ $receipts->links() }}
    </div>
@endsection
