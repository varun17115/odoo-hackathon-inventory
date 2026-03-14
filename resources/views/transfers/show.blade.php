@extends('layouts.app')

@section('title', $transfer->transfer_number . ' - Invento Market')
@section('page-title', 'Transfer: ' . $transfer->transfer_number)

@section('content')
<div class="mb-6">
    <a href="{{ route('transfers.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Transfers</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Transfer Details -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">{{ $transfer->transfer_number }}</h2>
                <p class="text-gray-600 mt-1">{{ $transfer->transfer_date->format('F d, Y') }}</p>
            </div>
            <div class="flex gap-2">
                @if($transfer->status === 'pending')
                    <form method="POST" action="{{ route('transfers.complete', $transfer) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Complete Transfer
                        </button>
                    </form>
                    <form method="POST" action="{{ route('transfers.cancel', $transfer) }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                            Cancel
                        </button>
                    </form>
                    <form method="POST" action="{{ route('transfers.destroy', $transfer) }}" class="inline" onsubmit="return confirmDelete(this)">
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
                <p class="text-gray-600 text-sm">From Warehouse</p>
                <p class="text-lg font-semibold text-gray-800">{{ $transfer->fromWarehouse->name }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">To Warehouse</p>
                <p class="text-lg font-semibold text-gray-800">{{ $transfer->toWarehouse->name }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Created By</p>
                <p class="text-lg font-semibold text-gray-800">{{ $transfer->user->name }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Status</p>
                <div class="mt-1">
                    @if($transfer->status === 'pending')
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Pending</span>
                    @elseif($transfer->status === 'completed')
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Completed</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Cancelled</span>
                    @endif
                </div>
            </div>
        </div>

        @if($transfer->notes)
            <div class="mb-8">
                <p class="text-gray-600 text-sm">Notes</p>
                <p class="text-gray-800">{{ $transfer->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Summary -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Summary</h3>
        <div class="space-y-4">
            <div>
                <p class="text-gray-600 text-sm">Total Items</p>
                <p class="text-3xl font-bold text-blue-600">{{ $transfer->items->count() }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Total Quantity</p>
                <p class="text-3xl font-bold text-green-600">{{ $transfer->getTotalQuantity() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Items -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Items</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">From Rack</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">To Rack</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($transfer->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $item->product->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->fromRack->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->toRack->name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
