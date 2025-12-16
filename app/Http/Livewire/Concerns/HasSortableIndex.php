<?php

namespace App\Http\Livewire\Concerns;

trait HasSortableIndex
{
    // Default to sorting by created_at descending
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            // default to ascending when switching to a new column
            $this->sortDir = 'asc';
        }

        // Reset pagination if used
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    protected function applySort($query)
    {
        // Apply order by on the query using current sort settings
        return $query->orderBy($this->sortBy, $this->sortDir);
    }
}
