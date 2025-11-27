<?php

namespace App\Http\Livewire\Organizations;

use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function deleteOrganization($id)
    {
        $user = Auth::user();

        // superadmin can delete any organization; admin can delete only their own org
        if ($user->hasRole('superadmin')) {
            $organization = Organization::findOrFail($id);
        } elseif ($user->hasRole('admin')) {
            $organization = Organization::where('id', $id)->where('id', $user->organization_id)->first();
            if (! $organization) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $organization->delete();
        session()->flash('message', 'Organization deleted successfully.');
    }

    public function render()
    {
        $user = Auth::user();

        // Superadmin sees all organizations. Admins see only their own organization.
        if ($user->hasRole('superadmin')) {
            $organizations = Organization::orderBy('name')->paginate(20);
        } elseif ($user->hasRole('admin')) {
            $organizations = Organization::where('id', $user->organization_id)->orderBy('name')->paginate(20);
        } else {
            abort(403);
        }

        return view('livewire.organizations.index', ['organizations' => $organizations]);
    }
}
