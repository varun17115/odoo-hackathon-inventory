@props(['disabled' => false, 'label' => null, 'id' => null, 'error' => null])

<div class="mb-4 last:mb-0">
    @if($label)
        <label @if($id) for="{{ $id }}" @endif class="block text-sm font-semibold text-gray-700 mb-1.5">
            {{ $label }}
        </label>
    @endif

    <input {{ $disabled ? 'disabled' : '' }} @if($id) id="{{ $id }}" @endif {!! $attributes->merge(['class' => 'w-full px-4 py-2 text-sm border ' . ($error ? 'border-red-500' : 'border-gray-300') . ' rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition duration-200 disabled:bg-gray-50 disabled:text-gray-500']) !!}>
    
    @if($error)
        <p class="mt-1 text-xs text-red-600">{{ $error }}</p>
    @endif
</div>
