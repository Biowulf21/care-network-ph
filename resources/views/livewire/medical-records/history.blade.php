<div>
    <h1 class="text-2xl font-semibold mb-4">Medical History for {{ $this->patient->full_name }}</h1>

    <div class="mb-4">
        <a href="{{ route('patients.profile', $this->patient) }}" class="text-sm text-blue-600">&larr; Back to patient</a>
    </div>

    <div class="space-y-4">
        @foreach ($records as $record)
            <div class="p-4 bg-white shadow rounded">
                <div class="flex justify-between">
                    <div>
                        <div class="text-sm text-gray-600">{{ $record->consultation_date->format('Y-m-d') }}</div>
                        <div class="font-medium">{{ $record->chief_complaint }}</div>
                        <div class="text-sm text-gray-500">{{ $record->diagnosis }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="editRecord({{ $record->id }})" class="text-sm text-blue-600">Edit</button>
                        <button wire:click="deleteRecord({{ $record->id }})" class="text-sm text-red-600">Delete</button>
                    </div>
                </div>

                @if ($editingRecordId === $record->id)
                    <div class="mt-3">
                        <div class="grid grid-cols-1 gap-3">
                            <input type="date" wire:model.defer="editingState.consultation_date" class="border rounded p-2" />
                            <input type="text" wire:model.defer="editingState.chief_complaint" placeholder="Chief complaint" class="border rounded p-2" />
                            <textarea wire:model.defer="editingState.diagnosis" placeholder="Diagnosis" class="border rounded p-2"></textarea>
                            <textarea wire:model.defer="editingState.treatment_plan" placeholder="Treatment plan" class="border rounded p-2"></textarea>
                            <div class="flex gap-2 mt-2">
                                <button wire:click="saveRecord" class="px-3 py-1 bg-green-600 text-white rounded">Save</button>
                                <button wire:click="cancelEdit" class="px-3 py-1 bg-gray-200 rounded">Cancel</button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $records->links() }}
    </div>
</div>
