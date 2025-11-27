<div class="p-4">
    <div class="mb-4 flex items-center justify-between">
        <input wire:model.debounce.300ms="search" placeholder="Search patients..." class="border p-2 rounded" />
        <a href="{{ route('patients.create') }}" class="ml-4 px-3 py-2 bg-green-600 text-white rounded">Add Patient</a>
    </div>

    <table class="min-w-full bg-white">
        <thead>
            <tr>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Clinic</th>
                <th class="px-4 py-2">DOB</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $patient)
                <tr>
                    <td class="border px-4 py-2">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                    <td class="border px-4 py-2">{{ $patient->clinic->name }}</td>
                    <td class="border px-4 py-2">{{ optional($patient->date_of_birth)->format('Y-m-d') }}</td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('patients.index') }}#patient-{{ $patient->id }}" class="text-blue-600">View</a>
                        <button onclick="if(!confirm('Delete this patient?')) return false; Livewire.emit('deletePatient', {{ $patient->id }})" class="ml-2 text-red-600">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $patients->links() }}</div>
</div>
