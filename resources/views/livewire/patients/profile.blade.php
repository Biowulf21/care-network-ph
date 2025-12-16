<div class="p-6">
    <!-- Patient Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-start space-x-6">
                        @php
                            $first = $state['first_name'] ?? optional($patient)->first_name ?? '';
                            $last = $state['last_name'] ?? optional($patient)->last_name ?? '';
                            $initials = trim((substr($first,0,1) ?? '') . (substr($last,0,1) ?? '')) ?: 'P';

                            // compute a server-side photo URL that mirrors the public disk logic
                            $serverPhotoUrl = null;
                            if ($patient && $patient->photo) {
                                $serverPhotoUrl = Storage::url($patient->photo);
                            }
                        @endphp

                        <div class="h-20 w-20 rounded-full bg-blue-600 overflow-hidden flex items-center justify-center text-white text-2xl font-bold">
                            @if(! empty($photo))
                                <img src="{{ $photo->temporaryUrl() }}" alt="Photo" class="h-full w-full object-cover" />
                            @elseif(! empty($serverPhotoUrl))
                                <img src="{{ $serverPhotoUrl }}" alt="Photo" class="h-full w-full object-cover" />
                            @else
                                {{ strtoupper($initials) }}
                            @endif
                        </div>
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $patient->full_name }}</h1>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Patient ID</span>
                        <p class="font-semibold text-blue-600">{{ $patient->patient_id ?? 'N/A' }}</p>

                    <script>
                        // Global helper: ensure vitals chart is created when Livewire injects the vitals tab.
                        (function () {
                            let attempts = 0;
                            const maxAttempts = 60; // ~6s of retries

                            const tryCreate = () => {
                                const el = document.getElementById('vitalsChart');
                                if (!el) return false;

                                try {
                                    if (typeof Chart === 'undefined') {
                                        console.debug('[global] Chart not yet available');
                                        return false;
                                    }

                                    // If a Chart instance already exists, skip
                                    if (Chart.getChart && Chart.getChart(el)) {
                                        console.debug('[global] Chart instance already present');
                                        return true;
                                    }

                                    const raw = el.getAttribute('data-vitals') || '{}';
                                    const data = JSON.parse(raw || '{}');
                                    const labels = data.dates || [];
                                    const weights = data.weight || [];

                                    if (!labels.length) {
                                        console.debug('[global] no vitals labels to render');
                                        return true;
                                    }

                                    const ctx = el.getContext('2d');
                                    const created = new Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: labels,
                                            datasets: [{
                                                label: 'Weight (kg)',
                                                data: weights,
                                                borderColor: 'rgb(59, 130, 246)',
                                                backgroundColor: 'rgba(59, 130, 246, 0.08)',
                                                fill: true,
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { position: 'top' } }
                                        }
                                    });

                                    console.log('[global] vitals chart created', created);
                                    return true;
                                } catch (e) {
                                    console.error('[global] error creating vitals chart', e);
                                    return false;
                                }
                            };

                            const attemptEnsure = () => {
                                attempts += 1;
                                const ok = tryCreate();
                                if (ok) return;
                                if (attempts >= maxAttempts) {
                                    console.warn('[global] giving up creating vitals chart after', attempts, 'attempts');
                                    return;
                                }
                                setTimeout(attemptEnsure, 100);
                            };

                            document.addEventListener('DOMContentLoaded', attemptEnsure);
                            document.addEventListener('livewire:update', attemptEnsure);
                            // run once immediately in case the element is already present
                            setTimeout(attemptEnsure, 50);
                        })();
                    </script>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Age</span>
                        <p class="font-semibold">{{ $patient->age ?? 'N/A' }} years old</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Gender</span>
                        <p class="font-semibold">{{ $patient->gender ?? $patient->sex ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Blood Type</span>
                        <p class="font-semibold">{{ $patient->blood_type ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Clinic</span>
                    <p class="font-semibold">{{ $patient->clinic->name ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="text-right">
                <a href="{{ route('patients.edit', $patient) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button 
                wire:click="setActiveTab('overview')"
                class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                Overview
            </button>
            <button 
                wire:click="setActiveTab('vitals')"
                class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'vitals' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                Recent Vitals
            </button>
            <button 
                wire:click="setActiveTab('history')"
                class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'history' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                Medical History
            </button>
            <button 
                wire:click="setActiveTab('prescriptions')"
                class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'prescriptions' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                Prescriptions
            </button>
        </nav>
    </div>

    @if($activeTab === 'overview')
        <!-- Overview Tab -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Consultation -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Consultation</h3>
                    <a href="{{ route('medical-records.create', ['patient' => $patient->id]) }}" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded transition-colors text-sm">
                        Add New Consultation
                    </a>
                </div>
                
                @if($this->recentRecords->count())
                    @php $latestRecord = $this->recentRecords->first() @endphp
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Consultation Date</span>
                                <p class="font-medium">{{ $latestRecord->consultation_date?->format('F j, Y') ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Consultation Type</span>
                                <p class="font-medium">{{ $latestRecord->consultation_type ?? $latestRecord->encounter_type ?? 'General Consultation' }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Chief Complaint</span>
                                <p class="font-medium">{{ $latestRecord->chief_complaint ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Attending Physician</span>
                                <p class="font-medium">{{ $latestRecord->user->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        @if($latestRecord->diagnosis)
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Diagnosis</span>
                                <p class="font-medium">{{ $latestRecord->diagnosis }}</p>
                            </div>
                        @endif
                        
                        @if($latestRecord->disposition)
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Disposition</span>
                                <p class="font-medium">{{ $latestRecord->disposition }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">No consultation records found.</p>
                @endif
            </div>

            <!-- Recent Vitals Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Vitals</h3>
                @if($this->recentVitals->count())
                    @php $latestRecord = $this->recentVitals->first(); $lv = $latestRecord->vitals ?? []; @endphp
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Weight (kg)</span>
                            <span class="font-medium">{{ $lv['weight'] ?? $lv['w'] ?? 'n/a' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Temp (°C)</span>
                            <span class="font-medium">{{ $lv['temperature'] ?? $lv['temp'] ?? 'n/a' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Blood Type</span>
                            <span class="font-medium">{{ $patient->blood_type ?? 'n/a' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">HR (bpm)</span>
                            <span class="font-medium">{{ $lv['heart_rate'] ?? $lv['hr'] ?? 'n/a' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">BP (mmHg)</span>
                            <span class="font-medium">{{ $lv['blood_pressure'] ?? $lv['bp'] ?? 'n/a' }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">No vital signs recorded.</p>
                @endif
            </div>
        </div>
    @endif

    @if($activeTab === 'vitals')
        <!-- Vitals Tab -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Vital Signs Chart</h3>
            
            @if(count($this->vitalsChartData['dates']) > 0)
                <div class="chart-responsive mb-6">
                    <canvas id="vitalsChart" width="400" height="200" data-vitals='@json($this->vitalsChartData)'></canvas>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('vitalsChart').getContext('2d');
                        const vitalsChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: @json($this->vitalsChartData['dates']),
                                datasets: [{
                                    label: 'Weight (kg)',
                                    data: @json($this->vitalsChartData['weight']),
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                }
                                return;
                            }

                            if (attempts % 5 === 0) {
                                console.debug('[inline] waiting for Chart to load, attempt', attempts);
                            }
                        });
                    });
                </script>
            @else
                <p class="text-gray-500 dark:text-gray-400">No vital signs data available for charting.</p>
            @endif
        </div>
    @endif

    @if($activeTab === 'history')
        <!-- Medical History Tab -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Medical History</h3>
            
            <div class="space-y-6">
                @foreach($this->recentRecords as $record)
                    <div class="border-l-4 border-blue-500 pl-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">
                                    {{ $record->consultation_date?->format('M d, Y') ?? 'No Date' }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $record->encounter_type ?? 'General Consultation' }}
                                </p>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Dr. {{ $record->user->name ?? 'Unknown' }}
                            </span>
                        </div>
                        <div class="mt-2 text-right">
                            <a href="{{ route('medical-records.show', $record) }}" class="px-2 py-1 text-sm bg-blue-100 text-blue-700 rounded">View</a>
                        </div>
                        
                        @if($record->chief_complaint)
                            <p class="mt-2 text-sm"><strong>Chief Complaint:</strong> {{ $record->chief_complaint }}</p>
                        @endif
                        
                        @if($record->diagnosis)
                            <p class="mt-2 text-sm"><strong>Diagnosis:</strong> {{ $record->diagnosis }}</p>
                        @endif
                        
                        @if($record->treatment_plan)
                            <p class="mt-2 text-sm"><strong>Treatment:</strong> {{ $record->treatment_plan }}</p>
                        @endif
                    </div>
                @endforeach
                @if($this->selectedRecord)
                    <div class="mt-6 bg-gray-50 border rounded p-4">
                        <h4 class="font-semibold">Record Details — {{ $this->selectedRecord->consultation_date?->format('M d, Y') }}</h4>
                        <p class="text-sm mt-2"><strong>Chief Complaint:</strong> {{ $this->selectedRecord->chief_complaint }}</p>
                        <p class="text-sm mt-2"><strong>Diagnosis:</strong> {{ $this->selectedRecord->diagnosis }}</p>
                        <p class="text-sm mt-2"><strong>Treatment:</strong> {{ $this->selectedRecord->treatment_plan }}</p>
                        @if($this->selectedRecordPrescriptions->count())
                            <div class="mt-3">
                                <h5 class="font-medium">Prescriptions</h5>
                                <ul class="list-disc pl-5">
                                    @foreach($this->selectedRecordPrescriptions as $pres)
                                        @foreach($pres->items as $item)
                                            <li>{{ $item->name }} — {{ $item->dosage }} ({{ $item->quantity }}) @if($item->instructions) — {{ $item->instructions }} @endif</li>
                                        @endforeach
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($activeTab === 'prescriptions')
        <!-- Prescriptions Tab -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Prescription History</h3>
            @php $records = $this->prescriptionHistory; @endphp

            @if($records->isNotEmpty())
                <div class="space-y-4">
                    @foreach($records as $r)
                        @php $presList = $r->prescriptions()->with('items')->get(); @endphp
                        @foreach($presList as $pres)
                            <div class="p-4 bg-white rounded shadow-sm">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-medium">Prescription #{{ $pres->id }}</div>
                                        <div class="text-sm text-gray-500">Record: {{ $r->consultation_date?->format('M d, Y') }}</div>
                                    </div>
                                    <div>
                                        @if($r->id)
                                            <a href="{{ route('medical-records.show', $r) }}" class="text-sm text-blue-600">View record</a>
                                        @endif
                                    </div>
                                </div>
                                <ul class="list-disc pl-5 mt-3">
                                    @foreach($pres->items as $item)
                                        <li>{{ $item->name }} — {{ $item->dosage }} — {{ $item->quantity }} @if($item->instructions) ({{ $item->instructions }}) @endif</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No prescriptions recorded.</p>
            @endif
        </div>
    @endif
</div>
