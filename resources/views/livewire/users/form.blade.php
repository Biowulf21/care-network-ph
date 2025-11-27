<div class="p-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $editing ? 'Edit User' : 'Add User' }}</h1>
        <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">Back</a>
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
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                    <input 
                        type="text" 
                        id="name" 
                        wire:model="name" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        required
                    >
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                    <input 
                        type="email" 
                        id="email" 
                        wire:model="email" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        required
                    >
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password {{ $editing ? '(leave blank to keep current)' : '*' }}</label>
                    <input 
                        type="password" 
                        id="password" 
                        wire:model="password" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        {{ $editing ? '' : 'required' }}
                    >
                    @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        wire:model="password_confirmation" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                    @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role *</label>
                    <select 
                        id="role_id" 
                        wire:model="role_id" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        required
                    >
                        <option value="">Select a role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    @error('role_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="organization_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Organization *</label>
                    <select 
                        id="organization_id" 
                        wire:model="organization_id" 
                        wire:change="updatedOrganizationId"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        {{ auth()->user()->hasRole('admin') ? 'readonly' : '' }}
                        required
                    >
                        <option value="">Select an organization</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </select>
                    @error('organization_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="clinic_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Clinic (Optional)</label>
                    <select 
                        id="clinic_id" 
                        wire:model="clinic_id" 
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">No clinic assignment</option>
                        @foreach($clinics as $clinic)
                            <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                        @endforeach
                    </select>
                    @error('clinic_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>{{ $editing ? 'Update User' : 'Create User' }}</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </form>
    </div>
</div>
