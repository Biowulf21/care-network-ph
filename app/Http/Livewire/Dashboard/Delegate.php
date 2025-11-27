<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Clinic;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Delegate extends Component
{
    public function render()
    {
        $user = Auth::user();
        $clinic = $user->clinic_id ? Clinic::find($user->clinic_id) : null;

        $patientsCount = $clinic ? Patient::where('clinic_id', $clinic->id)->count() : 0;
        $emarCount = $clinic ? MedicalRecord::where('clinic_id', $clinic->id)->count() : 0;
        $myAssigned = MedicalRecord::where('user_id', $user->id)->latest()->limit(10)->get();

        return view('livewire.dashboard.delegate', [
            'clinic' => $clinic,
            'patientsCount' => $patientsCount,
            'emarCount' => $emarCount,
            'myAssigned' => $myAssigned,
        ]);
    }
}
