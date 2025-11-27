<?php

namespace App\Http\Livewire\Patients;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['deletePatient' => 'delete'];

    public function render()
    {
        $user = Auth::user();
        $query = Patient::query();

        if ($user->hasRole('admin')) {
            $query->whereHas('clinic', fn ($q) => $q->where('organization_id', $user->organization_id));
        }

        if ($user->hasRole('delegate')) {
            $query->where('clinic_id', $user->clinic_id);
        }

        if ($this->search) {
            $query->where(fn ($q) => $q->where('first_name', 'like', "%{$this->search}%")->orWhere('last_name', 'like', "%{$this->search}%"));
        }

        return view('livewire.patients.index', ['patients' => $query->paginate(15)]);
    }

    public function delete(int $id)
    {
        $patient = Patient::findOrFail($id);

        if (Gate::denies('delete', $patient)) {
            $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'Not authorized']);

            return;
        }

        $patient->delete();
        $this->resetPage();
        $this->dispatchBrowserEvent('notify', ['type' => 'success', 'message' => 'Patient deleted']);
    }
}
