@extends('layouts.app')

@section('title', 'Dashboard - Invento Market')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Products Card -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Products</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalProducts }}</p>
            </div>
            <svg class="w-12 h-12 text-blue-500 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 6H6.28l-.31-1.243A1 1 0 005 4H3z"></path>
                <path d="M16 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
                <path d="M4 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
    </div>

    <!-- Total Categories Card -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Categories</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalCategories }}</p>
            </div>
            <svg class="w-12 h-12 text-green-500 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                <path d="M7 3a1 1 0 000 2h6a1 1 0 000-2H7zM4 7a1 1 0 011-1h10a1 1 0 011 1v3a2 2 0 01-2 2H6a2 2 0 01-2-2V7z"></path>
                <path d="M2 13a1 1 0 011-1h14a1 1 0 011 1v3a2 2 0 01-2 2H4a2 2 0 01-2-2v-3z"></path>
            </svg>
        </div>
    </div>

    <!-- Low Stock Products Card -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Low Stock Items</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $lowStockProducts }}</p>
            </div>
            <svg class="w-12 h-12 text-yellow-500 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
        </div>
    </div>

    <!-- Inventory Value Card -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Inventory Value</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($totalInventoryValue, 2) }}</p>
            </div>
            <svg class="w-12 h-12 text-purple-500 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8.16 2.75a.75.75 0 00-1.32 0l-3.5 6A.75.75 0 004 9h12a.75.75 0 00.66-1.25l-3.5-6zM12.5 14a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
        </div>
    </div>
</div>


<!-- Recent Activity and Top Products -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Recent Inventory Logs -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $log->product->name }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $log->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($log->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $log->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $log->user->name ?? 'System' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $log->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No recent activity</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Top Products</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($topProducts as $product)
                <div class="p-4 hover:bg-gray-50">
                    <p class="text-sm font-medium text-gray-800">{{ $product->name }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ $product->logs_count }} transactions</p>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500 text-sm">No products yet</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Inventory Status -->
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Inventory Status</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Current Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Min Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Max Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($inventoryStatus as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $item['product'] }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $item['quantity'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item['min'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item['max'] }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($item['status'] === 'low')
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Low Stock</span>
                            @elseif($item['status'] === 'over')
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Overstock</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Normal</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No inventory data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
