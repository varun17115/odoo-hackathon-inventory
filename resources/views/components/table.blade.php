@props(['headers' => [], 'empty' => false, 'emptyMessage' => 'No data found.'])

<div class="overflow-x-auto rounded-xl border border-gray-100 bg-white shadow-sm">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-100']) }}>
        <thead class="bg-gray-50/80 backdrop-blur-sm">
            <tr>
                @foreach($headers as $header)
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @if($empty)
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-6 py-12 text-center text-sm text-gray-400 font-medium">
                        <i class="fas fa-inbox block text-4xl mb-3 opacity-20"></i>
                        {{ $emptyMessage }}
                    </td>
                </tr>
            @else
                {{ $slot }}
            @endif
        </tbody>
    </table>
</div>
