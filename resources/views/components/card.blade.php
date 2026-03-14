@props(['title' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100']) }}>
    @if ($title)
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
        </div>
    @endif

    <div class="p-6 text-gray-900">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $footer }}
        </div>
    @endif
</div>
