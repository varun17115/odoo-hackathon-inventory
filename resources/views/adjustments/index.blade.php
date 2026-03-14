@extends('layouts.app')
@section('title', 'Stock Adjustments')
@section('page-title', 'Stock Adjustments')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-gray-500">Physical count reconciliations and manual stock corrections.</p>
    <a href="{{ route('adjustments.create') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-semibold hover:bg-orange-700">
        <i class="fas fa-plus mr-2"></i>New Adjustment
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">By</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Items</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Notes</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($adjustments as $adj)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-gray-500">#{{ $adj->id }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $adj->created_at->format('M j, Y H:i') }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $adj->user?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center justify-center w-7 h-7 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">
                        {{ $adj->items->count() }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $adj->notes ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('adjustments.show', $adj) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                        <i class="fas fa-eye mr-1"></i>View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                    <i class="fas fa-clipboard-list text-3xl mb-3 block"></i>
                    No adjustments yet. <a href="{{ route('adjustments.create') }}" class="text-orange-600 hover:underline">Create one</a>.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($adjustments->hasPages())
    <div class="px-4 py-3 border-t border-gray-200">
        {{ $adjustments->links() }}
    </div>
    @endif
</div>
@endsection
