<div class="p-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Appointment Calendar</h1>
        
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <!-- View Type Buttons -->
            <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                <button wire:click="setViewType('month')" class="px-3 py-2 text-sm rounded-md {{ $viewType === 'month' ? 'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' }}">
                    Month
                </button>
                <button wire:click="setViewType('week')" class="px-3 py-2 text-sm rounded-md {{ $viewType === 'week' ? 'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' }}">
                    Week
                </button>
                <button wire:click="setViewType('day')" class="px-3 py-2 text-sm rounded-md {{ $viewType === 'day' ? 'bg-white dark:bg-gray-600 shadow text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-300' }}">
                    Day
                </button>
            </div>
            
            <!-- Clinic Filter (for superadmin) -->
            @if(Auth::user()->role === 'superadmin' && count($clinics) > 0)
                <x-searchable-dropdown :options="$clinics->pluck('name','id')" placeholder="All Clinics" wire:model.live="selectedClinic" />
            @endif
            
            <button wire:click="openModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                New Appointment
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- Calendar Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-center space-x-4">
                <button wire:click="previousPeriod" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    @if($viewType === 'month')
                        {{ $currentDate->format('F Y') }}
                    @elseif($viewType === 'week')
                        Week of {{ $currentDate->startOfWeek()->format('M j') }} - {{ $currentDate->endOfWeek()->format('M j, Y') }}
                    @else
                        {{ $currentDate->format('l, F j, Y') }}
                    @endif
                </h2>
                <button wire:click="nextPeriod" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
            
            <button wire:click="$set('currentDate', '{{ \Carbon\Carbon::now() }}')" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded">
                Today
            </button>
        </div>

        <!-- Calendar Grid -->
        @if($viewType === 'month')
            <!-- Month View -->
            <div class="overflow-x-auto">
            <div class="grid grid-cols-7 gap-0 min-w-[640px]">
                <!-- Day Headers -->
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="p-3 text-center font-medium text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        {{ $day }}
                    </div>
                @endforeach
                
                <!-- Calendar Days -->
                @foreach($calendarDays as $day)
                    @php
                        $dayKey = $day->format('Y-m-d');
                        $dayAppointments = $appointmentsByDate->get($dayKey, collect());
                        $isCurrentMonth = $day->month === $currentDate->month;
                        $isToday = $day->isToday();
                        $isSelected = $dayKey === $selectedDate;
                    @endphp
                    
                    <div wire:click="selectDate('{{ $dayKey }}')" 
                         class="min-h-[120px] p-2 border-b border-r border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 {{ !$isCurrentMonth ? 'bg-gray-50 dark:bg-gray-800 text-gray-400 dark:text-gray-500' : '' }} {{ $isSelected ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-sm font-medium {{ $isToday ? 'bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center' : '' }}">
                                {{ $day->day }}
                            </span>
                            @if($dayAppointments->count() > 0)
                                <span class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-1 rounded">
                                    {{ $dayAppointments->count() }}
                                </span>
                            @endif
                        </div>
                        
                        <!-- Appointments for this day -->
                        <div class="space-y-1">
                            @foreach($dayAppointments->take(3) as $appointment)
                                <div wire:click.stop="openModal({{ $appointment->id }})" 
                                     class="text-xs p-1 rounded cursor-pointer {{ $appointment->status === 'scheduled' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : ($appointment->status === 'confirmed' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : ($appointment->status === 'cancelled' ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200')) }}">
                                    <div class="font-medium">{{ $appointment->appointment_time->format('g:i A') }}</div>
                                    <div class="truncate">{{ $appointment->patient->full_name }}</div>
                                </div>
                            @endforeach
                            
                            @if($dayAppointments->count() > 3)
                                <div class="text-xs text-gray-500 dark:text-gray-400">+{{ $dayAppointments->count() - 3 }} more</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            </div>
        @endif

        @if($viewType === 'week' || $viewType === 'day')
            <!-- Week/Day View -->
            <div class="p-4">
                @php
                    $hours = range(8, 18); // 8 AM to 6 PM
                    $days = $viewType === 'week' ? 
                        collect(range(0, 6))->map(fn($i) => $currentDate->copy()->startOfWeek()->addDays($i)) :
                        collect([$currentDate]);
                @endphp
                
                <div class="grid grid-cols-{{ $viewType === 'week' ? '8' : '2' }} gap-0 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <!-- Time column header -->
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-600">
                        Time
                    </div>
                    
                    <!-- Day headers -->
                    @foreach($days as $day)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 text-center font-medium text-gray-900 dark:text-white border-b border-r border-gray-200 dark:border-gray-600">
                            <div>{{ $day->format('D') }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $day->format('M j') }}</div>
                        </div>
                    @endforeach
                    
                    <!-- Time slots -->
                    @foreach($hours as $hour)
                        <!-- Time label -->
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 text-sm text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-600">
                            {{ \Carbon\Carbon::createFromTime($hour)->format('g:i A') }}
                        </div>
                        
                        <!-- Day columns -->
                        @foreach($days as $day)
                            @php
                                $dayKey = $day->format('Y-m-d');
                                $hourAppointments = $appointmentsByDate->get($dayKey, collect())
                                    ->filter(fn($apt) => $apt->appointment_time->hour === $hour);
                            @endphp
                            
                            <div class="min-h-[60px] p-2 border-b border-r border-gray-200 dark:border-gray-600 relative">
                                @foreach($hourAppointments as $appointment)
                                    <div wire:click="openModal({{ $appointment->id }})" 
                                         class="absolute inset-2 p-2 text-xs rounded cursor-pointer {{ $appointment->status === 'scheduled' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : ($appointment->status === 'confirmed' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : ($appointment->status === 'cancelled' ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200')) }}">
                                        <div class="font-medium">{{ $appointment->patient->full_name }}</div>
                                        <div>{{ $appointment->appointment_type }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Appointment Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ $editingAppointment ? 'Edit Appointment' : 'New Appointment' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Patient *</label>
                            <x-searchable-dropdown :options="$patients->mapWithKeys(fn($p) => [ $p->id => ($p->full_name . ' (' . ($p->patient_id ?? '') . ')') ])" placeholder="Select Patient" wire:model="patient_id" id="patient_id" />
                            @error('patient_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="appointment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Appointment Type *</label>
                            <x-searchable-dropdown :options="['General Consultation'=>'General Consultation','Follow-up Visit'=>'Follow-up Visit','Emergency Visit'=>'Emergency Visit','Routine Check-up'=>'Routine Check-up','Vaccination'=>'Vaccination','Laboratory'=>'Laboratory','Specialist Referral'=>'Specialist Referral']" placeholder="Type" wire:model="appointment_type" id="appointment_type" />
                        </div>

                        <div>
                            <label for="appointment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date *</label>
                            <input type="date" wire:model="appointment_date" id="appointment_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                            @error('appointment_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="appointment_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time *</label>
                            <input type="time" wire:model="appointment_time" id="appointment_time" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                            @error('appointment_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes) *</label>
                            <x-searchable-dropdown :options="['15'=>'15 minutes','30'=>'30 minutes','45'=>'45 minutes','60'=>'1 hour','90'=>'1.5 hours','120'=>'2 hours']" placeholder="Duration" wire:model="duration" id="duration" />
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <x-searchable-dropdown :options="['scheduled'=>'Scheduled','confirmed'=>'Confirmed','cancelled'=>'Cancelled','completed'=>'Completed','no-show'=>'No Show']" placeholder="Status" wire:model="status" id="status" />
                        </div>

                        <div>
                            <label for="specialty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specialty</label>
                            <input type="text" wire:model="specialty" id="specialty" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="e.g., Cardiology, Pediatrics">
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                        <textarea wire:model="notes" id="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Additional notes or instructions"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        @if($editingAppointment)
                            <button type="button" wire:click="deleteAppointment({{ $editingAppointment->id }})" onclick="if(!confirm('Delete this appointment?')) return false;" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                Delete
                            </button>
                        @endif
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors" wire:loading.attr="disabled">
                            <span wire:loading.remove">{{ $editingAppointment ? 'Update' : 'Create' }} Appointment</span>
                            <span wire:loading>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
