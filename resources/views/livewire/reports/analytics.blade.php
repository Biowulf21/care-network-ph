<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Analytics & Reports</h1>
        
        <div class="flex items-center space-x-4">
            <!-- Date Range Filter -->
            <select wire:model.live="dateRange" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                <option value="7">Last 7 Days</option>
                <option value="30">Last 30 Days</option>
                <option value="90">Last 90 Days</option>
                <option value="365">Last Year</option>
                <option value="ytd">Year to Date</option>
            </select>

            <!-- Organization Filter (superadmin only) -->
            @if(Auth::user()->hasRole('superadmin') && count($organizations) > 0)
                <select wire:model.live="selectedOrganization" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Organizations</option>
                    @foreach($organizations as $organization)
                        <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                    @endforeach
                </select>
            @endif

            <!-- Clinic Filter -->
            @if(count($clinics) > 1)
                <select wire:model.live="selectedClinic" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Clinics</option>
                    @foreach($clinics as $clinic)
                        <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>

    <!-- Report Type Navigation -->
    <div class="mb-8">
        <div class="flex space-x-1 bg-gray-100 dark:bg-gray-700 p-1 rounded-lg">
            <button wire:click="setReportType('overview')" class="px-4 py-2 text-sm rounded-md transition-colors {{ $reportType === 'overview' ? 'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}">
                Overview
            </button>
            <button wire:click="setReportType('patients')" class="px-4 py-2 text-sm rounded-md transition-colors {{ $reportType === 'patients' ? 'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}">
                Patients
            </button>
            <button wire:click="setReportType('appointments')" class="px-4 py-2 text-sm rounded-md transition-colors {{ $reportType === 'appointments' ? 'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}">
                Appointments
            </button>
            <button wire:click="setReportType('medical_records')" class="px-4 py-2 text-sm rounded-md transition-colors {{ $reportType === 'medical_records' ? 'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}">
                Medical Records
            </button>
        </div>
    </div>

    @if($reportType === 'overview')
        <!-- Overview Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Patients -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Patients</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($overviewStats['total_patients']) }}</p>
                        <p class="text-sm text-green-600 dark:text-green-400">+{{ $overviewStats['new_patients'] }} new</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Appointments -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Appointments</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($overviewStats['appointments_period']) }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $overviewStats['completed_appointments'] }} completed</p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Medical Records -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Medical Records</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($overviewStats['records_period']) }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">This period</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.467-.881-6.08-2.33M15 11.75a7.963 7.963 0 00-6.208-3.129c-.932-.24-1.936-.07-2.902.31M15 11.75V9a6 6 0 00-6-6c-1.36 0-2.629.24-3.75.673"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completion Rate -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completion Rate</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">
                            @if($overviewStats['appointments_period'] > 0)
                                {{ round(($overviewStats['completed_appointments'] / $overviewStats['appointments_period']) * 100, 1) }}%
                            @else
                                N/A
                            @endif
                        </p>
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $overviewStats['cancelled_appointments'] }} cancelled</p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Appointment Status Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appointment Status</h3>
                <canvas id="appointmentStatusChart" width="400" height="200"></canvas>
            </div>

            <!-- Patient Demographics Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Patient Age Groups</h3>
                <canvas id="patientAgeChart" width="400" height="200"></canvas>
            </div>
        </div>
    @endif

    @if($reportType === 'patients')
        <!-- Patient Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Age Distribution -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Age Distribution</h3>
                <div class="space-y-3">
                    @foreach($patientStats['age_groups'] as $ageGroup => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $ageGroup }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $patientStats['age_groups']->sum() > 0 ? ($count / $patientStats['age_groups']->sum()) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Gender Distribution -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Gender Distribution</h3>
                <canvas id="genderChart" width="400" height="200"></canvas>
            </div>

            <!-- Blood Type Distribution -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Blood Type Distribution</h3>
                <div class="space-y-3">
                    @foreach($patientStats['by_blood_type'] as $bloodType => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $bloodType }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-red-600 h-2 rounded-full" style="width: {{ $patientStats['by_blood_type']->sum() > 0 ? ($count / $patientStats['by_blood_type']->sum()) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- New Registrations -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">New Patient Registrations</h3>
                <canvas id="newRegistrationsChart" width="400" height="200"></canvas>
            </div>
        </div>
    @endif

    @if($reportType === 'appointments')
        <!-- Appointment Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Appointments by Type -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appointments by Type</h3>
                <div class="space-y-3">
                    @foreach($appointmentStats['by_type'] as $type => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $type }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $appointmentStats['by_type']->sum() > 0 ? ($count / $appointmentStats['by_type']->sum()) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Appointment Status -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appointment Status</h3>
                <canvas id="appointmentStatusChart2" width="400" height="200"></canvas>
            </div>

            <!-- Monthly Trends -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Appointment Trends</h3>
                <canvas id="monthlyTrendsChart" width="800" height="300"></canvas>
            </div>
        </div>

        <!-- Completion Rate Stats -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Metrics</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $appointmentStats['completion_rate'] }}%</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Completion Rate</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $appointmentStats['by_status']->get('scheduled', 0) + $appointmentStats['by_status']->get('confirmed', 0) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Upcoming Appointments</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $appointmentStats['by_status']->get('cancelled', 0) + $appointmentStats['by_status']->get('no-show', 0) }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Missed Appointments</p>
                </div>
            </div>
        </div>
    @endif

    @if($reportType === 'medical_records')
        <!-- Medical Records Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Common Diagnoses -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Most Common Diagnoses</h3>
                <div class="space-y-3">
                    @foreach($medicalRecordStats['common_diagnoses']->take(10) as $diagnosis => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400 capitalize truncate max-w-40">{{ $diagnosis }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $medicalRecordStats['common_diagnoses']->max() > 0 ? ($count / $medicalRecordStats['common_diagnoses']->max()) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Records by Month -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Consultations</h3>
                <canvas id="recordsMonthlyChart" width="400" height="200"></canvas>
            </div>

            <!-- Activity Metrics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Activity Metrics</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($medicalRecordStats['total_consultations']) }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Consultations</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $medicalRecordStats['average_per_day'] }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Average per Day</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $medicalRecordStats['common_diagnoses']->count() }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Unique Diagnoses</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    (function() {
        // Use server-provided report type instead of accessing $wire directly in JS
        const currentReportType = {!! json_encode($reportType) !!} ?? null;

        // Provide JS-friendly copies of PHP data (fall back to empty objects/arrays)
        const appointmentByStatus = {!! json_encode($appointmentStats['by_status'] ?? []) !!};
        const patientAgeGroups = {!! json_encode($patientStats['age_groups'] ?? []) !!};
        const patientByGender = {!! json_encode($patientStats['by_gender'] ?? []) !!};
        const newRegistrations = {!! json_encode($patientStats['new_registrations'] ?? []) !!};
        const appointmentByMonth = {!! json_encode($appointmentStats['by_month'] ?? []) !!};
        const appointmentByType = {!! json_encode($appointmentStats['by_type'] ?? []) !!};
        const medicalRecordsByMonth = {!! json_encode($medicalRecordStats['records_by_month'] ?? []) !!};

        function registerLivewireHooks() {
            if (typeof window.Livewire !== 'undefined') {
                if (typeof window.Livewire.on === 'function') {
                    window.Livewire.on('refresh', () => setTimeout(initializeCharts, 100));
                } else if (window.Livewire.hook) {
                    window.Livewire.hook('message.processed', () => setTimeout(initializeCharts, 100));
                }
            } else {
                // As a fallback, initialize on DOM load
                document.addEventListener('livewire:load', () => setTimeout(initializeCharts, 100));
            }
        }

        function initializeCharts() {
            try {
                if (typeof Chart === 'undefined') return; // Chart.js not loaded yet

                // Appointment Status Chart (overview)
                try {
                    const appointmentStatusCtx = document.getElementById('appointmentStatusChart');
                    if (appointmentStatusCtx) {
                        const labels = Object.keys(appointmentByStatus || {});
                        const data = Object.values(appointmentByStatus || {});

                        // reuse/destroy if already present
                        if (window.appointmentStatusChart && typeof window.appointmentStatusChart.destroy === 'function') {
                            try { window.appointmentStatusChart.destroy(); } catch (e) { /* ignore */ }
                            window.appointmentStatusChart = null;
                        }

                        window.appointmentStatusChart = new Chart(appointmentStatusCtx.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels,
                                datasets: [{
                                    data,
                                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#6B7280', '#8B5CF6'],
                                }]
                            },
                            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
                        });
                    }
                } catch (e) { console.warn('appointmentStatusChart init failed', e); }

                // Patient Age Chart
                try {
                    const patientAgeCtx = document.getElementById('patientAgeChart');
                    if (patientAgeCtx) {
                        const labels = Object.keys(patientAgeGroups || {});
                        const data = Object.values(patientAgeGroups || {});

                        if (window.patientAgeChart && typeof window.patientAgeChart.destroy === 'function') {
                            try { window.patientAgeChart.destroy(); } catch (e) { /* ignore */ }
                            window.patientAgeChart = null;
                        }

                        window.patientAgeChart = new Chart(patientAgeCtx.getContext('2d'), {
                            type: 'bar',
                            data: { labels, datasets: [{ label: 'Patients', data, backgroundColor: '#3B82F6' }] },
                            options: { responsive: true, scales: { y: { beginAtZero: true } } }
                        });
                    }
                } catch (e) { console.warn('patientAgeChart init failed', e); }

                // Initialize other charts based on the server-provided report type
                if (currentReportType === 'patients') {
                    initializePatientCharts();
                } else if (currentReportType === 'appointments') {
                    initializeAppointmentCharts();
                } else if (currentReportType === 'medical_records') {
                    initializeMedicalRecordCharts();
                }
            } catch (err) {
                console.error('initializeCharts error', err);
            }
        }

        function initializePatientCharts() {
            try {
                // Gender Chart
                const genderCtx = document.getElementById('genderChart');
                if (genderCtx) {
                    const labels = Object.keys(patientByGender || {});
                    const data = Object.values(patientByGender || {});

                    if (window.genderChart && typeof window.genderChart.destroy === 'function') {
                        try { window.genderChart.destroy(); } catch (e) { /* ignore */ }
                        window.genderChart = null;
                    }

                    window.genderChart = new Chart(genderCtx.getContext('2d'), {
                        type: 'pie',
                        data: { labels, datasets: [{ data, backgroundColor: ['#3B82F6', '#EF4444', '#10B981'] }] },
                        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
                    });
                }
            } catch (e) { console.warn('genderChart init failed', e); }

            try {
                // New Registrations Chart
                const newRegCtx = document.getElementById('newRegistrationsChart');
                if (newRegCtx) {
                    const labels = Object.keys(newRegistrations || {});
                    const data = Object.values(newRegistrations || {});

                    if (window.newRegistrationsChart && typeof window.newRegistrationsChart.destroy === 'function') {
                        try { window.newRegistrationsChart.destroy(); } catch (e) { /* ignore */ }
                        window.newRegistrationsChart = null;
                    }

                    window.newRegistrationsChart = new Chart(newRegCtx.getContext('2d'), {
                        type: 'line',
                        data: { labels, datasets: [{ label: 'New Patients', data, borderColor: '#10B981', tension: 0.1 }] },
                        options: { responsive: true, scales: { y: { beginAtZero: true } } }
                    });
                }
            } catch (e) { console.warn('newRegistrationsChart init failed', e); }
        }

        function initializeAppointmentCharts() {
            try {
                // Appointment Status Chart 2
                const appointmentStatus2Ctx = document.getElementById('appointmentStatusChart2');
                if (appointmentStatus2Ctx) {
                    const labels = Object.keys(appointmentByStatus || {});
                    const data = Object.values(appointmentByStatus || {});

                    if (window.appointmentStatusChart2 && typeof window.appointmentStatusChart2.destroy === 'function') {
                        try { window.appointmentStatusChart2.destroy(); } catch (e) { /* ignore */ }
                        window.appointmentStatusChart2 = null;
                    }

                    window.appointmentStatusChart2 = new Chart(appointmentStatus2Ctx.getContext('2d'), {
                        type: 'doughnut',
                        data: { labels, datasets: [{ data, backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#6B7280', '#8B5CF6'] }] },
                        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
                    });
                }
            } catch (e) { console.warn('appointmentStatusChart2 init failed', e); }

            try {
                // Monthly Trends Chart
                const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart');
                if (monthlyTrendsCtx) {
                    const labels = Object.keys(appointmentByMonth || {});
                    const data = Object.values(appointmentByMonth || {});

                    if (window.monthlyTrendsChart && typeof window.monthlyTrendsChart.destroy === 'function') {
                        try { window.monthlyTrendsChart.destroy(); } catch (e) { /* ignore */ }
                        window.monthlyTrendsChart = null;
                    }

                    window.monthlyTrendsChart = new Chart(monthlyTrendsCtx.getContext('2d'), {
                        type: 'line',
                        data: { labels, datasets: [{ label: 'Appointments', data, borderColor: '#3B82F6', tension: 0.1 }] },
                        options: { responsive: true, scales: { y: { beginAtZero: true } } }
                    });
                }
            } catch (e) { console.warn('monthlyTrendsChart init failed', e); }
        }

        function initializeMedicalRecordCharts() {
            try {
                // Records Monthly Chart
                const recordsMonthlyCtx = document.getElementById('recordsMonthlyChart');
                if (recordsMonthlyCtx) {
                    const labels = Object.keys(medicalRecordsByMonth || {});
                    const data = Object.values(medicalRecordsByMonth || {});

                    if (window.recordsMonthlyChart && typeof window.recordsMonthlyChart.destroy === 'function') {
                        try { window.recordsMonthlyChart.destroy(); } catch (e) { /* ignore */ }
                        window.recordsMonthlyChart = null;
                    }

                    window.recordsMonthlyChart = new Chart(recordsMonthlyCtx.getContext('2d'), {
                        type: 'bar',
                        data: { labels, datasets: [{ label: 'Consultations', data, backgroundColor: '#8B5CF6' }] },
                        options: { responsive: true, scales: { y: { beginAtZero: true } } }
                    });
                }
            } catch (e) { console.warn('recordsMonthlyChart init failed', e); }
        }

        // Initialize charts on page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeCharts, 100);
        });

        // Register Livewire hooks so charts re-init after updates
        registerLivewireHooks();
    })();
</script>
@endscript
