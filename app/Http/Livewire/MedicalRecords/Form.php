<?php

namespace App\Http\Livewire\MedicalRecords;

use App\Models\Clinic;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Form extends Component
{
    public ?MedicalRecord $record = null;

    public $state = [];

    protected $listeners = ['deleteRecord' => 'delete'];

    protected $rules = [
        'state.patient_id' => 'required|exists:patients,id',
        'state.clinic_id' => 'required|exists:clinics,id',
        'state.consultation_date' => 'nullable|date',
        'state.chief_complaint' => 'nullable|string|max:500',
        'state.history_present_illness' => 'nullable|string',
        'state.physical_examination' => 'nullable|string',
        'state.diagnosis' => 'nullable|string',
        'state.diagnosis_codes' => 'nullable|string',
        'state.assessment_plan' => 'nullable|string',
        'state.treatment_plan' => 'nullable|string',
        'state.disposition' => 'nullable|string',
        'state.next_appointment' => 'nullable|date',
        'state.encounter_type' => 'nullable|string',
        'state.doctor_notes' => 'nullable|string',
        'state.provider_notes' => 'nullable|string',
        'state.philhealth_number' => 'nullable|string',
        'state.vital_signs' => 'nullable|array',
    ];

    protected $validationAttributes = [
        'state.patient_id' => 'patient',
        'state.clinic_id' => 'clinic',
        'state.consultation_date' => 'consultation date',
        'state.chief_complaint' => 'chief complaint',
        'state.history_present_illness' => 'history of present illness',
        'state.physical_examination' => 'physical examination',
        'state.diagnosis' => 'diagnosis',
        'state.diagnosis_codes' => 'diagnosis codes',
        'state.assessment_plan' => 'assessment and plan',
        'state.treatment_plan' => 'treatment plan',
        'state.next_appointment' => 'next appointment',
        'state.encounter_type' => 'encounter type',
        'state.doctor_notes' => 'doctor notes',
        'state.provider_notes' => 'provider notes',
        'state.philhealth_number' => 'PhilHealth number',
        'state.vital_signs' => 'vital signs',
    ];

    public function mount(?MedicalRecord $record = null)
    {
        $this->record = $record;
        $this->state = $record ? $record->toArray() : [];
        // If the page was opened from a patient profile (e.g. ?patient=123), preselect that patient
        $patientId = request()->query('patient');
        if (! $this->record && $patientId) {
            $this->state['patient_id'] = (int) $patientId;
        }

        // Default consultation date to today when creating
        if (empty($this->state['consultation_date'])) {
            $this->state['consultation_date'] = now()->format('Y-m-d');
        }
    }

    public function save()
    {
        $rules = $this->rules;
        $this->validate($rules);

        $user = Auth::user();

        // If the user is a delegate, force the clinic to their assigned clinic
        if ($user->hasRole('delegate')) {
            $this->state['clinic_id'] = $user->clinic_id;
        }

        if ($this->record) {
            $this->record->update($this->state);
            session()->flash('message', 'Medical record updated successfully.');
        } else {
            $this->record = MedicalRecord::create(array_merge($this->state, ['user_id' => $user->id]));
            session()->flash('message', 'Medical record created successfully.');
        }

        return redirect()->route('medical-records.index');
    }

    public function render()
    {
        $patients = Patient::orderBy('last_name')->get();
        $user = Auth::user();

        if ($user->hasRole('delegate')) {
            $clinics = Clinic::where('id', $user->clinic_id)->orderBy('name')->get();
        } else {
            $clinics = Clinic::orderBy('name')->get();
        }

        return view('livewire.medical-records.form', [
            'patients' => $patients,
            'clinics' => $clinics,
        ]);
    }

    public function delete(?int $id = null)
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
