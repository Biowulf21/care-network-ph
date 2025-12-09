<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Admin extends Component
{
    public function render()
    {
        $user = Auth::user();
        $clinics = $user->organization_id ? Clinic::where('organization_id', $user->organization_id)->get() : collect();
        $clinicIds = $clinics->pluck('id');

        // today's traffic per clinic
        $traffic = [];
        foreach ($clinics as $clinic) {
            $traffic[] = [
                'clinic' => $clinic->name,
                'count' => Patient::whereDate('created_at', today())->where('clinic_id', $clinic->id)->count(),
            ];
        }

        // emar completion per clinic
        $completion = [];
        foreach ($clinics as $clinic) {
            $total = MedicalRecord::where('clinic_id', $clinic->id)->count();
            $done = MedicalRecord::where('clinic_id', $clinic->id)->whereNotNull('doctor_notes')->count();
            $completion[] = [
                'clinic' => $clinic->name,
                'rate' => $total ? round(($done / $total) * 100, 2) : 0,
            ];
        }

        // Gender distribution
        $patients = Patient::whereHas('medicalRecords', function ($q) use ($clinicIds) {
            $q->whereIn('clinic_id', $clinicIds);
        })->get();
        $genderDistribution = $patients->groupBy('gender')->map->count();

        // New patient registrations (last 30 days)
        $newPatientRegistrations = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $newPatientRegistrations[$date] = Patient::whereHas('medicalRecords', function ($q) use ($clinicIds) {
                $q->whereIn('clinic_id', $clinicIds);
            })->whereDate('created_at', $date)->count();
        }

        // Appointment status (last 30 days)
        $appointmentStatus = Appointment::whereIn('clinic_id', $clinicIds)
            ->whereBetween('appointment_date', [now()->subDays(30), now()])
            ->get()
            ->groupBy('status')
            ->map->count();

        // Monthly appointment trends (last 6 months)
        $monthlyAppointments = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthlyAppointments[$month] = Appointment::whereIn('clinic_id', $clinicIds)
                ->whereYear('appointment_date', now()->subMonths($i)->year)
                ->whereMonth('appointment_date', now()->subMonths($i)->month)
                ->count();
        }

        // Monthly consultations (last 6 months)
        $monthlyConsultations = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthlyConsultations[$month] = MedicalRecord::whereIn('clinic_id', $clinicIds)
                ->whereYear('consultation_date', now()->subMonths($i)->year)
                ->whereMonth('consultation_date', now()->subMonths($i)->month)
                ->count();
        }

        return view('livewire.dashboard.admin', [
            'clinics' => $clinics,
            'todayPatientTraffic' => collect($traffic),
            'emarCompletionRate' => collect($completion),
            'genderDistribution' => $genderDistribution,
            'newPatientRegistrations' => $newPatientRegistrations,
            'appointmentStatus' => $appointmentStatus,
            'monthlyAppointments' => $monthlyAppointments,
            'monthlyConsultations' => $monthlyConsultations,
        ]);
    }

    protected function calculateEmarCompletion($clinics)
    {
        $clinicIds = $clinics->pluck('id');
        $total = MedicalRecord::whereIn('clinic_id', $clinicIds)->count();
        $completed = MedicalRecord::whereIn('clinic_id', $clinicIds)->whereNotNull('doctor_notes')->count();

        if ($total === 0) {
            return 0;
        }

        return round(($completed / $total) * 100, 2);
    }
}
