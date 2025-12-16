<?php

namespace App\Http\Livewire;

use Livewire\Component;

class SearchableDropdown extends Component
{
    public $options = [];
    public $placeholder = 'Select';
    public $value = null; // bound via wire:model on the component tag
    public $query = '';
    public $bind = null;

    public function mount($options = [], $placeholder = 'Select', $value = null)
    {
        $this->options = is_array($options) ? $options : (is_object($options) ? (array) $options : (array) $options);
        $this->placeholder = $placeholder;
        $this->value = $value;
    }

    public function select($val)
    {
        $this->value = $val;
        // emit event to parent with binding path (e.g. "state.sex") so parent can update its state
        // Livewire v3: dispatch events instead of emit/emitUp
        $this->dispatch('searchableSet', $this->bind, $val);
    }

    public function updatedValue($val)
    {
        // keep value in sync (Livewire will propagate to parent when using wire:model on the component)
        // In Livewire v3 `emitUp` was removed; nested model syncing should be handled via
        // browser events or parent listeners. Dispatch an event for compatibility.
        $this->dispatch('input', $val);
    }

    public function render()
    {
        $filtered = collect($this->options)->filter(function ($label, $key) {
            if ($this->query === '') return true;
            return str_contains(strtolower($label), strtolower($this->query));
        });

        return view('livewire.searchable-dropdown', [
            'filtered' => $filtered->toArray(),
        ]);
    }
}
