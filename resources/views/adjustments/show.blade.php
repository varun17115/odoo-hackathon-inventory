@extends('layouts.app')
@section('title', 'Adjustment #' . $adjustment->id)
@section('page-title', 'Adjustment #' . $adjustment->id)

@section('content')
<div class="mb-4">
    <a href="{{ route('adjustments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
        <i class="fas fa-arrow-left mr-1"></i>Back to Adjustments
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Details card --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-800 border-b pb-2">Adjustment Info</h3>
            <div class="text-sm space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Reference</span>
                    <span class="font-mono font-semibold text-gray-800">#{{ $adjustment->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Date</span>
                    <span class="text-gray-800">{{ $adjustment->created_at->format('M j, Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Performed by</span>
                    <span class="text-gray-800">{{ $adjustment->user?->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Items adjusted</span>
                    <span class="font-semibold text-orange-600">{{ $adjustment->items->count() }}</span>
                </div>
                @if($adjustment->notes)
                <div class="pt-2 border-t">
                    <p class="text-gray-500 text-xs mb-1">Notes</p>
                    <p class="text-gray-800">{{ $adjustment->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Items table --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-800">Adjusted Items</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Location</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Qty Changed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($adjustment->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $item->product?->name ?? '—' }}</div>
                            <div class="text-xs text-gray-400">{{ $item->product?->sku }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $item->sourceLocation?->name ?? 'No specific rack' }}
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-gray-700">
                            {{ $item->quantity }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-400">No items recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
