<?php

namespace App\Http\Livewire\Patients;

use App\Models\Patient;
use App\Models\Clinic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;
    public ?Patient $patient = null;

    public $state = [];
    public $clinics = [];

    // Photo upload
    public $photo = null;
    public $existingPhotoUrl = null;


    protected $listeners = [
        'deletePatient' => 'delete',
        'searchableSet' => 'handleSearchableSet',
    ];

    protected $rules = [
        'state.first_name' => 'required|string|max:255',
        'state.last_name' => 'required|string|max:255',
        'state.middle_name' => 'nullable|string|max:255',
        'state.date_of_birth' => 'required|nullable|date',
        'state.sex' => 'required|string',
        'state.phone' => 'required|string|max:50',
        'state.email' => 'nullable|email|max:255',
        'state.address' => 'required|string',
        'state.city' => 'required|string|max:255',
        'state.province' => 'required|string|max:255',
        'state.zip_code' => 'required|string|max:20',
        'state.blood_type' => 'nullable|string|max:5',
        'state.height' => 'nullable|numeric',
        'state.weight' => 'nullable|numeric',
        'state.philhealth_number' => 'required|string|max:100',
    ];

    protected $validationAttributes = [
        'state.first_name' => 'first name',
        'state.last_name' => 'last name',
        'state.sex' => 'sex',
        'state.address' => 'address',
        'state.city' => 'city',
        'state.province' => 'province',
        'state.zip_code' => 'zip code',
        'state.phone' => 'phone number',    
        'state.middle_name' => 'middle name',
        'state.date_of_birth' => 'date of birth',
        'state.philhealth_number' => 'PhilHealth number',
        'state.clinic_id' => 'clinic',
    ];

    public function mount(?Patient $patient = null)
    {
        $this->patient = $patient;
        $this->state = $patient ? $patient->toArray() : [];

        $user = Auth::user();
        // Provide clinics list for admin / superadmin so they can pick where patient belongs
        if ($user->hasRole('superadmin')) {
            // keep as Collection so Blade helpers like ->pluck() work
            $this->clinics = Clinic::orderBy('name')->get();
        } elseif ($user->hasRole('admin')) {
            $this->clinics = Clinic::where('organization_id', $user->organization_id)->orderBy('name')->get();
        } else {
            $this->clinics = collect([]);
        }

        // existing photo URL for preview
        $this->existingPhotoUrl = $this->patient && $this->patient->photo ? Storage::url($this->patient->photo) : null;
    }

    public function save()
    {
        $user = Auth::user();

        // Start from the component's base rules so all fields are validated
        $rules = $this->rules;

        // Non-delegates must provide a clinic_id when creating/updating
        if (! $user->hasRole('delegate')) {
            $rules['state.clinic_id'] = 'required|exists:clinics,id';
        }

        // photo validation
        $rules['photo'] = 'nullable|image|max:2048';

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

        if ($this->photo) {
            // store photo on the default disk (public)
            $payload['photo'] = $this->photo->store('patients', 'public');
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

    public function handleSearchableSet($bind, $value)
    {
        if (! $bind) {
            return;
        }

        // If binding to a key within state (e.g. "state.sex"), set the state key
        if (str_starts_with($bind, 'state.')) {
            $key = substr($bind, strlen('state.'));
            $this->state[$key] = $value;
            return;
        }

        // Fallback: set property using data_set
        data_set($this, $bind, $value);
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
