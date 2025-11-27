<?php

namespace App\Http\Livewire\Clinics;

use App\Models\Clinic;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function deleteClinic($id)
    {
        $user = Auth::user();

        if (! $user->hasRole('superadmin') && ! $user->hasRole('admin')) {
            abort(403);
        }

        $clinic = Clinic::findOrFail($id);

        // Admin can only delete clinics in their organization
        if ($user->hasRole('admin') && $clinic->organization_id !== $user->organization_id) {
            abort(403);
        }

        $clinic->delete();
        session()->flash('message', 'Clinic deleted successfully.');
    }

    public function render()
    {
        $user = Auth::user();

        $query = Clinic::query();

        if ($user->hasRole('admin')) {
            $query->where('organization_id', $user->organization_id);
        }

        $clinics = $query->orderBy('name')->paginate(20);

        return view('livewire.clinics.index', ['clinics' => $clinics]);
    }
}
