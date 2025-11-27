<?php

namespace App\Livewire\Clinics;

use App\Models\Clinic;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Form extends Component
{
    public $clinic;

    public $name = '';

    public $organization_id = '';

    public $editing = false;

    public function mount($clinic = null)
    {
        $currentUser = Auth::user();

        if (! $currentUser->hasRole('superadmin') && ! $currentUser->hasRole('admin')) {
            abort(403);
        }

        if ($clinic) {
            $this->clinic = $clinic;
            $this->editing = true;
            $this->name = $clinic->name;
            $this->organization_id = $clinic->organization_id;

            // Admin can only edit clinics in their organization
            if ($currentUser->hasRole('admin') && $clinic->organization_id !== $currentUser->organization_id) {
                abort(403);
            }
        } else {
            // For admin, pre-fill organization
            if ($currentUser->hasRole('admin')) {
                $this->organization_id = $currentUser->organization_id;
            }
        }
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'organization_id' => ['required', 'exists:organizations,id'],
        ];
    }

    public function save()
    {
        $this->validate();

        $currentUser = Auth::user();

        // Admin can only create/edit clinics in their organization
        if ($currentUser->hasRole('admin') && $this->organization_id !== $currentUser->organization_id) {
            abort(403);
        }

        if ($this->editing) {
            $this->clinic->update([
                'name' => $this->name,
                'organization_id' => $this->organization_id,
            ]);

            session()->flash('message', 'Clinic updated successfully.');
        } else {
            Clinic::create([
                'name' => $this->name,
                'organization_id' => $this->organization_id,
            ]);

            session()->flash('message', 'Clinic created successfully.');
        }

        return redirect()->route('clinics.index');
    }

    public function render()
    {
        $currentUser = Auth::user();

        $organizations = collect();

        if ($currentUser->hasRole('superadmin')) {
            $organizations = Organization::orderBy('name')->get();
        } elseif ($currentUser->hasRole('admin')) {
            $organizations = Organization::where('id', $currentUser->organization_id)->get();
        }

        return view('livewire.clinics.form', [
            'organizations' => $organizations,
        ]);
    }
}
