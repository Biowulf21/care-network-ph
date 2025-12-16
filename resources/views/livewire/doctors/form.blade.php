<div class="p-6 max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $doctor ? 'Edit Doctor' : 'New Doctor' }}</h1>
        <a href="{{ route('doctors.index') }}" class="px-3 py-2 bg-gray-200 rounded">Back</a>
    </div>

    @if(session()->has('message'))
        <div class="mb-4 p-3 bg-green-100">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4 bg-white p-6 rounded shadow">
        <div>
            <label class="block text-sm">Name</label>
            <input type="text" wire:model.defer="state.name" class="w-full border rounded px-3 py-2" required />
            @error('state.name') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm">Clinic</label>
            <x-searchable-dropdown :options="$clinics->pluck('name','id')" wire:model="state.clinic_id" />
            @error('state.clinic_id') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm">Specialty</label>
                <input type="text" wire:model.defer="state.specialty" class="w-full border rounded px-3 py-2" />
            </div>
            <div>
                <label class="block text-sm">Phone</label>
                <input type="text" wire:model.defer="state.phone" class="w-full border rounded px-3 py-2" />
            </div>
        </div>

        <div>
            <label class="block text-sm">Email</label>
            <input type="email" wire:model.defer="state.email" class="w-full border rounded px-3 py-2" />
        </div>

        <div class="text-right">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
        </div>
    </form>
</div>
