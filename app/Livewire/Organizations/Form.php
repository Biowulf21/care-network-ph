<?php

namespace App\Livewire\Organizations;

use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Form extends Component
{
    public $organization;

    public $name = '';

    public $editing = false;

    public function mount($organization = null)
    {
        // Only superadmin can manage organizations
        if (! Auth::user()->hasRole('superadmin')) {
            abort(403);
        }

        if ($organization) {
            $this->organization = $organization;
            $this->editing = true;
            $this->name = $organization->name;
        }
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:organizations,name'.($this->editing ? ','.$this->organization->id : '')],
        ];
    }

    public function save()
    {
        $this->validate();

        if ($this->editing) {
            $this->organization->update([
                'name' => $this->name,
            ]);

            session()->flash('message', 'Organization updated successfully.');
        } else {
            Organization::create([
                'name' => $this->name,
            ]);

            session()->flash('message', 'Organization created successfully.');
        }

        return redirect()->route('organizations.index');
    }

    public function render()
    {
        return view('livewire.organizations.form');
    }
}
