<?php

namespace App\Livewire\Appointments;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $dateFilter = '';

    protected $listeners = ['deleteAppointment' => 'delete'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $query = Appointment::with(['patient', 'clinic', 'user'])->orderBy('appointment_date', 'desc')->orderBy('appointment_time', 'desc');

        // Role-based scoping
        if ($user->hasRole('admin')) {
            $query->whereHas('clinic', fn ($q) => $q->where('organization_id', $user->organization_id));
        }

        if ($user->hasRole('delegate')) {
            $query->where('clinic_id', $user->clinic_id);
        }

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('patient', function ($pq) {
                    $pq->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%");
                })->orWhere('notes', 'like', "%{$this->search}%");
            });
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Date filter
        if ($this->dateFilter) {
            $query->whereDate('appointment_date', $this->dateFilter);
        }

        return view('livewire.appointments.index', ['appointments' => $query->paginate(15)]);
    }

    public function delete(int $id)
    {
        $appointment = Appointment::findOrFail($id);

        if (Gate::denies('delete', $appointment)) {
            $this->dispatchBrowserEvent('notify', ['type' => 'error', 'message' => 'Not authorized']);

            return;
        }

        $appointment->delete();
        $this->resetPage();
        $this->dispatchBrowserEvent('notify', ['type' => 'success', 'message' => 'Appointment deleted']);
    }
}
