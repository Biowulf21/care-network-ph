<?php

namespace App\Livewire\Search;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\MedicalRecord;
use App\Models\Organization;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GlobalSearch extends Component
{
    public $query = '';

    public $showResults = false;

    public $results = [];

    public function updatedQuery()
    {
        $this->showResults = strlen($this->query) > 2;

        if ($this->showResults) {
            $this->search();
        } else {
            $this->results = [];
        }
    }

    public function search()
    {
        $user = Auth::user();
        $searchTerm = '%'.$this->query.'%';

        $this->results = [
            'patients' => [],
            'appointments' => [],
            'medical_records' => [],
            'users' => [],
            'organizations' => [],
            'clinics' => [],
        ];

        // Search Patients
        $patientsQuery = Patient::where(function ($q) use ($searchTerm) {
            $q->where('first_name', 'like', $searchTerm)
                ->orWhere('last_name', 'like', $searchTerm)
                ->orWhere('patient_id', 'like', $searchTerm)
                ->orWhere('email', 'like', $searchTerm)
                ->orWhere('phone_number', 'like', $searchTerm);
        });

        // Apply role-based filtering for patients
        if ($user->hasRole('delegate')) {
            $patientsQuery->whereHas('medicalRecords', function ($q) use ($user) {
                $q->whereHas('clinic', function ($clinicQ) use ($user) {
                    $clinicQ->where('id', $user->clinic_id);
                });
            });
        } elseif ($user->hasRole('admin')) {
            $patientsQuery->whereHas('medicalRecords', function ($q) use ($user) {
                $q->whereHas('clinic', function ($clinicQ) use ($user) {
                    $clinicQ->where('organization_id', $user->organization_id);
                });
            });
        }

        $this->results['patients'] = $patientsQuery->limit(5)->get();

        // Search Appointments
        $appointmentsQuery = Appointment::with(['patient', 'clinic'])
            ->where(function ($q) use ($searchTerm) {
                $q->where('appointment_type', 'like', $searchTerm)
                    ->orWhere('specialty', 'like', $searchTerm)
                    ->orWhere('notes', 'like', $searchTerm)
                    ->orWhereHas('patient', function ($patientQ) use ($searchTerm) {
                        $patientQ->where('first_name', 'like', $searchTerm)
                            ->orWhere('last_name', 'like', $searchTerm);
                    });
            });

        // Apply role-based filtering for appointments
        if ($user->hasRole('delegate')) {
            $appointmentsQuery->where('clinic_id', $user->clinic_id);
        } elseif ($user->hasRole('admin')) {
            $appointmentsQuery->whereHas('clinic', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            });
        }

        $this->results['appointments'] = $appointmentsQuery->limit(5)->get();

        // Search Medical Records
        $recordsQuery = MedicalRecord::with(['patient', 'clinic'])
            ->where(function ($q) use ($searchTerm) {
                $q->where('diagnosis', 'like', $searchTerm)
                    ->orWhere('chief_complaint', 'like', $searchTerm)
                    ->orWhere('doctor_notes', 'like', $searchTerm)
                    ->orWhere('treatment_plan', 'like', $searchTerm);
            });

        // Apply role-based filtering for medical records
        if ($user->hasRole('delegate')) {
            $recordsQuery->where('clinic_id', $user->clinic_id);
        } elseif ($user->hasRole('admin')) {
            $recordsQuery->whereHas('clinic', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            });
        }

        $this->results['medical_records'] = $recordsQuery->limit(5)->get();

        // Search Users (for admin and superadmin)
        if ($user->hasRole('admin') || $user->hasRole('superadmin')) {
            $usersQuery = User::where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm);
            });

            if ($user->hasRole('admin')) {
                $usersQuery->where('organization_id', $user->organization_id);
            }

            $this->results['users'] = $usersQuery->limit(5)->get();
        }

        // Search Organizations (for superadmin only)
        if ($user->hasRole('superadmin')) {
            $this->results['organizations'] = Organization::where('name', 'like', $searchTerm)
                ->limit(5)->get();
        }

        // Search Clinics
        $clinicsQuery = Clinic::where('name', 'like', $searchTerm);

        if ($user->hasRole('admin')) {
            $clinicsQuery->where('organization_id', $user->organization_id);
        } elseif ($user->hasRole('delegate')) {
            $clinicsQuery->where('id', $user->clinic_id);
        }

        $this->results['clinics'] = $clinicsQuery->limit(5)->get();
    }

    public function closeResults()
    {
        $this->showResults = false;
        $this->results = [];
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->showResults = false;
        $this->results = [];
    }

    public function render()
    {
        return view('livewire.search.global-search');
    }
}
