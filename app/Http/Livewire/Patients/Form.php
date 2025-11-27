<?php

namespace App\Http\Livewire\Patients;

use Livewire\Component;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class Form extends Component
{
    public ?Patient $patient = null;

    public $state = [];
    protected $listeners = ['deletePatient' => 'delete'];

    public function mount(?Patient $patient = null)
    {
        $this->patient = $patient;
        $this->state = $patient ? $patient->toArray() : [];
    }

    public function save()
    {
        $user = Auth::user();

        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'nullable|date',
        ];

        $this->validate($rules);

        if ($this->patient) {
            $this->patient->update($this->state);
        } else {
            $this->patient = Patient::create(array_merge($this->state, ['clinic_id' => $user->clinic_id ?? $this->state['clinic_id'] ?? null]));
        }

        $this->emit('saved');
    }

    public function render()
    {
        return view('livewire.patients.form');
    }

    public function delete(int $id = null)
    {
        $p = $this->patient ?? ($id ? Patient::find($id) : null);
        if (! $p) {
            $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'Patient not found']);
            return;
        }

        $p->delete();
        $this->dispatchBrowserEvent('notify', ['type' => 'success', 'message' => 'Patient deleted']);
        redirect()->route('patients.index');
    }
}
