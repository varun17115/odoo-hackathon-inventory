@extends('layouts.app')

@section('title', $receipt->receipt_number . ' - Invento Market')
@section('page-title', 'Receipt: ' . $receipt->receipt_number)

@section('content')
<div class="mb-6">
    <a href="{{ route('receipts.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Receipts</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Receipt Details -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">{{ $receipt->receipt_number }}</h2>
                <p class="text-gray-600 mt-1">{{ $receipt->receipt_date->format('F d, Y') }}</p>
            </div>
            <div class="flex gap-2">
                @if($receipt->status === 'pending')
                    <form method="POST" action="{{ route('receipts.verify', $receipt) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Verify & Update Inventory
                        </button>
                    </form>
                    <form method="POST" action="{{ route('receipts.destroy', $receipt) }}" class="inline" onsubmit="return confirmDelete(this)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-8">
            <div>
                <p class="text-gray-600 text-sm">Supplier</p>
                <p class="text-lg font-semibold text-gray-800">{{ $receipt->supplier->name }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Warehouse</p>
                <p class="text-lg font-semibold text-gray-800">{{ $receipt->warehouse->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Received By</p>
                <p class="text-lg font-semibold text-gray-800">{{ $receipt->user->name }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Status</p>
                <div class="mt-1">
                    @if($receipt->status === 'pending')
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Pending</span>
                    @elseif($receipt->status === 'received')
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Received</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Verified</span>
                    @endif
                </div>
            </div>
        </div>

        @if($receipt->notes)
            <div class="mb-8">
                <p class="text-gray-600 text-sm">Notes</p>
                <p class="text-gray-800">{{ $receipt->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Summary -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Summary</h3>
        <div class="space-y-4">
            <div>
                <p class="text-gray-600 text-sm">Total Items</p>
                <p class="text-3xl font-bold text-blue-600">{{ $receipt->items->count() }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Total Quantity</p>
                <p class="text-3xl font-bold text-green-600">{{ $receipt->items->sum('quantity') }}</p>
            </div>
            <div class="border-t pt-4">
                <p class="text-gray-600 text-sm">Total Amount</p>
                <p class="text-3xl font-bold text-purple-600">${{ number_format($receipt->total_amount, 2) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Items -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Items</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Unit Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Total Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Rack</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($receipt->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $item->product->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">${{ number_format($item->total_price, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->rack->name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
