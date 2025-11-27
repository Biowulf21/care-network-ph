<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public Patient $patient;

    public $activeTab = 'overview';

    public function mount(Patient $patient)
    {
        $this->patient = $patient;

        // Authorization: users can only view patients in their clinic/organization
        $user = Auth::user();

        if ($user->hasRole('delegate')) {
            if ($patient->clinic_id !== $user->clinic_id) {
                abort(403);
            }
        } elseif ($user->hasRole('admin')) {
            if ($patient->clinic->organization_id !== $user->organization_id) {
                abort(403);
            }
        } elseif (! $user->hasRole('superadmin')) {
            abort(403);
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function getRecentRecordsProperty()
    {
        return $this->patient->medicalRecords()
            ->with('user', 'clinic')
            ->latest('consultation_date')
            ->limit(5)
            ->get();
    }

    public function getRecentVitalsProperty()
    {
        return $this->patient->medicalRecords()
            ->whereNotNull('vitals')
            ->latest('consultation_date')
            ->limit(10)
            ->get()
            ->pluck('vitals')
            ->filter();
    }

    public function getUpcomingAppointmentsProperty()
    {
        return $this->patient->appointments()
            ->with('user', 'clinic')
            ->where('appointment_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(3)
            ->get();
    }

    public function getPrescriptionHistoryProperty()
    {
        return $this->patient->medicalRecords()
            ->whereNotNull('prescriptions')
            ->latest('consultation_date')
            ->limit(10)
            ->get();
    }

    public function getVitalsChartDataProperty()
    {
        $records = $this->recentVitals;

        $dates = [];
        $weights = [];
        $temperatures = [];
        $bloodPressures = [];
        $heartRates = [];

        foreach ($records as $vitals) {
            if (isset($vitals['date'])) {
                $dates[] = $vitals['date'];
                $weights[] = $vitals['weight'] ?? null;
                $temperatures[] = $vitals['temp'] ?? null;
                $bloodPressures[] = $vitals['bp'] ?? null;
                $heartRates[] = $vitals['hr'] ?? null;
            }
        }

        return [
            'dates' => array_reverse($dates),
            'weight' => array_reverse($weights),
            'temperature' => array_reverse($temperatures),
            'bloodPressure' => array_reverse($bloodPressures),
            'heartRate' => array_reverse($heartRates),
        ];
    }

    public function render()
    {
        return view('livewire.patients.profile');
    }
}
