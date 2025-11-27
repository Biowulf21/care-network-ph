<div class="p-6">
    <h1 class="text-2xl font-bold">Admin Dashboard</h1>
    <div class="mt-4">
        <h2 class="font-semibold">Clinics</h2>
        <ul>
            @foreach($clinics as $clinic)
                <li>{{ $clinic->name }}</li>
            @endforeach
        </ul>
    </div>
    <div class="flex items-center justify-between mt-4">
        <div class="flex gap-2">
            <a href="{{ route('patients.index') }}" class="px-3 py-2 bg-blue-600 text-white rounded">Patients</a>
            <a href="{{ route('medical-records.index') }}" class="px-3 py-2 bg-green-600 text-white rounded">Medical Records</a>
        </div>
        <div class="text-sm text-zinc-500">Clinics: <strong>{{ $clinics->count() }}</strong></div>
    </div>

    <div class="grid grid-cols-2 gap-6 mt-4">
        {{-- Data for bundled charts (read by resources/js/charts.js) --}}
        <div id="admin-chart-data" style="display:none"
             data-traffic='@json($todayPatientTraffic->toArray())'
             data-completion='@json($emarCompletionRate->toArray())'></div>

        <div class="p-4 bg-white rounded shadow h-64">
            <h2 class="font-semibold mb-2">Today's Patient Traffic</h2>
            <div class="h-48">
                <canvas id="adminTrafficChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <div class="p-4 bg-white rounded shadow h-64">
            <h2 class="font-semibold mb-2">EMAR Completion Rate per Clinic</h2>
            <div class="h-48">
                <canvas id="adminEmarChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    {{-- charts are initialized from bundled JS (resources/js/charts.js) --}}
</div>
