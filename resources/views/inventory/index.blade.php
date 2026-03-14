@extends('layouts.app')

@section('page-title', 'Live Inventory Status')

@section('content')
    {{-- ── Status Overview ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-layer-group text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900">{{ number_format($stocks->total()) }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Stock Records</div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-cubes text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900">{{ number_format($stocks->sum('quantity')) }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Units</div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-amber-600">{{ number_format($totalLowStock) }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Low Stock</div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-rose-600">{{ number_format($totalOutOfStock) }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Out of Stock</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Advanced Filters ── --}}
    <x-card class="mb-8 overflow-visible">
        <form method="GET" action="{{ route('inventory.index') }}" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-6">
            <div class="md:col-span-2">
                <x-input label="Search Inventory" name="search" :value="request('search')" placeholder="Product name or SKU..." />
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Warehouse</label>
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
                @if(request()->hasAny(['search','warehouse_id','low_stock','out_of_stock']))
                    <a href="{{ route('inventory.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors text-sm font-bold">
                        <i class="fas fa-undo"></i>
                    </a>
                @endif
            </div>

            {{-- Filter Pills --}}
            <div class="md:col-span-4 lg:col-span-5 flex flex-wrap gap-3 pt-2">
                <a href="{{ route('inventory.index', array_merge(request()->query(), ['low_stock' => 1])) }}"
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border transition-all duration-200 {{ request('low_stock') ? 'bg-amber-500 text-white border-amber-500 shadow-lg shadow-amber-500/20' : 'bg-white text-amber-600 border-amber-100 hover:bg-amber-50' }}">
                    <i class="fas fa-exclamation-triangle text-[10px]"></i> Low Stock Only
                </a>
                <a href="{{ route('inventory.index', array_merge(request()->query(), ['out_of_stock' => 1])) }}"
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border transition-all duration-200 {{ request('out_of_stock') ? 'bg-rose-500 text-white border-rose-500 shadow-lg shadow-rose-500/20' : 'bg-white text-rose-600 border-rose-100 hover:bg-rose-50' }}">
                    <i class="fas fa-times-circle text-[10px]"></i> Out of Stock
                </a>
            </div>
        </form>
    </x-card>

    {{-- ── Inventory Table ── --}}
    <x-table 
        :headers="['Product Details', 'SKU', 'Warehouse / Location', 'Available Stock', 'Actions']"
        :empty="$stocks->isEmpty()"
        emptyMessage="No stock records match your search."
    >
        @foreach($stocks as $stock)
            <tr class="group hover:bg-gray-50/80 transition-colors {{ $stock->quantity == 0 ? 'bg-rose-50/30' : ($stock->quantity < 10 ? 'bg-amber-50/30' : '') }}">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-black
                            {{ $stock->quantity == 0 ? 'bg-rose-100 text-rose-600' : ($stock->quantity < 10 ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600') }}">
                            {{ substr($stock->product->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ $stock->product->name }}</div>
                            @if($stock->quantity == 0)
                                <span class="text-[10px] font-black uppercase text-rose-500 tracking-tighter">🚨 Depleted</span>
                            @elseif($stock->quantity < 10)
                                <span class="text-[10px] font-black uppercase text-amber-500 tracking-tighter">⚠️ Critical</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="text-[10px] font-mono font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded tracking-tighter uppercase">{{ $stock->product->sku }}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-700">{{ $stock->warehouse->name }}</div>
                    @if($stock->location)
                        <div class="flex items-center gap-1.5 text-[10px] font-black text-blue-500 uppercase mt-0.5">
                            <i class="fas fa-location-dot"></i>
                            {{ $stock->location->name }}
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="text-xl font-black {{ $stock->quantity == 0 ? 'text-rose-600' : ($stock->quantity < 10 ? 'text-amber-600' : 'text-gray-900') }}">
                        {{ number_format($stock->quantity) }}
                    </div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $stock->product->unit }}s</div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('inventory.by-product', $stock->product->id) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="Product breakdown">
                            <i class="fas fa-chart-bar text-xs"></i>
                        </a>
                        <a href="{{ route('stock-movements.by-product', $stock->product->id) }}" class="p-2 text-gray-400 hover:text-indigo-600 transition-colors" title="Movement history">
                            <i class="fas fa-history text-xs"></i>
                        </a>
                        <a href="{{ route('adjustments.create', ['product_id' => $stock->product_id, 'warehouse_id' => $stock->warehouse_id]) }}" class="p-2 text-gray-400 hover:text-amber-600 transition-colors" title="Quick adjustment">
                            <i class="fas fa-sliders text-xs"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-8">
        {{ $stocks->appends(request()->query())->links() }}
    </div>
@endsection
