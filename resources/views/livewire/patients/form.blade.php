<div class="max-w-4xl mx-auto p-6">
    <form wire:submit.prevent="save" class="bg-white shadow-md rounded-lg p-6">
        <div class="flex items-start gap-6 mb-6">
            <div class="flex-shrink-0">
                <div class="h-20 w-20 rounded-full bg-blue-600 flex items-center justify-center text-white text-2xl font-bold">
                    @php
                        $first = $state['first_name'] ?? ($patient->first_name ?? '');
                        $last = $state['last_name'] ?? ($patient->last_name ?? '');
                        $initials = trim((substr($first,0,1) ?? '') . (substr($last,0,1) ?? '')) ?: 'P';
                    @endphp
                    {{ strtoupper($initials) }}
                </div>
            </div>

            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $patient ? ($patient->full_name ?? 'Edit Patient') : 'New Patient' }}</h2>
                        <p class="text-sm text-gray-500 mt-1">Basic demographic and contact information.</p>
                    </div>
                    <div class="text-right text-sm text-gray-600">
                        @if($patient && $patient->date_of_birth)
                            <div>Age: <span class="font-medium">{{ $patient->age }}</span></div>
                        @endif
                    </div>
                </div>

                @if(session()->has('message'))
                    <div class="mt-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded">{{ session('message') }}</div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">First name</label>
                        <input wire:model.defer="state.first_name" type="text" class="mt-1 block w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('state.first_name') border-red-400 bg-red-50 @enderror" />
                        @error('state.first_name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last name</label>
                        <input wire:model.defer="state.last_name" type="text" class="mt-1 block w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('state.last_name') border-red-400 bg-red-50 @enderror" />
                        @error('state.last_name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Middle name</label>
                        <input wire:model.defer="state.middle_name" type="text" class="mt-1 block w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('state.middle_name') border-red-400 bg-red-50 @enderror" />
                        @error('state.middle_name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date of birth</label>
                        <input wire:model="state.date_of_birth" type="date" class="mt-1 block w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('state.date_of_birth') border-red-400 bg-red-50 @enderror" />
                        @error('state.date_of_birth') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sex</label>
                        <select wire:model.defer="state.sex" class="mt-1 block w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('state.sex') border-red-400 bg-red-50 @enderror">
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Blood type</label>
                        <input wire:model.defer="state.blood_type" type="text" class="mt-1 block w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('state.blood_type') border-red-400 bg-red-50 @enderror" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">PhilHealth #</label>
                        <input wire:model.defer="state.philhealth_number" type="text" class="mt-1 block w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('state.philhealth_number') border-red-400 bg-red-50 @enderror" />
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <input wire:model.defer="state.address" type="text" class="mt-1 block w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('state.address') border-red-400 bg-red-50 @enderror" />
                    @error('state.address') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input wire:model.defer="state.phone" type="text" class="mt-1 block w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('state.phone') border-red-400 bg-red-50 @enderror" />
                        @error('state.phone') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input wire:model.defer="state.email" type="email" class="mt-1 block w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('state.email') border-red-400 bg-red-50 @enderror" />
                        @error('state.email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    @if(auth()->user()->hasRole('delegate'))
                        <label class="block text-sm font-medium text-gray-700">Clinic</label>
                        <div class="mt-1 p-2 bg-gray-50 rounded">{{ optional(auth()->user()->clinic)->name ?? 'Assigned clinic' }}</div>
                    @else
                        <label class="block text-sm font-medium text-gray-700">Clinic</label>
                        <select wire:model.defer="state.clinic_id" class="mt-1 block w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('state.clinic_id') border-red-400 bg-red-50 @enderror">
                            <option value="">Select clinic</option>
                            @foreach($clinics as $clinic)
                                <option value="{{ $clinic['id'] }}">{{ $clinic['name'] }}</option>
                            @endforeach
                        </select>
                        @error('state.clinic_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    @endif
                </div>

            </div>

            <aside class="space-y-4">
                <div class="p-4 bg-zinc-50 rounded-md">
                    <h3 class="text-sm font-medium mb-2">Physical</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-gray-600">Height (cm)</label>
                            <input wire:model.defer="state.height" type="number" step="0.1" class="mt-1 block w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('state.height') border-red-400 bg-red-50 @enderror" />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">Weight (kg)</label>
                            <input wire:model.defer="state.weight" type="number" step="0.1" class="mt-1 block w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('state.weight') border-red-400 bg-red-50 @enderror" />
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-zinc-50 rounded-md">
                    <h3 class="text-sm font-medium mb-2">Emergency Contact</h3>
                    <div class="space-y-2">
                        <input wire:model.defer="state.emergency_contact_name" placeholder="Name" class="block w-full rounded-md border px-3 py-2" />
                        <input wire:model.defer="state.emergency_contact_phone" placeholder="Phone" class="block w-full rounded-md border px-3 py-2" />
                    </div>
                </div>
            </aside>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" wire:loading.attr="disabled" wire:target="save" class="inline-flex items-center gap-3 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <svg wire:loading wire:target="save" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                <span>{{ $patient ? 'Update Patient' : 'Create Patient' }}</span>
            </button>

            <a href="{{ route('patients.index') }}" class="px-4 py-2 bg-zinc-100 rounded-md">Cancel</a>

            @if($patient)
                <button type="button" onclick="if(!confirm('Delete this patient?')) return false; $wire.delete({{ $patient->id }})" class="px-4 py-2 bg-red-600 text-white rounded-md">Delete</button>
            @endif
        </div>
    </form>
</div>
