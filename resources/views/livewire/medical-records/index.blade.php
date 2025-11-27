<div class="p-4">
    <div class="mb-4 flex items-center justify-between">
        <input wire:model.debounce.300ms="search" placeholder="Search records..." class="border p-2 rounded" />
        <a href="{{ route('medical-records.create') }}" class="ml-4 px-3 py-2 bg-green-600 text-white rounded">Add Record</a>
    </div>

    <table class="min-w-full bg-white">
        <thead>
            <tr>
                <th class="px-4 py-2">Patient</th>
                <th class="px-4 py-2">Clinic</th>
                <th class="px-4 py-2">Date</th>
                <th class="px-4 py-2">Notes</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td class="border px-4 py-2">{{ $record->patient->first_name }} {{ $record->patient->last_name }}</td>
                    <td class="border px-4 py-2">{{ $record->clinic->name }}</td>
                    <td class="border px-4 py-2">{{ optional($record->consultation_date)->format('Y-m-d') }}</td>
                    <td class="border px-4 py-2">{{ Str::limit($record->doctor_notes, 60) }}</td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('medical-records.index') }}#record-{{ $record->id }}" class="text-blue-600">View</a>
                        <button onclick="if(!confirm('Delete this record?')) return false; Livewire.emit('deleteRecord', {{ $record->id }})" class="ml-2 text-red-600">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $records->links() }}</div>
</div>
