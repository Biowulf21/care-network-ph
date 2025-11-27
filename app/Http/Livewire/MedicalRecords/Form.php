<?php

namespace App\Http\Livewire\MedicalRecords;

use Livewire\Component;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\Clinic;

class Form extends Component
{
    public ?MedicalRecord $record = null;
    public $state = [];
    protected $listeners = ['deleteRecord' => 'delete'];

    public function mount(?MedicalRecord $record = null)
    {
        $this->record = $record;
        $this->state = $record ? $record->toArray() : [];
    }

    public function save()
    {
        $this->validate([
            'state.patient_id' => 'required|exists:patients,id',
            'state.clinic_id' => 'required|exists:clinics,id',
        ]);

        $user = Auth::user();

        if ($this->record) {
            $this->record->update($this->state);
        } else {
            $this->record = MedicalRecord::create(array_merge($this->state, ['user_id' => $user->id]));
        }

        $this->emit('saved');
    }

    public function render()
    {
        $patients = Patient::orderBy('last_name')->get();
        $clinics = Clinic::orderBy('name')->get();

        return view('livewire.medical-records.form', [
            'patients' => $patients,
            'clinics' => $clinics,
        ]);
    }

    public function delete(int $id = null)
    {
        $rec = $this->record ?? ($id ? MedicalRecord::find($id) : null);
        if (! $rec) {
            $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'Record not found']);
            return;
        }

        $rec->delete();
        $this->dispatchBrowserEvent('notify', ['type' => 'success', 'message' => 'Record deleted']);
        redirect()->route('medical-records.index');
    }
}
