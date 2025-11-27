<?php

namespace App\Http\Livewire\Patients;

use App\Models\Patient;
use App\Models\Clinic;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Form extends Component
{
    public ?Patient $patient = null;

    public $state = [];

    public $clinics = [];

    protected $listeners = ['deletePatient' => 'delete'];

    public function mount(?Patient $patient = null)
    {
        $this->patient = $patient;
        $this->state = $patient ? $patient->toArray() : [];

        $user = Auth::user();
        // Provide clinics list for admin / superadmin so they can pick where patient belongs
        if ($user->hasRole('superadmin')) {
            $this->clinics = Clinic::orderBy('name')->get()->toArray();
        } elseif ($user->hasRole('admin')) {
            $this->clinics = Clinic::where('organization_id', $user->organization_id)->orderBy('name')->get()->toArray();
        } else {
            $this->clinics = [];
        }
    }

    public function save()
    {
        $user = Auth::user();
        $rules = [
            'state.first_name' => 'required|string|max:255',
            'state.last_name' => 'required|string|max:255',
            'state.middle_name' => 'nullable|string|max:255',
            'state.date_of_birth' => 'nullable|date',
            'state.sex' => 'nullable|string',
            'state.gender' => 'nullable|string',
            'state.phone' => 'nullable|string|max:50',
            'state.email' => 'nullable|email|max:255',
            'state.address' => 'nullable|string',
            'state.city' => 'nullable|string|max:255',
            'state.province' => 'nullable|string|max:255',
            'state.zip_code' => 'nullable|string|max:20',
            'state.blood_type' => 'nullable|string|max:5',
            'state.height' => 'nullable|numeric',
            'state.weight' => 'nullable|numeric',
            'state.philhealth_number' => 'nullable|string|max:100',
        ];

        // Non-delegates must provide a clinic_id when creating/updating
        if (! $user->hasRole('delegate')) {
            $rules['state.clinic_id'] = 'required|exists:clinics,id';
        }

        $this->validate($rules);

        $payload = $this->state;

        // If delegate, force their clinic regardless of submitted data
        if ($user->hasRole('delegate')) {
            $payload['clinic_id'] = $user->clinic_id;
        } else {
            // If editing an existing patient and no clinic supplied, preserve existing
            if (empty($payload['clinic_id']) && $this->patient && ! empty($this->patient->clinic_id)) {
                $payload['clinic_id'] = $this->patient->clinic_id;
            }
        }

        if ($this->patient) {
            $this->patient->update($payload);
            session()->flash('message', 'Patient updated successfully.');
        } else {
            $this->patient = Patient::create($payload);
            session()->flash('message', 'Patient created successfully.');
        }

        return redirect()->route('patients.index');
    }

    public function render()
    {
        return view('livewire.patients.form');
    }

    public function delete(?int $id = null)
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
