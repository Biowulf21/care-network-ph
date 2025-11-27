<div class="p-6">
    <h1 class="text-2xl font-bold">Superadmin Dashboard</h1>

    <div class="grid grid-cols-4 gap-4 mt-4">
        <div class="p-4 bg-white rounded shadow">Total Organizations: <strong>{{ $totalOrganizations }}</strong></div>
        <div class="p-4 bg-white rounded shadow">Total Clinics: <strong>{{ $totalClinics }}</strong></div>
        <div class="p-4 bg-white rounded shadow">Total Users: <strong>{{ $totalUsers }}</strong></div>
        <div class="p-4 bg-white rounded shadow">EMAR Entries: <strong>{{ $emarCount }}</strong></div>
    </div>

    <div class="grid grid-cols-2 gap-6 mt-6">
        <div class="p-4 bg-white rounded shadow h-72">
            <h2 class="font-semibold mb-2">Daily Patient Intake (last 14 days)</h2>
            <div class="h-56">
                <canvas id="intakeChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <div class="p-4 bg-white rounded shadow h-72">
            <h2 class="font-semibold mb-2">EMAR Completion</h2>
            <div class="mb-4">Completion rate: <strong>{{ $emarCompletion }}%</strong></div>
            <div class="h-56">
                <canvas id="emarCompletionChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-6 p-4 bg-white rounded shadow h-48">
        <h2 class="font-semibold mb-2">Claims Overview</h2>
        <div class="h-32">
            <canvas id="claimsChart" class="w-full h-full"></canvas>
        </div>
    </div>
</div>

{{-- Expose data for bundled charts initializer --}}
<div id="superadmin-chart-data" style="display:none"
     data-labels='@json($labels)'
     data-counts='@json($counts)'
     data-emar='{{ $emarCompletion }}'
     data-claims='@json($claims)'></div>

 
