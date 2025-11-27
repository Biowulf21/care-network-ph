<div class="p-4">
    <form wire:submit.prevent="save">
        <div class="grid grid-cols-2 gap-4">
            <input wire:model="state.first_name" placeholder="First name" class="border p-2" />
            <input wire:model="state.last_name" placeholder="Last name" class="border p-2" />
            <input wire:model="state.date_of_birth" type="date" class="border p-2" />
            <input wire:model="state.philhealth_number" placeholder="PhilHealth #" class="border p-2" />
        </div>

        <div class="mt-4 flex gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            <a href="{{ route('patients.index') }}" class="px-4 py-2 bg-zinc-200 rounded">Back</a>
            @if($patient)
                <button type="button" onclick="if(!confirm('Delete this patient?')) return false; Livewire.emit('deletePatient', {{ $patient->id }})" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
            @endif
        </div>
    </form>
</div>
