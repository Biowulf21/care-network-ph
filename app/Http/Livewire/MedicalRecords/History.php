<?php

namespace App\Http\Livewire\MedicalRecords;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
    use WithPagination;

    public Patient $patient;

    public $editingRecordId = null;
    public $editingState = [];

    protected $listeners = ['refreshHistory' => '$refresh'];

    protected $rules = [
        'editingState.chief_complaint' => 'nullable|string|max:500',
        'editingState.diagnosis' => 'nullable|string',
        'editingState.treatment_plan' => 'nullable|string',
        'editingState.consultation_date' => 'nullable|date',
        'editingState.disposition' => 'nullable|string',
    ];

    public function mount(Patient $patient)
    {
        $this->patient = $patient;

        // authorization similar to patient profile
        $user = Auth::user();
        if ($user->hasRole('delegate') && $patient->clinic_id !== $user->clinic_id) {
            abort(403);
        }
        if ($user->hasRole('admin') && $patient->clinic->organization_id !== $user->organization_id) {
            abort(403);
        }
        // If a specific record is requested via query param, open it for viewing/editing
        $requested = request()->query('record');
        if ($requested) {
            $record = $this->patient->medicalRecords()->find($requested);
            if ($record) {
                $this->editingRecordId = $record->id;
                $this->editingState = $record->only(['chief_complaint','diagnosis','treatment_plan','consultation_date','disposition','encounter_type','doctor_notes']);
            }
        }
    }

    public function editRecord(int $id)
    {
        $record = $this->patient->medicalRecords()->findOrFail($id);

        if (Gate::denies('view', $record)) {
            $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'Not authorized']);
            return;
        }

        $this->editingRecordId = $record->id;
        $this->editingState = $record->only(['chief_complaint','diagnosis','treatment_plan','consultation_date','disposition','encounter_type','doctor_notes']);
    }

    public function cancelEdit()
    {
        $this->editingRecordId = null;
        $this->editingState = [];
    }

    public function saveRecord()
    {
        $this->validate();

        $record = MedicalRecord::findOrFail($this->editingRecordId);

        if (Gate::denies('update', $record)) {
            $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'Not authorized']);
            return;
        }

        $record->update($this->editingState);
        $this->dispatchBrowserEvent('notify', ['type' => 'success', 'message' => 'Record updated']);
        $this->cancelEdit();
        $this->emit('refreshHistory');
    }

    public function deleteRecord(int $id)
    {
        $record = MedicalRecord::findOrFail($id);

        if (Gate::denies('delete', $record)) {
            $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'Not authorized']);
            return;
        }

        $record->delete();
        $this->dispatchBrowserEvent('notify', ['type' => 'success', 'message' => 'Record deleted']);
        $this->resetPage();
    }

    public function render()
    {
        $records = $this->patient->medicalRecords()->with('user','clinic')->orderByDesc('consultation_date')->paginate(20);

        return view('livewire.medical-records.history', ['records' => $records]);
    }
}
