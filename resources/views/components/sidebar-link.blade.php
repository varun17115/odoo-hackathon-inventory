@props(['active' => false, 'icon' => null])

@php
    $classes = ($active ?? false)
                ? 'flex items-center px-4 py-2.5 text-sm font-medium text-white bg-blue-700/50 border-l-4 border-blue-400'
                : 'flex items-center px-4 py-2.5 text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700/30 border-l-4 border-transparent transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <i class="{{ $icon }} w-5 h-5 mr-3 text-center transition-colors"></i>
    @endif
    <span>{{ $slot }}</span>
</a>
