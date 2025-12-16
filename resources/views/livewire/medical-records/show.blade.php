<div class="p-6 max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Medical Record — {{ $record->consultation_date?->format('M d, Y') }}</h1>
        <div class="space-x-2">
            @if($record->patient)
                <a href="{{ route('patients.profile', $record->patient) }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">Back to Patient</a>
            @else
                <a href="{{ route('medical-records.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">Back to Records</a>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Patient Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Patient</p>
                    <p class="font-medium">{{ $record->patient->full_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Clinic</p>
                    <p class="font-medium">{{ $record->clinic->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Consultation Date</p>
                    <p class="font-medium">{{ $record->consultation_date?->format('Y-m-d') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Encounter Type</p>
                    <p class="font-medium">{{ $record->encounter_type ?? $record->consultation_type ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Clinical Assessment</h2>
            <p><strong>Chief Complaint:</strong> {{ $record->chief_complaint ?? 'N/A' }}</p>
            <p class="mt-2"><strong>History of Present Illness:</strong> {{ $record->history_present_illness ?? 'N/A' }}</p>
            <p class="mt-2"><strong>Physical Examination:</strong> {{ $record->physical_examination ?? 'N/A' }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Vital Signs</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Blood Pressure</p>
                    <p class="font-medium">{{ $record->vitals['blood_pressure'] ?? $record->vital_signs['blood_pressure'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Heart Rate (bpm)</p>
                    <p class="font-medium">{{ $record->vitals['heart_rate'] ?? $record->vital_signs['heart_rate'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Temperature (°C)</p>
                    <p class="font-medium">{{ $record->vitals['temperature'] ?? $record->vital_signs['temperature'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Weight (kg)</p>
                    <p class="font-medium">{{ $record->vitals['weight'] ?? $record->vital_signs['weight'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Height (cm)</p>
                    <p class="font-medium">{{ $record->vitals['height'] ?? $record->vital_signs['height'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">O2 Saturation (%)</p>
                    <p class="font-medium">{{ $record->vitals['oxygen_saturation'] ?? $record->vital_signs['oxygen_saturation'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Diagnosis & Treatment</h2>
            <p><strong>Primary Diagnosis:</strong> {{ $record->diagnosis ?? 'N/A' }}</p>
            <p class="mt-2"><strong>ICD-10 Code:</strong> {{ $record->diagnosis_codes ?? 'N/A' }}</p>
            <p class="mt-2"><strong>Assessment & Plan:</strong> {{ $record->assessment_plan ?? 'N/A' }}</p>
            <p class="mt-2"><strong>Treatment Plan:</strong> {{ $record->treatment_plan ?? 'N/A' }}</p>
            <p class="mt-2"><strong>Disposition:</strong> {{ $record->disposition ?? 'N/A' }}</p>
            <p class="mt-2"><strong>Next Appointment:</strong> {{ $record->next_appointment ?? 'N/A' }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Clinical Notes</h2>
            <p><strong>Provider Notes:</strong> {{ $record->doctor_notes ?? 'N/A' }}</p>
            <p class="mt-2"><strong>Additional Notes:</strong> {{ $record->provider_notes ?? 'N/A' }}</p>
            <p class="mt-2"><strong>PhilHealth Number:</strong> {{ $record->philhealth_number ?? 'N/A' }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Prescriptions</h2>
            @php
                $prescriptions = $record->prescriptions()->with('items')->get();
            @endphp

            @if($prescriptions->isNotEmpty())
                @foreach($prescriptions as $pres)
                    <div class="mb-3">
                        <div class="font-medium">Prescription #{{ $pres->id }} — {{ optional($pres->created_at)->format('M d, Y') }}</div>
                        <ul class="list-disc pl-6 mt-2">
                            @foreach($pres->items as $item)
                                <li>{{ $item->name }} — {{ $item->dosage }} — {{ $item->quantity }} @if($item->instructions) ({{ $item->instructions }}) @endif</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @else
                <p class="text-gray-500">No prescriptions recorded.</p>
            @endif
        </div>
    </div>
</div>
