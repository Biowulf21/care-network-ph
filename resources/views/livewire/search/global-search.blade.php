<div class="relative" x-data="{ open: @entangle('showResults') }" @click.away="$wire.closeResults()">
    <!-- Search Input -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <input 
            type="text" 
            wire:model.live.debounce.300ms="query"
            class="w-full pl-10 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
            placeholder="Search patients, appointments, records..."
            @focus="if($wire.query.length > 2) $wire.showResults = true"
        >
        @if($query)
            <button 
                wire:click="clearSearch" 
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
            >
                <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
    </div>

    <!-- Search Results -->
    @if($showResults && $query)
        <div class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">
            @if(collect($results)->flatten()->isEmpty())
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.467-.881-6.08-2.33M15 11.75a7.963 7.963 0 00-6.208-3.129c-.932-.24-1.936-.07-2.902.31M15 11.75V9a6 6 0 00-6-6c-1.36 0-2.629.24-3.75.673"></path>
                    </svg>
                    <p class="text-sm">No results found for "{{ $query }}"</p>
                </div>
            @else
                <!-- Patients Section -->
                @if(count($results['patients']) > 0)
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Patients</h4>
                        </div>
                        @foreach($results['patients'] as $patient)
                            <a href="{{ route('patients.profile', $patient) }}" 
                               wire:navigate 
                               class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 last:border-b-0"
                               @click="$wire.closeResults()">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                                {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $patient->full_name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                            {{ $patient->patient_id }} • {{ $patient->email }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Appointments Section -->
                @if(count($results['appointments']) > 0)
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Appointments</h4>
                        </div>
                        @foreach($results['appointments'] as $appointment)
                            <div class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 last:border-b-0">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-green-800 dark:text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $appointment->patient->full_name }} - {{ $appointment->appointment_type }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                            {{ $appointment->appointment_date->format('M j, Y') }} at {{ $appointment->appointment_time->format('g:i A') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Medical Records Section -->
                @if(count($results['medical_records']) > 0)
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Medical Records</h4>
                        </div>
                        @foreach($results['medical_records'] as $record)
                            <div class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 last:border-b-0">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-purple-800 dark:text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.467-.881-6.08-2.33M15 11.75a7.963 7.963 0 00-6.208-3.129c-.932-.24-1.936-.07-2.902.31M15 11.75V9a6 6 0 00-6-6c-1.36 0-2.629.24-3.75.673"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $record->patient->full_name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                            {{ $record->diagnosis ?: $record->chief_complaint ?: 'Medical Record' }} • {{ $record->consultation_date->format('M j, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Users Section (for admin and superadmin) -->
                @if(count($results['users']) > 0)
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Users</h4>
                        </div>
                        @foreach($results['users'] as $user)
                            <a href="{{ route('users.edit', $user) }}" 
                               wire:navigate 
                               class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 last:border-b-0"
                               @click="$wire.closeResults()">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                                            <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                                {{ $user->initials() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $user->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                            {{ $user->email }} • {{ ucfirst($user->role) }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Organizations Section (for superadmin) -->
                @if(count($results['organizations']) > 0)
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Organizations</h4>
                        </div>
                        @foreach($results['organizations'] as $organization)
                            <a href="{{ route('organizations.edit', $organization) }}" 
                               wire:navigate 
                               class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 last:border-b-0"
                               @click="$wire.closeResults()">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-indigo-800 dark:text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $organization->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                            {{ $organization->address ?? 'Organization' }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Clinics Section -->
                @if(count($results['clinics']) > 0)
                    <div>
                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Clinics</h4>
                        </div>
                        @foreach($results['clinics'] as $clinic)
                            <a href="{{ route('clinics.edit', $clinic) }}" 
                               wire:navigate 
                               class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 last:border-b-0"
                               @click="$wire.closeResults()">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-red-800 dark:text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $clinic->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                            {{ $clinic->address ?? 'Clinic' }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>
