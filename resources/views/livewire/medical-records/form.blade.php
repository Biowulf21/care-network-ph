<div class="p-6 max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $record ? 'Edit Medical Record' : 'New Medical Record' }}</h1>
        <a href="{{ request()->query('patient') ? route('patients.profile', request()->query('patient')) : route('medical-records.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">{{ request()->query('patient') ? 'Back to Patient' : 'Back to Records' }}</a>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-8">
        <!-- Patient Selection and Basic Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Patient Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Patient *</label>
                    <x-searchable-dropdown :options="$patients->pluck('full_name','id')" placeholder="Select patient" wire:model="state.patient_id" id="patient_id" :value="$state['patient_id'] ?? ''" />
                    @error('state.patient_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="clinic_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Clinic *</label>
                    <x-searchable-dropdown :options="$clinics->pluck('name','id')" placeholder="Select clinic" wire:model="state.clinic_id" id="clinic_id" :value="$state['clinic_id'] ?? ''" />
                    @error('state.clinic_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="doctor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attending Physician</label>
                    <x-searchable-dropdown :options="$doctors->pluck('name','id')" placeholder="Select doctor" wire:model="state.doctor_id" id="doctor_id" :value="$state['doctor_id'] ?? ''" />
                    @error('state.doctor_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="consultation_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consultation Date *</label>
                    <input type="date" wire:model="state.consultation_date" id="consultation_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                    @error('state.consultation_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="encounter_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Encounter Type</label>
                    <x-searchable-dropdown :options="['' => '--','consultation' => 'Consultation','follow_up' => 'Follow Up','telemedicine' => 'Telemedicine']" placeholder="Encounter type" wire:model="state.encounter_type" id="encounter_type" :value="$state['encounter_type'] ?? ''" />
                    @error('state.encounter_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Chief Complaint and History -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Clinical Assessment</h2>
            <div class="space-y-6">
                <div>
                    <label for="chief_complaint" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Chief Complaint</label>
                    <input type="text" wire:model="state.chief_complaint" id="chief_complaint" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="e.g., FEVER RASHES COUGH COLD">
                </div>
                <div>
                    <label for="history_present_illness" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">History of Present Illness</label>
                    <textarea wire:model="state.history_present_illness" id="history_present_illness" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Detailed description of current illness"></textarea>
                </div>
                <div>
                    <label for="physical_examination" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Physical Examination</label>
                    <textarea wire:model="state.physical_examination" id="physical_examination" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Physical examination findings"></textarea>
                </div>
            </div>
        </div>

        <!-- Vital Signs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Vital Signs</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <div>
                    <label for="bp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Blood Pressure</label>
                    <input type="text" wire:model="state.vitals.blood_pressure" id="bp" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="120/80">
                </div>
                <div>
                    <label for="hr" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Heart Rate (bpm)</label>
                    <input type="number" wire:model="state.vitals.heart_rate" id="hr" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="72">
                </div>
                <div>
                    <label for="temp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Temperature (°C)</label>
                    <input type="number" step="0.1" wire:model="state.vitals.temperature" id="temp" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="37.0">
                </div>
                <div>
                    <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Weight (kg)</label>
                    <input type="number" step="0.1" wire:model="state.vitals.weight" id="weight" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="70.0">
                </div>
                <div>
                    <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Height (cm)</label>
                    <input type="number" wire:model="state.vitals.height" id="height" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="170">
                </div>
                <div>
                    <label for="o2_sat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">O2 Saturation (%)</label>
                    <input type="number" wire:model="state.vitals.oxygen_saturation" id="o2_sat" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="98">
                </div>
                <div>
                    <label for="rr" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Respiratory Rate</label>
                    <input type="number" wire:model="state.vitals.respiratory_rate" id="rr" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="16">
                </div>
                <div>
                    <label for="bmi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">BMI</label>
                    <input type="number" step="0.1" wire:model="state.vitals.bmi" id="bmi" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="24.2">
                </div>
            </div>
        </div>

        <!-- Diagnosis and Treatment -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Diagnosis & Treatment</h2>
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="diagnosis" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Primary Diagnosis</label>
                        <textarea wire:model="state.diagnosis" id="diagnosis" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="e.g., Acute Upper Respiratory Tract Infection"></textarea>
                    </div>
                    <div>
                        <label for="diagnosis_codes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ICD-10 Code</label>
                        <input type="text" wire:model="state.diagnosis_codes" id="diagnosis_codes" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="J06.9">
                    </div>
                </div>
                <div>
                    <label for="assessment_plan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assessment & Plan</label>
                    <textarea wire:model="state.assessment_plan" id="assessment_plan" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Assessment and treatment plan"></textarea>
                </div>
                <div>
                    <label for="treatment_plan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Treatment Plan</label>
                    <textarea wire:model="state.treatment_plan" id="treatment_plan" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Detailed treatment plan"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="disposition" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Disposition</label>
                                <x-searchable-dropdown :options="['' => '--','referred' => 'Referred','admitted' => 'Admitted','discharged' => 'Discharged']" placeholder="Disposition" wire:model="state.disposition" id="disposition" />
                            </div>
                            <div>
                                <label for="next_appointment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Next Appointment</label>
                                <input type="date" wire:model="state.next_appointment" id="next_appointment" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                </div>
            </div>
        </div>

        <!-- Clinical Notes -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Clinical Notes</h2>
            <div class="space-y-6">
                <div>
                    <label for="doctor_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Provider Notes</label>
                    <textarea wire:model="state.doctor_notes" id="doctor_notes" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Additional clinical notes and observations"></textarea>
                </div>
                <div>
                    <label for="provider_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional Notes</label>
                    <textarea wire:model="state.provider_notes" id="provider_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Additional notes, follow-up instructions, etc."></textarea>
                </div>
                <div>
                    <label for="philhealth_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">PhilHealth Number</label>
                    <input disabled wire:model="state.philhealth_number" id="philhealth_number" placeholder="PhilHealth #" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <!-- Prescriptions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Prescriptions</h2>
            <div class="space-y-3">
                <template wire:ignore>
                    <!-- Livewire will render the dynamic rows below -->
                </template>

                @foreach($prescriptions as $i => $line)
                    <div class="grid grid-cols-12 gap-2 items-center">
                        <div class="col-span-4">
                            <input type="text" wire:model.defer="prescriptions.{{ $i }}.name" placeholder="Medicine name" class="w-full rounded-md border px-3 py-2" />
                        </div>
                        <div class="col-span-1">
                            <label class="sr-only">Quantity</label>
                            <input type='number' min='1' wire:model.defer="prescriptions.{{ $i }}.quantity" class="w-full rounded-md border px-3 py-2">
                        </div>
                        <div class="col-span-2">
                            <label class="sr-only">Dosage unit</label>
                            <select wire:model.defer="prescriptions.{{ $i }}.dosage" class="w-full rounded-md border px-3 py-2">
                                <option value="">-- Unit --</option>
                                <option value="tbsp">tbsp</option>
                                <option value="tablet/capsule">tablet / capsule</option>
                                <option value="gram">gram</option>
                                <option value="ml">ml</option>
                                <option value="other">Other (specify)</option>
                            </select>
                            @if(isset($prescriptions[$i]['dosage']) && $prescriptions[$i]['dosage'] === 'other')
                                <input type="text" wire:model.defer="prescriptions.{{ $i }}.dosage_custom" placeholder="Custom dosage unit" class="w-full mt-2 rounded-md border px-3 py-2" />
                            @endif
                        </div>
                        <div class="col-span-2">
                            <input type="text" wire:model.defer="prescriptions.{{ $i }}.frequency" placeholder="Frequency (e.g., 1 tab every 8 hrs)" class="w-full rounded-md border px-3 py-2" />
                        </div>
                        <div class="col-span-2">
                            <input type="text" wire:model.defer="prescriptions.{{ $i }}.instructions" placeholder="Instructions" class="w-full rounded-md border px-3 py-2" />
                        </div>
                        <div class="col-span-1 text-right">
                            <button type="button" wire:click.prevent="removePrescriptionLine({{ $i }})" class="text-red-600">Remove</button>
                        </div>
                    </div>
                @endforeach

                <div>
                    <button type="button" wire:click.prevent="addPrescriptionLine" class="px-3 py-2 bg-green-600 text-white rounded">Add Prescription</button>
                </div>
            </div>
        </div>
        <div class="flex justify-end space-x-4">
            <a href="{{ request()->query('patient') ? route('patients.profile', request()->query('patient')) : route('medical-records.index') }}" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                Cancel
            </a>
            @if($record)
                <button type="button" wire:click="delete" onclick="if(!confirm('Delete this record?')) return false;" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Delete
                </button>
            @endif
            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $record ? 'Update Record' : 'Create Record' }}</span>
                <span wire:loading>Saving...</span>
            </button>
        </div>
    </form>
</div>
