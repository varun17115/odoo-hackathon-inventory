@extends('layouts.app')

@section('page-title', 'Stock Replenishment Rules')

@section('content')
<div x-data="{ 
    modalOpen: false, 
    isEdit: false,
    formAction: '{{ route('reorder-rules.store') }}',
    formData: { id: '', product_id: '', warehouse_id: '', min_quantity: '', reorder_quantity: '', preferred_supplier_id: '', is_active: true },
    resetForm() {
        this.isEdit = false;
        this.formAction = '{{ route('reorder-rules.store') }}';
        this.formData = { id: '', product_id: '', warehouse_id: '', min_quantity: '', reorder_quantity: '', preferred_supplier_id: '', is_active: true };
    },
    editRule(rule) {
        this.isEdit = true;
        this.formAction = '/reorder-rules/' + rule.id;
        this.formData = { ...rule };
        this.modalOpen = true;
    }
}">
    {{-- ── Status Overview ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-robot text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900">{{ $rules->total() }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Active Automation Rules</div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-rose-600">{{ $rules->getCollection()->where('triggered', true)->count() }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Currently Triggered</div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-double text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-emerald-600">{{ $rules->getCollection()->where('is_active', true)->count() }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Enabled Rules</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Action Bar ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Reorder Rules</h2>
            <p class="text-sm font-medium text-gray-500 mt-1">Configure automated stock replenishment thresholds and supplier preferences.</p>
        </div>
        <x-button @click="resetForm(); modalOpen = true" class="shadow-blue-500/25 py-2.5">
            <i class="fas fa-plus mr-2 text-[10px]"></i>
            Add New Rule
        </x-button>
    </div>

    {{-- ── Table ── --}}
    <x-table 
        :headers="['Product Context', 'Thresholds', 'Current vs Min', 'Supplier Strategy', 'Status', 'Actions']"
        :empty="$rules->isEmpty()"
        emptyMessage="No reorder rules defined yet."
    >
        @foreach($rules as $rule)
            <tr class="group hover:bg-gray-50/80 transition-colors {{ $rule->triggered && $rule->is_active ? 'bg-rose-50/30' : '' }}">
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-900 leading-tight">{{ $rule->product->name }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase mt-0.5 tracking-tighter">{{ $rule->warehouse->name ?? 'Global (All Sites)' }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-xs font-bold text-gray-700">Min: {{ number_format($rule->min_quantity) }}</div>
                    <div class="text-[10px] text-gray-400 font-black uppercase mt-0.5 tracking-widest">Order: {{ number_format($rule->reorder_quantity) }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="text-sm font-black {{ $rule->triggered ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ number_format($rule->current_stock) }}
                        </div>
                        <div class="w-24 bg-gray-100 h-1.5 rounded-full overflow-hidden">
                            @php $pct = min(100, ($rule->current_stock / max(1, $rule->min_quantity)) * 50); @endphp
                            <div class="h-full {{ $rule->triggered ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm font-bold text-gray-600">
                    {{ $rule->preferredSupplier->name ?? 'Direct Procurement' }}
                </td>
                <td class="px-6 py-4 text-center">
                    @if(!$rule->is_active)
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-400 text-[10px] font-black rounded-lg uppercase tracking-wider">Disabled</span>
                    @elseif($rule->triggered)
                        <span class="px-2.5 py-1 bg-rose-50 text-rose-600 text-[10px] font-black rounded-lg uppercase tracking-wider animate-pulse">Critical</span>
                    @else
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-lg uppercase tracking-wider">Active</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2 justify-end">
                        <button @click="editRule({{ json_encode($rule) }})" class="p-2 text-gray-400 hover:text-amber-500 transition-colors" title="Edit Rule">
                            <i class="fas fa-pencil-alt text-xs"></i>
                        </button>
                        <form method="POST" action="{{ route('reorder-rules.destroy', $rule) }}" onsubmit="return confirmDelete(this)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 transition-colors" title="Delete Rule">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-8">
        {{ $rules->links() }}
    </div>

    {{-- ── Modal ── --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center">
        <div x-show="modalOpen" class="fixed inset-0 transform transition-all" @click="modalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        </div>

        <div x-show="modalOpen" class="bg-white rounded-3xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-2xl animate-in zoom-in-95 duration-200" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-xl font-black text-gray-900" x-text="isEdit ? 'Update Reorder Strategy' : 'Define New Automation Rule'"></h3>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form :action="formAction" method="POST" class="p-8 space-y-6">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Product Asset *</label>
                        <select name="product_id" x-model="formData.product_id" required :disabled="isEdit" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200 disabled:bg-gray-50 disabled:text-gray-500">
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Site Location</label>
                        <select name="warehouse_id" x-model="formData.warehouse_id" :disabled="isEdit" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200 disabled:bg-gray-50 disabled:text-gray-500">
                            <option value="">Global Rule (All Warehouses)</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input label="Minimum Threshold *" type="number" name="min_quantity" x-model="formData.min_quantity" required />
                    <x-input label="Standard Reorder Qty *" type="number" name="reorder_quantity" x-model="formData.reorder_quantity" required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Preferred Supplier</label>
                        <select name="preferred_supplier_id" x-model="formData.preferred_supplier_id" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                            <option value="">-- Manual Selection --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center mb-2">
                        <input type="checkbox" name="is_active" x-model="formData.is_active" value="1" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                        <label class="ml-2 text-sm font-bold text-gray-700 uppercase tracking-widest">Enable Rule</label>
                    </div>
                </div>

                <div class="flex justify-end items-center gap-4 pt-4 border-t border-gray-100">
                    <button type="button" @click="modalOpen = false" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors uppercase tracking-widest">Cancel</button>
                    <x-button class="py-3 px-8 shadow-blue-500/20">
                        <span x-text="isEdit ? 'Update Rule' : 'Initialize Automation'"></span>
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
