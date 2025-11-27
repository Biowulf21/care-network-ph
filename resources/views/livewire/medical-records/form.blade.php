<div class="p-4 max-w-3xl">
    <form wire:submit.prevent="save">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm">Patient</label>
                <select wire:model="state.patient_id" class="border p-2 w-full">
                    <option value="">-- Select Patient --</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->first_name }} {{ $p->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm">Clinic</label>
                <select wire:model="state.clinic_id" class="border p-2 w-full">
                    <option value="">-- Select Clinic --</option>
                    @foreach($clinics as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm">Consultation Date</label>
                <input type="date" wire:model="state.consultation_date" class="border p-2 w-full" />
            </div>

            <div>
                <label class="block text-sm">PhilHealth Claim</label>
                <input wire:model="state.philhealth_number" placeholder="PhilHealth #" class="border p-2 w-full" />
            </div>

            <div class="col-span-2">
                <label class="block text-sm">Doctor Notes</label>
                <textarea wire:model="state.doctor_notes" class="border p-2 w-full h-32"></textarea>
            </div>
        </div>

        <div class="mt-4 flex gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            <a href="{{ route('medical-records.index') }}" class="px-4 py-2 bg-zinc-200 rounded">Back</a>
            @if($record)
                <button type="button" onclick="if(!confirm('Delete this record?')) return false; Livewire.emit('deleteRecord', {{ $record->id }})" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
            @endif
        </div>
    </form>
</div>
