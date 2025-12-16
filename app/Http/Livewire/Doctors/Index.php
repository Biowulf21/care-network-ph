<?php

namespace App\Http\Livewire\Doctors;

use App\Models\Doctor;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $doctors = Doctor::with('clinic')->orderBy('name')->get();

        return view('livewire.doctors.index', [
            'doctors' => $doctors,
        ]);
    }
}
