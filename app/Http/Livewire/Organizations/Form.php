<?php

namespace App\Http\Livewire\Organizations;

use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Form extends Component
{
    public ?Organization $organization = null;

    public $state = [];

    public function mount(?Organization $organization = null)
    {
        $user = Auth::user();

        // Only superadmin may create. Admins may edit their own organization.
        if (! $organization) {
            if (! $user->hasRole('superadmin')) {
                abort(403);
            }
        } else {
            if (! $user->hasRole('superadmin') && ! ($user->hasRole('admin') && $user->organization_id === $organization->id)) {
                abort(403);
            }
        }

        $this->organization = $organization;
        $this->state = $organization ? $organization->toArray() : [];
    }

    public function save()
    {
        $user = Auth::user();

        // Only superadmin may create organizations. Admins may update their own organization.
        if (! $this->organization) {
            if (! $user->hasRole('superadmin')) {
                abort(403);
            }
        } else {
            if (! $user->hasRole('superadmin') && ! ($user->hasRole('admin') && $user->organization_id === $this->organization->id)) {
                abort(403);
            }
        }

        $rules = [
            'state.name' => 'required|string|max:255',
            'state.code' => 'nullable|string|max:50',
            'state.address' => 'nullable|string',
            'state.phone' => 'nullable|string|max:50',
        ];

        $this->validate($rules);

        $payload = $this->state;

        if ($this->organization) {
            $this->organization->update($payload);
            session()->flash('message', 'Organization updated successfully.');
        } else {
            Organization::create($payload);
            session()->flash('message', 'Organization created successfully.');
        }

        return redirect()->route('organizations.index');
    }

    public function render()
    {
        return view('livewire.organizations.form');
    }
}
