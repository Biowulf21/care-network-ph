<div class="p-6">
    <h1 class="text-2xl font-bold">Delegate Dashboard</h1>

    <div class="grid grid-cols-3 gap-4 mt-4">
        <div class="p-4 bg-white rounded shadow">Clinic: <strong>{{ $clinic?->name ?? '—' }}</strong></div>
        <div class="p-4 bg-white rounded shadow">Patients: <strong>{{ $patientsCount }}</strong></div>
        <div class="p-4 bg-white rounded shadow">EMAR Entries: <strong>{{ $emarCount }}</strong></div>
    </div>

    <div class="mt-6 p-4 bg-white rounded shadow">
        <h2 class="font-semibold mb-2">Recently Assigned To You</h2>
        <ul class="space-y-2">
            @forelse($myAssigned as $r)
                <li class="flex items-start justify-between">
                    <div>
                        <div class="font-medium">{{ $r->patient->first_name }} {{ $r->patient->last_name }} <span class="text-xs text-zinc-500">— {{ optional($r->consultation_date)->format('Y-m-d') }}</span></div>
                        <div class="text-sm text-zinc-600">{{ Str::limit($r->doctor_notes, 120) }}</div>
                    </div>
                    <div class="text-sm">
                        <a href="{{ route('medical-records.index') }}#record-{{ $r->id }}" class="text-blue-600">Open</a>
                    </div>
                </li>
            @empty
                <li class="text-zinc-500">No recent records assigned to you.</li>
            @endforelse
        </ul>
    </div>
</div>
