<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
    
    <div class="flex items-center justify-between mt-4 mb-6">
        <div class="flex gap-2">
            <a href="{{ route('patients.index') }}" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Patients</a>
            <a href="{{ route('medical-records.index') }}" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700">Medical Records</a>
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">Clinics: <strong>{{ $clinics->count() }}</strong></div>
    </div>

    <!-- Grid layout for charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's Patient Traffic -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Today's Patient Traffic</h2>
            <div class="relative h-64">
                <canvas id="adminTrafficChart"></canvas>
            </div>
        </div>

        <!-- EMAR Completion Rate per Clinic -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">EMAR Completion Rate per Clinic</h2>
            <div class="relative h-64">
                <canvas id="adminEmarChart"></canvas>
            </div>
        </div>

        <!-- Gender Distribution -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Gender Distribution</h2>
            <div class="relative h-64">
                <canvas id="adminGenderChart"></canvas>
            </div>
        </div>

        <!-- New Patient Registrations -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">New Patient Registrations (Last 30 Days)</h2>
            <div class="relative h-64">
                <canvas id="adminNewPatientsChart"></canvas>
            </div>
        </div>

        <!-- Appointment Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appointment Status (Last 30 Days)</h2>
            <div class="relative h-64">
                <canvas id="adminAppointmentStatusChart"></canvas>
            </div>
        </div>

        <!-- Monthly Appointment Trends -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Appointment Trends</h2>
            <div class="relative h-64">
                <canvas id="adminMonthlyAppointmentsChart"></canvas>
            </div>
        </div>

        <!-- Monthly Consultations -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Consultations</h2>
            <div class="relative h-80">
                <canvas id="adminMonthlyConsultationsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Expose data for bundled charts initializer --}}
    <div id="admin-chart-data" style="display:none"
         data-traffic='@json($todayPatientTraffic->toArray())'
         data-completion='@json($emarCompletionRate->toArray())'
         data-gender='@json($genderDistribution->toArray())'
         data-new-patients='@json($newPatientRegistrations)'
         data-appointment-status='@json($appointmentStatus->toArray())'
         data-monthly-appointments='@json($monthlyAppointments)'
         data-monthly-consultations='@json($monthlyConsultations)'></div>
</div>
