<div class="p-6">
    <!-- Patient Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-start space-x-6">
            <div class="w-24 h-24 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $patient->full_name }}</h1>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Patient ID</span>
                        <p class="font-semibold text-blue-600">{{ $patient->patient_id ?? 'N/A' }}</p>
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
                    @php $latestVitals = $this->recentVitals->first() @endphp
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Weight (kg)</span>
                            <span class="font-medium">{{ $latestVitals['weight'] ?? 'n/a' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Temp (°C)</span>
                            <span class="font-medium">{{ $latestVitals['temp'] ?? 'n/a' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Blood Type</span>
                            <span class="font-medium">{{ $patient->blood_type ?? 'n/a' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">HR (bpm)</span>
                            <span class="font-medium">{{ $latestVitals['hr'] ?? 'n/a' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">BP (mmHg)</span>
                            <span class="font-medium">{{ $latestVitals['bp'] ?? 'n/a' }}</span>
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
                    <canvas id="vitalsChart" width="400" height="200"></canvas>
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
            </div>
        </div>
    @endif

    @if($activeTab === 'prescriptions')
        <!-- Prescriptions Tab -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Prescription History</h3>
            <p class="text-gray-500 dark:text-gray-400">Prescription functionality coming soon...</p>
        </div>
    @endif
</div>
