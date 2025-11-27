<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Clinic;
use App\Models\MedicalRecord;
use App\Models\Organization;
use App\Models\Patient;
use App\Models\User;
use Livewire\Component;

class Superadmin extends Component
{
    public function render()
    {
        // daily patient intake for last 14 days
        $labels = [];
        $counts = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[] = $date;
            $counts[] = Patient::whereDate('created_at', $date)->count();
        }

        // EMAR completeness: percentage of records with doctor_notes
        $totalEmar = MedicalRecord::count();
        $completedEmar = MedicalRecord::whereNotNull('doctor_notes')->count();
        $emarCompletion = $totalEmar ? round(($completedEmar / $totalEmar) * 100, 2) : 0;

        // Claims overview
        $claims = MedicalRecord::query()->pluck('philhealth')->filter()->map(fn ($p) => $p['claim_status'] ?? 'pending')->countBy()->toArray();

        return view('livewire.dashboard.superadmin', [
            'totalOrganizations' => Organization::count(),
            'totalClinics' => Clinic::count(),
            'totalUsers' => User::count(),
            'todayPatientIntake' => Patient::whereDate('created_at', today())->count(),
            'emarCount' => $totalEmar,
            'labels' => $labels,
            'counts' => $counts,
            'emarCompletion' => $emarCompletion,
            'claims' => $claims,
        ]);
    }
}
