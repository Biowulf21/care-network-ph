<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Superadmin Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Organizations</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalOrganizations }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Clinics</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalClinics }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Users</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalUsers }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">EMAR Entries</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $emarCount }}</p>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Daily Patient Intake -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daily Patient Intake (Last 14 Days)</h2>
            <div class="relative h-64">
                <canvas id="superadminIntakeChart"></canvas>
            </div>
        </div>

        <!-- EMAR Completion -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">EMAR Completion</h2>
            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Completion rate: <strong class="text-gray-900 dark:text-white">{{ $emarCompletion }}%</strong></p>
            </div>
            <div class="relative h-48">
                <canvas id="superadminEmarChart"></canvas>
            </div>
        </div>

        <!-- Claims Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Claims Overview</h2>
            <div class="relative h-64">
                <canvas id="superadminClaimsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Expose data for bundled charts initializer --}}
    <div id="superadmin-chart-data" style="display:none"
        data-labels='@json($labels)'
        data-counts='@json($counts)'
        data-emar='{{ $emarCompletion }}'
        data-claims='@json($claims)'></div>
</div>
 




