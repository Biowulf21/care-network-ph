<?php

namespace App\Http\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Only superadmin or admin (for their org's delegates) can delete
        $currentUser = Auth::user();
        if ($currentUser->hasRole('admin')) {
            if ($user->organization_id !== $currentUser->organization_id || ! $user->hasRole('delegate')) {
                abort(403);
            }
        } elseif (! $currentUser->hasRole('superadmin')) {
            abort(403);
        }

        $user->delete();
        session()->flash('message', 'User deleted successfully.');
    }

    public function render()
    {
        $user = Auth::user();

        if (! $user->hasRole('superadmin') && ! $user->hasRole('admin')) {
            abort(403);
        }

        $query = User::query();

        if ($user->hasRole('admin')) {
            // admins only see users in their organization (delegates)
            $query->where('organization_id', $user->organization_id)
                ->whereHas('roles', fn ($q) => $q->where('name', 'delegate'));
        }
        // superadmin sees all users

        $users = $query->orderBy('name')->paginate(20);

        return view('livewire.users.index', ['users' => $users]);
    }
}
