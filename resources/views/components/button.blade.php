@props(['type' => 'submit', 'variant' => 'primary'])

@php
    $baseClasses = 'inline-flex items-center px-4 py-2 border rounded-lg font-semibold text-sm transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50';
    $variants = [
        'primary' => 'bg-blue-600 border-transparent text-white hover:bg-blue-700 active:bg-blue-800 focus:ring-blue-500',
        'secondary' => 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50 active:bg-gray-100 focus:ring-blue-500',
        'danger' => 'bg-red-600 border-transparent text-white hover:bg-red-700 active:bg-red-800 focus:ring-red-500',
        'success' => 'bg-emerald-600 border-transparent text-white hover:bg-emerald-700 active:bg-emerald-800 focus:ring-emerald-500',
        'warning' => 'bg-amber-500 border-transparent text-white hover:bg-amber-600 active:bg-amber-700 focus:ring-amber-500',
    ];
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
    {{ $slot }}
</button>
