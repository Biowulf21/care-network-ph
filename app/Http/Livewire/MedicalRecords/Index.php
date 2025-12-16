<?php

namespace App\Http\Livewire\MedicalRecords;

use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\Concerns\HasSortableIndex;

class Index extends Component
{
    use WithPagination;
    use HasSortableIndex;

    public $search = '';

    protected $listeners = ['deleteRecord' => 'delete'];

    public function render()
    {
        $user = Auth::user();
        $query = MedicalRecord::with('patient');

        if ($user->hasRole('admin')) {
            $query->whereHas('clinic', fn ($q) => $q->where('organization_id', $user->organization_id));
        }

        if ($user->hasRole('delegate')) {
            // delegates should only see records for their assigned clinic
            $query->where('clinic_id', $user->clinic_id);
        }

        if ($this->search) {
            $query->where('doctor_notes', 'like', "%{$this->search}%");
        }

        // Apply sortable ordering (default comes from trait: created_at desc)
        $this->applySort($query);

        return view('livewire.medical-records.index', ['records' => $query->paginate(15)]);
    }

    public function delete(int $id)
    {
        $record = MedicalRecord::findOrFail($id);

        if (Gate::denies('delete', $record)) {
            $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'Not authorized to delete']);

            return;
        }

        $record->delete();
        $this->resetPage();
        $this->dispatchBrowserEvent('notify', ['type' => 'success', 'message' => 'Record deleted']);
    }
}
