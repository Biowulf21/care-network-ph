<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\MedicalRecord;

class Admin extends Component
{
    public function render()
    {
        $user = Auth::user();
        $clinics = $user->organization_id ? Clinic::where('organization_id', $user->organization_id)->get() : collect();

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

        return view('livewire.dashboard.admin', [
            'clinics' => $clinics,
            'todayPatientTraffic' => collect($traffic),
            'emarCompletionRate' => collect($completion),
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
