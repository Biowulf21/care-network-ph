<?php

namespace App\Livewire\Users;

use App\Models\Clinic;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Form extends Component
{
    public $user;

    public $name = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public $role_id = '';

    public $organization_id = '';

    public $clinic_id = '';

    public $editing = false;

    public function mount($user = null)
    {
        $currentUser = Auth::user();

        if (! $currentUser->hasRole('superadmin') && ! $currentUser->hasRole('admin')) {
            abort(403);
        }

        if ($user) {
            $this->user = $user;
            $this->editing = true;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role_id = $user->roles()->first()?->id ?? '';
            $this->organization_id = $user->organization_id ?? '';
            $this->clinic_id = $user->clinic_id ?? '';

            // Admin can only edit delegates in their organization
            if ($currentUser->hasRole('admin')) {
                if ($user->organization_id !== $currentUser->organization_id || ! $user->hasRole('delegate')) {
                    abort(403);
                }
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
            'email' => ['required', 'email', 'max:255', 'unique:users,email'.($this->editing ? ','.$this->user->id : '')],
            'password' => $this->editing ? ['nullable', 'string', Password::defaults()] : ['required', 'string', Password::defaults()],
            'password_confirmation' => ['same:password'],
            'role_id' => ['required', 'exists:roles,id'],
            'organization_id' => ['required', 'exists:organizations,id'],
            'clinic_id' => ['required', 'exists:clinics,id'],
        ];
    }

    public function save()
    {
        $this->validate();

        $currentUser = Auth::user();

        if ($this->editing) {
            $this->user->update([
                'name' => $this->name,
                'email' => $this->email,
                'organization_id' => $this->organization_id,
                'clinic_id' => $this->clinic_id,
            ]);

            if ($this->password) {
                $this->user->update(['password' => Hash::make($this->password)]);
            }

            $this->user->roles()->sync([$this->role_id]);

            session()->flash('message', 'User updated successfully.');
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'organization_id' => $this->organization_id,
                'clinic_id' => $this->clinic_id,
            ]);

            $user->roles()->sync([$this->role_id]);

            session()->flash('message', 'User created successfully.');
        }

        return redirect()->route('users.index');
    }

    public function updatedOrganizationId()
    {
        $this->clinic_id = '';
    }

    public function render()
    {
        $currentUser = Auth::user();

        $organizations = collect();
        $roles = collect();
        $clinics = collect();

        if ($currentUser->hasRole('superadmin')) {
            $organizations = Organization::orderBy('name')->get();
            $roles = Role::all();
        } elseif ($currentUser->hasRole('admin')) {
            $organizations = Organization::where('id', $currentUser->organization_id)->get();
            $roles = Role::where('name', 'delegate')->get();
        }

        if ($this->organization_id) {
            $clinics = Clinic::where('organization_id', $this->organization_id)->orderBy('name')->get();
        }

        return view('livewire.users.form', [
            'organizations' => $organizations,
            'roles' => $roles,
            'clinics' => $clinics,
        ]);
    }
}
