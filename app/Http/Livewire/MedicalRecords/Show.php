<?php

namespace App\Http\Livewire\MedicalRecords;

use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Show extends Component
{
    public MedicalRecord $record;

    public function mount(MedicalRecord $record)
    {
        if (Gate::denies('view', $record)) {
            abort(403);
        }

        $this->record = $record;
    }

    public function render()
    {
        return view('livewire.medical-records.show', ['record' => $this->record]);
    }
}
