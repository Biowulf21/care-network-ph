<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Clinics</h1>
        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
            <a href="{{ route('clinics.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-center">Add Clinic</a>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="mt-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    @if(auth()->user()->hasRole('superadmin'))
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Organization</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Patients</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Delegates</th>
                    @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($clinics as $clinic)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $clinic->name }}</td>
                        @if(auth()->user()->hasRole('superadmin'))
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $clinic->organization->name ?? '—' }}</td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $clinic->patients()->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $clinic->users()->count() }}</td>
                        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('admin'))
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('clinics.edit', $clinic) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</a>
                                <button wire:click="deleteClinic({{ $clinic->id }})" onclick="if(!confirm('Delete this clinic?')) { $event.stopImmediatePropagation(); }" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->hasRole('superadmin') ? '5' : '4' }}" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No clinics found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($clinics->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">{{ $clinics->links() }}</div>
        @endif
    </div>
</div>
