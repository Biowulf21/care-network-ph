<div class="p-6 max-w-3xl mx-auto">
    <h1 class="text-2xl font-semibold mb-4">{{ $organization ? 'Edit Organization' : 'Create Organization' }}</h1>

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input wire:model.defer="state.name" type="text" class="mt-1 block w-full rounded border-gray-300" />
            @error('state.name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Code</label>
            <input wire:model.defer="state.code" type="text" class="mt-1 block w-full rounded border-gray-300" />
            @error('state.code') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Address</label>
            <textarea wire:model.defer="state.address" class="mt-1 block w-full rounded border-gray-300" rows="3"></textarea>
            @error('state.address') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Phone</label>
            <input wire:model.defer="state.phone" type="text" class="mt-1 block w-full rounded border-gray-300" />
            @error('state.phone') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            <a href="{{ route('organizations.index') }}" class="text-sm text-gray-600">Cancel</a>
        </div>
    </form>
</div>
<div class="p-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $editing ? 'Edit Organization' : 'Add Organization' }}</h1>
        <a href="{{ route('organizations.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">Back</a>
    </div>

    @if (session()->has('message'))
        <div class="mt-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form wire:submit.prevent="save" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Organization Name *</label>
                <input 
                    type="text" 
                    id="name" 
                    wire:model="name" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter organization name"
                    required
                >
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('organizations.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>{{ $editing ? 'Update Organization' : 'Create Organization' }}</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>
