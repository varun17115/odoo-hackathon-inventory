@extends('layouts.app')

@section('page-title', 'Inventory Ledger & Movements')

@section('content')
    {{-- ── Action Bar ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Stock Movements</h2>
            <p class="text-sm font-medium text-gray-500 mt-1">Audit trail of all inventory inflows, outflows, and internal adjustments.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('stock-movements.ledger') }}">
                <x-button variant="secondary" class="py-2.5">
                    <i class="fas fa-book-open mr-2 text-[10px]"></i>
                    Full Audit Ledger
                </x-button>
            </a>
        </div>
    </div>

    {{-- ── Table ── --}}
    <x-table 
        :headers="['Movement Timeline', 'Classification', 'Reference / Origin', 'Operator', 'Batch Size', 'Actions']"
        :empty="$movements->isEmpty()"
        emptyMessage="No inventory movements recorded in the system yet."
    >
        @foreach($movements as $movement)
            <tr class="group hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4">
                    <div class="text-sm font-black text-gray-900">{{ $movement->created_at->format('M d, Y') }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $movement->created_at->format('H:i:s') }}</div>
                </td>
                <td class="px-6 py-4">
                    @php
                        $color = match($movement->reference_type) {
                            'Receipt' => 'bg-emerald-50 text-emerald-600',
                            'Delivery' => 'bg-rose-50 text-rose-600',
                            'Transfer' => 'bg-indigo-50 text-indigo-600',
                            'Adjustment' => 'bg-amber-50 text-amber-600',
                            default => 'bg-gray-50 text-gray-500'
                        };
                    @endphp
                    <span class="px-2.5 py-1 {{ $color }} text-[10px] font-black rounded-lg uppercase tracking-wider">
                        {{ $movement->getMovementTypeLabel() }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-700 leading-tight">{{ $movement->reference_type }}</div>
                    @if($movement->reference_id)
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Ref: #{{ $movement->reference_id }}</div>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500">
                            {{ substr($movement->user->name, 0, 1) }}
                        </div>
                        <span class="text-xs font-bold text-gray-600">{{ $movement->user->name }}</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-[10px] font-black rounded-lg uppercase tracking-wider">
                        {{ $movement->items->count() }} Line Items
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('stock-movements.show', $movement) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="View Details">
                            <i class="fas fa-long-arrow-alt-right text-xs"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-8">
        {{ $movements->links() }}
    </div>
@endsection
