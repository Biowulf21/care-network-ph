<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public Patient $patient;

    public $activeTab = 'overview';
    public $selectedRecordId = null;

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

    public function viewRecord(int $id)
    {
        $this->selectedRecordId = $id;
        // ensure tab switches to history when viewing a specific record
        $this->activeTab = 'history';
    }

    public function getSelectedRecordProperty()
    {
        return $this->selectedRecordId ? $this->patient->medicalRecords()->with('user','clinic','prescriptions.items')->find($this->selectedRecordId) : null;
    }

    /**
     * Return prescriptions via the relation query to avoid attribute/relation name collision
     * (MedicalRecord has a casted `prescriptions` attribute in the model).
     */
    public function getSelectedRecordPrescriptionsProperty()
    {
        if (! $this->selectedRecordId) {
            return collect();
        }

        $record = $this->patient->medicalRecords()->find($this->selectedRecordId);

        if (! $record) {
            return collect();
        }

        return $record->prescriptions()->with('items')->get();
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
            ->get();
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
