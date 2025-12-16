<div x-data="{ open: false }" class="relative" x-cloak>
    <div class="relative">
        <input type="text"
            wire:model.debounce.250ms="query"
            placeholder="{{ $placeholder }}"
            @focus="open = true"
            @click.stop="open = true"
            @keydown.escape="open = false"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />

        <div class="absolute inset-y-0 end-0 px-3 text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
    </div>

    <div x-show="open" x-cloak @click.away="open = false" class="mt-1 absolute w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-56 overflow-auto z-50">
        @if(count($filtered))
            @foreach($filtered as $k => $label)
                <button type="button" @click.prevent.stop="open = false" wire:click="select('{{ $k }}')" class="w-full text-left px-3 py-2 hover:bg-gray-50 text-sm text-gray-700">{{ $label }}</button>
            @endforeach
        @else
            <div class="px-3 py-2 text-sm text-gray-500">No results</div>
        @endif
    </div>
</div>
