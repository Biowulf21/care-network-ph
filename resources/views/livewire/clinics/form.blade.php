<div class="p-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $editing ? 'Edit Clinic' : 'Add Clinic' }}</h1>
        <a href="{{ route('clinics.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">Back</a>
    </div>

    @if (session()->has('message'))
        <div class="mt-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Clinic Name *</label>
                    <input 
                        type="text" 
                        id="name" 
                        wire:model="name" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Enter clinic name"
                        required
                    >
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="organization_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Organization *</label>
                    @if(auth()->user()->hasRole('admin'))
                        <select 
                            id="organization_id" 
                            wire:model="organization_id" 
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            readonly
                            required
                        >
                            <option value="">Select an organization</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <x-searchable-dropdown :options="$organizations->pluck('name','id')" placeholder="Organization" wire:model="organization_id" id="organization_id" />
                    @endif
                    @error('organization_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('clinics.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>{{ $editing ? 'Update Clinic' : 'Create Clinic' }}</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>
