@props([
    'options' => [],
    'placeholder' => 'Select',
    'emptyLabel' => 'No results',
])

<div x-data="{
        open: false,
        query: '',
        selectedLabel: null,
        selectedValue: null,
        optionsMap: {},
        get filtered() {
            const map = this.optionsMap || {};
            const entries = Object.entries(map);
            if (!this.query) return entries;
            return entries.filter(([k, v]) => v.toLowerCase().includes(this.query.toLowerCase()));
        }
    }"
    x-init="optionsMap = {{ json_encode($options) }};"
    class="relative">

    <!-- Hidden input bound to Livewire via forwarded wire:* attributes -->
    <input type="hidden" x-ref="hidden" {{ $attributes->whereStartsWith('wire:')->merge(['id' => $attributes->get('id') ?? null]) }} />

    <!-- Search / display input -->
    <div class="relative">
        <input
            type="text"
            x-model="query"
            @focus="open = true"
            @keydown.arrow-down.prevent=" $nextTick(() => $refs.list.querySelector('button')?.focus())"
            @click="open = true"
            :placeholder="selectedLabel ? selectedLabel : '{{ $placeholder }}'"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
        />

        <button type="button" @click="open = !open" class="absolute inset-y-0 end-0 px-3 text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
    </div>

    <div x-show="open" x-cloak @click.away="open = false" class="mt-1 z-50 absolute w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto">
        <template x-if="filtered.length">
            <div x-ref="list" class="divide-y divide-gray-100 dark:divide-gray-700">
                <template x-for="([val, label]) in filtered" :key="val">
                    <div>
                        <button type="button"
                            @click.prevent="selectedValue = val; selectedLabel = label; open = false; query = label; $refs.hidden.value = val; $refs.hidden.dispatchEvent(new Event('input')); $refs.hidden.dispatchEvent(new Event('change'))"
                                class="w-full text-left px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm text-gray-700 dark:text-gray-200">
                            <span x-text="label"></span>
                        </button>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="!filtered.length">
            <div class="px-3 py-2 text-sm text-gray-500">{{ $emptyLabel }}</div>
        </template>
    </div>
</div>
