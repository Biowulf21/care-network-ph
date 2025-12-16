<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>

                    @auth
                        @php $user = auth()->user(); @endphp

                        @if($user->hasRole('superadmin'))
                            <flux:navlist.item icon="users" :href="route('users.index')" :current="request()->routeIs('users*')" wire:navigate>{{ __('Users') }}</flux:navlist.item>
                            <flux:navlist.item icon="building-office" :href="route('organizations.index')" :current="request()->routeIs('organizations*')" wire:navigate>{{ __('Organizations') }}</flux:navlist.item>
                            <flux:navlist.item icon="map" :href="route('clinics.index')" :current="request()->routeIs('clinics*')" wire:navigate>{{ __('Clinics') }}</flux:navlist.item>
                            <flux:navlist.item icon="users" :href="route('doctors.index')" :current="request()->routeIs('doctors*')" wire:navigate>{{ __('Doctors') }}</flux:navlist.item>
                            <flux:navlist.item icon="calendar" :href="route('appointments.calendar')" :current="request()->routeIs('appointments*')" wire:navigate>{{ __('Appointments') }}</flux:navlist.item>
                            <flux:navlist.item icon="chart-bar" :href="route('reports.analytics')" :current="request()->routeIs('reports*')" wire:navigate>{{ __('Reports') }}</flux:navlist.item>
                        @elseif($user->hasRole('admin'))
                            <flux:navlist.item icon="building-office" :href="route('organizations.index')" :current="request()->routeIs('organizations*')" wire:navigate>{{ __('Organization') }}</flux:navlist.item>
                            <flux:navlist.item icon="map" :href="route('clinics.index')" :current="request()->routeIs('clinics*')" wire:navigate>{{ __('Clinics') }}</flux:navlist.item>
                            <flux:navlist.item icon="users" :href="route('doctors.index')" :current="request()->routeIs('doctors*')" wire:navigate>{{ __('Doctors') }}</flux:navlist.item>
                            <flux:navlist.item icon="users" :href="route('patients.index')" :current="request()->routeIs('patients*')" wire:navigate>{{ __('Patients') }}</flux:navlist.item>
                            <flux:navlist.item icon="calendar" :href="route('appointments.calendar')" :current="request()->routeIs('appointments*')" wire:navigate>{{ __('Appointments') }}</flux:navlist.item>
                            <flux:navlist.item icon="chart-bar" :href="route('reports.analytics')" :current="request()->routeIs('reports*')" wire:navigate>{{ __('Reports') }}</flux:navlist.item>
                            <flux:navlist.item icon="user-group" :href="route('users.index')" :current="request()->routeIs('users*')" wire:navigate>{{ __('Delegates') }}</flux:navlist.item>
                        @elseif($user->hasRole('delegate'))
                            <flux:navlist.item icon="users" :href="route('patients.index')" :current="request()->routeIs('patients*')" wire:navigate>{{ __('Patients') }}</flux:navlist.item>
                            <flux:navlist.item icon="calendar" :href="route('appointments.calendar')" :current="request()->routeIs('appointments*')" wire:navigate>{{ __('Appointments') }}</flux:navlist.item>
                            <flux:navlist.item icon="document-text" :href="route('medical-records.index')" :current="request()->routeIs('medical-records*')" wire:navigate>{{ __('Medical Records') }}</flux:navlist.item>
                            <flux:navlist.item icon="chart-bar" :href="route('reports.analytics')" :current="request()->routeIs('reports*')" wire:navigate>{{ __('Reports') }}</flux:navlist.item>
                        @else
                            <flux:navlist.item icon="users" :href="route('patients.index')" :current="request()->routeIs('patients*')" wire:navigate>{{ __('Patients') }}</flux:navlist.item>
                            <flux:navlist.item icon="calendar" :href="route('appointments.calendar')" :current="request()->routeIs('appointments*')" wire:navigate>{{ __('Appointments') }}</flux:navlist.item>
                            <flux:navlist.item icon="chart-bar" :href="route('reports.analytics')" :current="request()->routeIs('reports*')" wire:navigate>{{ __('Reports') }}</flux:navlist.item>
                        @endif

                    @endauth
                </flux:navlist.group>
            </flux:navlist>
            <flux:spacer />
            <!-- Desktop User Menu -->
            <flux:dropdown class="lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        {{-- Allow pages to push additional scripts (e.g., Chart.js) --}}
        @stack('scripts')

        @fluxScripts
    </body>
</html>
