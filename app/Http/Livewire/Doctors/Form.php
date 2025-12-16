<?php

namespace App\Http\Livewire\Doctors;

use App\Models\Doctor;
use App\Models\Clinic;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Form extends Component
{
    public ?Doctor $doctor = null;

    public $state = [];

    protected $rules = [
        'state.name' => 'required|string|max:255',
        'state.clinic_id' => 'required|exists:clinics,id',
        'state.specialty' => 'nullable|string|max:255',
        'state.phone' => 'nullable|string|max:50',
        'state.email' => 'nullable|email|max:255',
    ];

    public function mount(?Doctor $doctor = null)
    {
        $this->doctor = $doctor;
        $this->state = $doctor ? $doctor->toArray() : [];
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        // Only admin or superadmin may create or transfer doctors between clinics
        if (! $user->hasRole('admin') && ! $user->hasRole('superadmin')) {
            abort(403);
        }

        if ($this->doctor) {
            $this->doctor->update($this->state);
            session()->flash('message', 'Doctor updated');
        } else {
            Doctor::create($this->state);
            session()->flash('message', 'Doctor created');
        }

        return redirect()->route('doctors.index');
    }

    public function render()
    {
        $clinics = Clinic::orderBy('name')->get();

        return view('livewire.doctors.form', [
            'clinics' => $clinics,
        ]);
    }
}
