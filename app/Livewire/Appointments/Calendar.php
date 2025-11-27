<?php

namespace App\Livewire\Appointments;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Calendar extends Component
{
    public $currentDate;

    public $selectedDate;

    public $showModal = false;

    public $editingAppointment = null;

    public $viewType = 'month'; // month, week, day

    public $selectedClinic = null;

    // Form fields
    public $patient_id;

    public $appointment_date;

    public $appointment_time;

    public $appointment_type = 'General Consultation';

    public $specialty;

    public $notes;

    public $duration = 30;

    public $status = 'scheduled';

    protected $listeners = ['refreshCalendar' => '$refresh'];

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->selectedDate = Carbon::now()->format('Y-m-d');
        $this->appointment_date = $this->selectedDate;
        $this->appointment_time = Carbon::now()->addHour()->format('H:i');
    }

    public function updatedSelectedDate($value)
    {
        $this->appointment_date = $value;
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->appointment_date = $date;
    }

    public function previousPeriod()
    {
        switch ($this->viewType) {
            case 'month':
                $this->currentDate = $this->currentDate->subMonth();
                break;
            case 'week':
                $this->currentDate = $this->currentDate->subWeek();
                break;
            case 'day':
                $this->currentDate = $this->currentDate->subDay();
                break;
        }
    }

    public function nextPeriod()
    {
        switch ($this->viewType) {
            case 'month':
                $this->currentDate = $this->currentDate->addMonth();
                break;
            case 'week':
                $this->currentDate = $this->currentDate->addWeek();
                break;
            case 'day':
                $this->currentDate = $this->currentDate->addDay();
                break;
        }
    }

    public function setViewType($type)
    {
        $this->viewType = $type;
    }

    public function openModal($appointmentId = null)
    {
        if ($appointmentId) {
            $this->editingAppointment = Appointment::find($appointmentId);
            $this->patient_id = $this->editingAppointment->patient_id;
            $this->appointment_date = $this->editingAppointment->appointment_date->format('Y-m-d');
            $this->appointment_time = $this->editingAppointment->appointment_time->format('H:i');
            $this->appointment_type = $this->editingAppointment->appointment_type;
            $this->specialty = $this->editingAppointment->specialty;
            $this->notes = $this->editingAppointment->notes;
            $this->duration = $this->editingAppointment->duration;
            $this->status = $this->editingAppointment->status;
        } else {
            $this->resetForm();
        }
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingAppointment = null;
        $this->patient_id = '';
        $this->appointment_date = $this->selectedDate;
        $this->appointment_time = Carbon::now()->addHour()->format('H:i');
        $this->appointment_type = 'General Consultation';
        $this->specialty = '';
        $this->notes = '';
        $this->duration = 30;
        $this->status = 'scheduled';
    }

    public function save()
    {
        $this->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'appointment_type' => 'required|string',
            'duration' => 'required|integer|min:15|max:180',
            'status' => 'required|in:scheduled,confirmed,cancelled,completed,no-show',
        ]);

        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            $clinic = $this->selectedClinic ? Clinic::find($this->selectedClinic) : Clinic::first();
        } elseif ($user->hasRole('admin')) {
            $clinic = $user->organization->clinics->first();
        } else {
            $clinic = $user->clinic;
        }

        $data = [
            'patient_id' => $this->patient_id,
            'clinic_id' => $clinic->id,
            'user_id' => $user->id,
            'appointment_date' => $this->appointment_date,
            'appointment_time' => $this->appointment_time,
            'appointment_type' => $this->appointment_type,
            'specialty' => $this->specialty,
            'notes' => $this->notes,
            'duration' => $this->duration,
            'status' => $this->status,
        ];

        if ($this->editingAppointment) {
            $this->editingAppointment->update($data);
            session()->flash('message', 'Appointment updated successfully.');
        } else {
            Appointment::create($data);
            session()->flash('message', 'Appointment created successfully.');
        }

        $this->closeModal();
        $this->dispatch('refreshCalendar');
    }

    public function updateStatus($appointmentId, $status)
    {
        $appointment = Appointment::find($appointmentId);
        if ($appointment) {
            $appointment->update(['status' => $status]);
            session()->flash('message', 'Appointment status updated.');
        }
    }

    public function deleteAppointment($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);
        if ($appointment) {
            $appointment->delete();
            session()->flash('message', 'Appointment deleted.');
        }
    }

    public function getAppointmentsForPeriod()
    {
        $user = Auth::user();
        $query = Appointment::with(['patient', 'clinic', 'user']);

        // Apply role-based filtering
        if ($user && $user->hasRole('delegate')) {
            $query->where('clinic_id', $user->clinic_id);
        } elseif ($user && $user->hasRole('admin')) {
            $query->whereHas('clinic', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            });
        }

        if ($this->selectedClinic && $user && $user->hasRole('superadmin')) {
            $query->where('clinic_id', $this->selectedClinic);
        }

        switch ($this->viewType) {
            case 'month':
                $startOfMonth = $this->currentDate->copy()->startOfMonth();
                $endOfMonth = $this->currentDate->copy()->endOfMonth();

                return $query->whereBetween('appointment_date', [$startOfMonth, $endOfMonth])->get();
            case 'week':
                $startOfWeek = $this->currentDate->copy()->startOfWeek();
                $endOfWeek = $this->currentDate->copy()->endOfWeek();

                return $query->whereBetween('appointment_date', [$startOfWeek, $endOfWeek])->get();
            case 'day':
                return $query->whereDate('appointment_date', $this->currentDate)->get();
        }
    }

    public function getCalendarDays()
    {
        $startOfMonth = $this->currentDate->copy()->startOfMonth();
        $endOfMonth = $this->currentDate->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek();
        $endOfCalendar = $endOfMonth->copy()->endOfWeek();

        $days = [];
        $current = $startOfCalendar->copy();

        while ($current <= $endOfCalendar) {
            $days[] = $current->copy();
            $current->addDay();
        }

        return $days;
    }

    public function render()
    {
        $appointments = $this->getAppointmentsForPeriod();
        $calendarDays = $this->getCalendarDays();
        $patients = Patient::orderBy('last_name')->get();
        $clinics = Auth::user()->hasRole('superadmin') ? Clinic::all() : [];

        // Group appointments by date for easy lookup
        $appointmentsByDate = $appointments->groupBy(function ($appointment) {
            return $appointment->appointment_date->format('Y-m-d');
        });

        return view('livewire.appointments.calendar', [
            'appointments' => $appointments,
            'calendarDays' => $calendarDays,
            'patients' => $patients,
            'clinics' => $clinics,
            'appointmentsByDate' => $appointmentsByDate,
        ]);
    }
}
