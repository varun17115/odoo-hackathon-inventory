@extends('layouts.app')
@section('page-title', 'Products')

@section('content')
<style>
.ui-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.06); margin-bottom:20px; overflow:hidden; }
.ui-card-body { padding:20px; }
.ui-action-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
.ui-page-sub { font-size:0.875rem; color:#64748b; margin:4px 0 0; }
.ui-btn-primary { background:#4f46e5; color:#fff; border:none; border-radius:8px; padding:8px 16px; font-size:0.875rem; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.ui-btn-primary:hover { background:#3730a3; color:#fff; text-decoration:none; }
.ui-btn-secondary { background:#fff; color:#374151; border:1px solid #e2e8f0; border-radius:8px; padding:8px 16px; font-size:0.875rem; font-weight:500; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.ui-btn-secondary:hover { background:#f8fafc; color:#374151; text-decoration:none; }
.ui-btn-sm { padding:5px 10px; font-size:0.75rem; }
.ui-btn-danger { background:#ef4444; color:#fff; border:none; border-radius:8px; padding:5px 10px; font-size:0.75rem; font-weight:600; cursor:pointer; }
.ui-btn-danger:hover { background:#dc2626; }

.ui-filter-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; align-items:end; }
.ui-label { font-size:0.75rem; font-weight:500; color:#64748b; display:block; margin-bottom:5px; }
.ui-input { border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px; font-size:0.875rem; width:100%; font-family:'Inter',sans-serif; outline:none; }
.ui-input:focus { outline:2px solid #4f46e5; border-color:#4f46e5; }
.ui-select { border:1px solid #e2e8f0; border-radius:8px; padding:8px 12px; font-size:0.875rem; width:100%; font-family:'Inter',sans-serif; outline:none; background:#fff; }
.ui-select:focus { outline:2px solid #
}">
    {{-- ── Action Bar ── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Products</h2>
            <p class="text-sm font-medium text-gray-500 mt-1">Manage your global inventory and product variants.</p>
        </div>
        <x-button @click="resetForm(); modalOpen = true" class="shadow-blue-500/25 py-2.5">
            <i class="fas fa-plus mr-2 text-[10px]"></i>
            New Product
        </x-button>
    </div>

    {{-- ── Filters ── --}}
    <x-card class="mb-8 overflow-visible">
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <x-input 
                label="Search Products" 
                name="search" 
                :value="request('search')" 
                placeholder="Name or SKU..."
            />
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Category</label>
                <select name="category_id" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                <select name="is_active" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="flex items-end gap-3 pb-4">
                <x-button class="flex-1 justify-center py-2.5">
                    <i class="fas fa-filter mr-2 text-[10px]"></i> Apply
                </x-button>
                @if(request()->hasAny(['search', 'category_id', 'is_active']))
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors text-sm font-bold">
                        <i class="fas fa-undo"></i>
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    {{-- ── Table ── --}}
    <x-table 
        :headers="['Product Details', 'Category', 'Pricing', 'Stock Status', 'Visibility', 'Actions']"
        :empty="$products->isEmpty()"
        emptyMessage="No products match your criteria."
    >
        @foreach($products as $product)
            <tr class="group hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                            <i class="fas fa-box text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900 leading-tight">{{ $product->name }}</div>
                            <div class="text-[10px] font-mono text-gray-400 mt-0.5 tracking-tighter uppercase">{{ $product->sku }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-[10px] font-black rounded-lg uppercase tracking-wider">
                        {{ $product->category->name ?? 'Uncategorized' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-900">${{ number_format($product->price, 2) }}</div>
                    <div class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">Selling Price</div>
                </td>
                <td class="px-6 py-4">
                    @php $totalStock = $product->stocks->sum('quantity'); @endphp
                    @php $rule = $product->reorderRules->first(); @endphp
                    <div class="flex items-center gap-3">
                        <div class="px-3 py-1 rounded-lg text-xs font-black
                            {{ $totalStock == 0 ? 'bg-rose-50 text-rose-600' : ($totalStock < 10 ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600') }}">
                            {{ number_format($totalStock) }} {{ $product->unit }}s
                        </div>
                        @if($rule && $totalStock <= $rule->min_quantity)
                            <i class="fas fa-exclamation-triangle text-rose-500 text-xs animate-pulse" title="Below Reorder Point ({{ $rule->min_quantity }})"></i>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($product->is_active)
                        <span class="inline-flex w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                    @else
                        <span class="inline-flex w-2 h-2 rounded-full bg-gray-300"></span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('products.show', $product) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="View Details">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        <button @click="editProduct({{ json_encode($product) }})" class="p-2 text-gray-400 hover:text-amber-500 transition-colors" title="Edit">
                            <i class="fas fa-pencil-alt text-xs"></i>
                        </button>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirmDelete(this)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 transition-colors" title="Delete">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-8">
        {{ $products->links() }}
    </div>

    {{-- ── Product Modal (Alpine.js) ── --}}
    <div 
        x-show="modalOpen" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center"
        @keydown.escape.window="modalOpen = false"
    >
        <div x-show="modalOpen" class="fixed inset-0 transform transition-all" @click="modalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        </div>

        <div x-show="modalOpen" class="bg-white rounded-3xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-2xl animate-in zoom-in-95 duration-200" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-black text-gray-900" x-text="isEdit ? 'Update Product' : 'Create New Product'"></h3>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-0.5">Specifications & Attributes</p>
                </div>
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
                    <x-input label="Product Name *" name="name" x-model="formData.name" required />
                    <x-input label="SKU / Barcode *" name="sku" x-model="formData.sku" required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Category *</label>
                        <select name="category_id" x-model="formData.category_id" required class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                            <option value="">-- Select Category --</option>
                            @foreach(\App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Unit of Measure *</label>
                        <select name="unit" x-model="formData.unit" required class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200">
                            <option value="">-- Select Unit --</option>
                            @foreach(['Piece', 'Box', 'Kg', 'Liter', 'Meter', 'Dozen', 'Pack', 'Carton'] as $u)
                                <option value="{{ $u }}">{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input label="Selling Price *" type="number" step="0.01" name="price" x-model="formData.price" required />
                    <x-input label="Cost Price" type="number" step="0.01" name="cost" x-model="formData.cost" />
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Product Description</label>
                    <textarea name="description" x-model="formData.description" rows="3" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200" placeholder="Technical specifications or notes..."></textarea>
                </div>

                <div class="flex justify-end items-center gap-4 pt-4 border-t border-gray-100">
                    <button type="button" @click="modalOpen = false" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors uppercase tracking-widest">Cancel</button>
                    <x-button class="py-3 px-8 shadow-blue-500/20">
                        <span x-text="isEdit ? 'Update Product' : 'Save Product'"></span>
                    </x-button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
