<?php

namespace App\Livewire\Reports;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\MedicalRecord;
use App\Models\Organization;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Analytics extends Component
{
    public $dateRange = '30'; // Default to last 30 days

    public $selectedClinic = null;

    public $selectedOrganization = null;

    public $reportType = 'overview'; // overview, patients, appointments, medical_records

    public function mount()
    {
        $user = Auth::user();

        if ($user->hasRole('delegate')) {
            $this->selectedClinic = $user->clinic_id;
        } elseif ($user->hasRole('admin')) {
            $this->selectedOrganization = $user->organization_id;
        }
    }

    public function setReportType($type)
    {
        $this->reportType = $type;
    }

    public function getDateRangeProperty()
    {
        $endDate = Carbon::now();
        $startDate = match ($this->dateRange) {
            '7' => $endDate->copy()->subDays(7),
            '30' => $endDate->copy()->subDays(30),
            '90' => $endDate->copy()->subDays(90),
            '365' => $endDate->copy()->subDays(365),
            'ytd' => $endDate->copy()->startOfYear(),
            default => $endDate->copy()->subDays(30),
        };

        return [$startDate, $endDate];
    }

    public function getOverviewStats()
    {
        [$startDate, $endDate] = $this->getDateRangeProperty();
        $user = Auth::user();

        // Base queries
        $patientsQuery = Patient::query();
        $appointmentsQuery = Appointment::query();
        $recordsQuery = MedicalRecord::query();

        // Apply role-based filtering
        if ($user->hasRole('delegate')) {
            $appointmentsQuery->where('clinic_id', $user->clinic_id);
            $recordsQuery->where('clinic_id', $user->clinic_id);
            $patientsQuery->whereHas('medicalRecords', function ($q) use ($user) {
                $q->where('clinic_id', $user->clinic_id);
            });
        } elseif ($user->hasRole('admin')) {
            $organizationClinicIds = Clinic::where('organization_id', $user->organization_id)->pluck('id');
            $appointmentsQuery->whereIn('clinic_id', $organizationClinicIds);
            $recordsQuery->whereIn('clinic_id', $organizationClinicIds);
            $patientsQuery->whereHas('medicalRecords', function ($q) use ($organizationClinicIds) {
                $q->whereIn('clinic_id', $organizationClinicIds);
            });
        } elseif ($user->hasRole('superadmin')) {
            if ($this->selectedClinic) {
                $appointmentsQuery->where('clinic_id', $this->selectedClinic);
                $recordsQuery->where('clinic_id', $this->selectedClinic);
            } elseif ($this->selectedOrganization) {
                $organizationClinicIds = Clinic::where('organization_id', $this->selectedOrganization)->pluck('id');
                $appointmentsQuery->whereIn('clinic_id', $organizationClinicIds);
                $recordsQuery->whereIn('clinic_id', $organizationClinicIds);
            }
        }

        return [
            'total_patients' => $patientsQuery->count(),
            'new_patients' => $patientsQuery->whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_appointments' => $appointmentsQuery->count(),
            'appointments_period' => $appointmentsQuery->whereBetween('appointment_date', [$startDate, $endDate])->count(),
            'total_records' => $recordsQuery->count(),
            'records_period' => $recordsQuery->whereBetween('consultation_date', [$startDate, $endDate])->count(),
            'completed_appointments' => $appointmentsQuery->where('status', 'completed')->whereBetween('appointment_date', [$startDate, $endDate])->count(),
            'cancelled_appointments' => $appointmentsQuery->where('status', 'cancelled')->whereBetween('appointment_date', [$startDate, $endDate])->count(),
        ];
    }

    public function getAppointmentStats()
    {
        [$startDate, $endDate] = $this->getDateRangeProperty();
        $user = Auth::user();

        $query = Appointment::with(['patient', 'clinic']);

        // Apply role-based filtering
        if ($user->hasRole('delegate')) {
            $query->where('clinic_id', $user->clinic_id);
        } elseif ($user->hasRole('admin')) {
            $organizationClinicIds = Clinic::where('organization_id', $user->organization_id)->pluck('id');
            $query->whereIn('clinic_id', $organizationClinicIds);
        }

        $appointments = $query->whereBetween('appointment_date', [$startDate, $endDate])->get();

        return [
            'by_type' => $appointments->groupBy('appointment_type')->map->count(),
            'by_status' => $appointments->groupBy('status')->map->count(),
            'by_month' => $appointments->groupBy(function ($appointment) {
                return $appointment->appointment_date->format('Y-m');
            })->map->count(),
            'completion_rate' => $appointments->count() > 0 ?
                round(($appointments->where('status', 'completed')->count() / $appointments->count()) * 100, 1) : 0,
        ];
    }

    public function getPatientStats()
    {
        [$startDate, $endDate] = $this->getDateRangeProperty();
        $user = Auth::user();

        $query = Patient::query();

        // Apply role-based filtering
        if ($user->hasRole('delegate')) {
            $query->whereHas('medicalRecords', function ($q) use ($user) {
                $q->where('clinic_id', $user->clinic_id);
            });
        } elseif ($user->hasRole('admin')) {
            $query->whereHas('medicalRecords', function ($q) use ($user) {
                $q->whereHas('clinic', function ($clinicQ) use ($user) {
                    $clinicQ->where('organization_id', $user->organization_id);
                });
            });
        }

        $patients = $query->get();
        $newPatients = $query->whereBetween('created_at', [$startDate, $endDate])->get();

        return [
            'age_groups' => $patients->groupBy(function ($patient) {
                $age = $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : null;
                if (! $age) {
                    return 'Unknown';
                }
                if ($age < 18) {
                    return '0-17';
                }
                if ($age < 35) {
                    return '18-34';
                }
                if ($age < 55) {
                    return '35-54';
                }
                if ($age < 75) {
                    return '55-74';
                }

                return '75+';
            })->map->count(),
            'by_gender' => $patients->groupBy('gender')->map->count(),
            'by_blood_type' => $patients->filter(fn($p) => ! empty($p->blood_type))->groupBy('blood_type')->map->count(),
            'new_registrations' => $newPatients->groupBy(function ($patient) {
                return $patient->created_at->format('Y-m-d');
            })->map->count(),
        ];
    }

    public function getMedicalRecordStats()
    {
        [$startDate, $endDate] = $this->getDateRangeProperty();
        $user = Auth::user();

        $query = MedicalRecord::with(['patient', 'clinic']);

        // Apply role-based filtering
        if ($user->hasRole('delegate')) {
            $query->where('clinic_id', $user->clinic_id);
        } elseif ($user->hasRole('admin')) {
            $organizationClinicIds = Clinic::where('organization_id', $user->organization_id)->pluck('id');
            $query->whereIn('clinic_id', $organizationClinicIds);
        }

        $records = $query->whereBetween('consultation_date', [$startDate, $endDate])->get();

        // Common diagnoses
        $diagnoses = $records->where('diagnosis')->pluck('diagnosis')
            ->flatMap(function ($diagnosis) {
                // Split by common separators and clean up
                return collect(preg_split('/[,;]/', strtolower($diagnosis)))
                    ->map(function ($d) {
                        return trim($d);
                    })
                    ->filter(function ($d) {
                        return strlen($d) > 2;
                    });
            })
            ->countBy()
            ->sortDesc()
            ->take(10);

        return [
            'total_consultations' => $records->count(),
            'common_diagnoses' => $diagnoses,
            'records_by_month' => $records->groupBy(function ($record) {
                return $record->consultation_date->format('Y-m');
            })->map->count(),
            'average_per_day' => $records->count() > 0 ?
                round($records->count() / max(1, $startDate->diffInDays($endDate)), 1) : 0,
        ];
    }

    public function render()
    {
        $user = Auth::user();
        $overviewStats = $this->getOverviewStats();
        $appointmentStats = $this->getAppointmentStats();
        $patientStats = $this->getPatientStats();
        $medicalRecordStats = $this->getMedicalRecordStats();

        // Get available organizations and clinics for filtering
        $organizations = $user->hasRole('superadmin') ? Organization::all() : collect();
        $clinics = collect();

        if ($user->hasRole('superadmin')) {
            $clinics = $this->selectedOrganization ?
                Clinic::where('organization_id', $this->selectedOrganization)->get() :
                Clinic::all();
        } elseif ($user->hasRole('admin')) {
            $clinics = Clinic::where('organization_id', $user->organization_id)->get();
        }

        return view('livewire.reports.analytics', [
            'overviewStats' => $overviewStats,
            'appointmentStats' => $appointmentStats,
            'patientStats' => $patientStats,
            'medicalRecordStats' => $medicalRecordStats,
            'organizations' => $organizations,
            'clinics' => $clinics,
        ]);
    }

    // Defensive: some client-side code has been observed attempting to call a
    // `toJSON` method on this component (likely a Livewire proxy serialization
    // edge-case). Provide a no-op public method to avoid MethodNotFoundException
    // and log occurrences for debugging.
    public function toJSON(...$args)
    {
        \Illuminate\Support\Facades\Log::warning('Reports\Analytics component received unexpected toJSON call', ['args' => $args]);

        return ['ok' => true];
    }
}
