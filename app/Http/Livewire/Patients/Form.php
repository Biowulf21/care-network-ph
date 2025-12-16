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
        'state.date_of_birth' => 'required|date',
        'state.sex' => 'required|string',
        'state.phone' => 'required|string|max:50',
        'state.email' => 'nullable|email|max:255',
        'state.address' => 'required|string',
        'state.city' => 'nullable|string|max:255',
        'state.province' => 'nullable|string|max:255',
        'state.zip_code' => 'nullable|string|max:20',
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

        // Ensure date_of_birth is prefilled in `Y-m-d` format for <input type="date"> elements
        if (! empty($this->state['date_of_birth'])) {
            $this->state['date_of_birth'] = optional($patient->date_of_birth)->format('Y-m-d');
        }

        // Backwards-compat: if `sex` is empty but `gender` is present, prefer `gender` value
        if (empty($this->state['sex']) && ! empty($this->state['gender'])) {
            $this->state['sex'] = $this->state['gender'];
        }

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
        $this->existingPhotoUrl = null;
        if ($this->patient && ! empty($this->patient->photo)) {
            $photoPath = $this->patient->photo;
            // prefer public disk URL if file exists there
            if (Storage::disk('public')->exists($photoPath)) {
                $this->existingPhotoUrl = Storage::disk('public')->url($photoPath);
            } else {
                // fallback to default Storage::url (handles other disks)
                $this->existingPhotoUrl = Storage::url($photoPath);
            }
        }
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
