<div class="p-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Organizations</h1>
        @if(auth()->user()->hasRole('superadmin'))
            <a href="{{ route('organizations.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">Add Organization</a>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="mt-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Clinics</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Users</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($organizations as $org)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $org->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $org->clinics()->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $org->users()->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('organizations.edit', $org) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</a>
                            <button wire:click="deleteOrganization({{ $org->id }})" onclick="if(!confirm('Delete this organization?')) { $event.stopImmediatePropagation(); }" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No organizations found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($organizations->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">{{ $organizations->links() }}</div>
        @endif
    </div>
</div>
