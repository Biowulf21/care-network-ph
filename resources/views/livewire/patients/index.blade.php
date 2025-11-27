<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Patients</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage patient demographics and records</p>
                </div>
                <a href="{{ route('patients.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Patient
                </a>
            </div>
            <div class="mt-4">
                <input wire:model.debounce.300ms="search" placeholder="Search by name..." class="w-full md:w-96 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-200" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clinic</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DOB / Age</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-medium">
                                        {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $patient->full_name }}</div>
                                        @if($patient->patient_id)<div class="text-sm text-gray-500">ID: {{ $patient->patient_id }}</div>@endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $patient->clinic->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ optional($patient->date_of_birth)->format('M d, Y') ?? 'N/A' }}</div>
                                @if($patient->age)<div class="text-sm text-gray-500">{{ $patient->age }} years old</div>@endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($patient->phone)<div class="text-sm text-gray-900">{{ $patient->phone }}</div>@endif
                                @if($patient->email)<div class="text-sm text-gray-500">{{ $patient->email }}</div>@endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('patients.profile', $patient) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="{{ route('patients.edit', $patient) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <button wire:click="deletePatient({{ $patient->id }})" onclick="if(!confirm('Delete this patient?')) { $event.stopImmediatePropagation(); }" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <p class="mt-2">No patients found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $patients->links() }}
        </div>
    </div>
</div>
