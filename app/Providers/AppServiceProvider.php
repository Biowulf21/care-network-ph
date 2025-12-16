<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Explicitly register Livewire dashboard components to avoid missing-component errors.
        if (class_exists(\App\Http\Livewire\Dashboard\Admin::class)) {
            Livewire::component('dashboard.admin', \App\Http\Livewire\Dashboard\Admin::class);
        }
        if (class_exists(\App\Http\Livewire\Dashboard\Superadmin::class)) {
            Livewire::component('dashboard.superadmin', \App\Http\Livewire\Dashboard\Superadmin::class);
        }
        if (class_exists(\App\Http\Livewire\Dashboard\Delegate::class)) {
            Livewire::component('dashboard.delegate', \App\Http\Livewire\Dashboard\Delegate::class);
        }
        
        // Register core EMR Livewire components explicitly to avoid any registry mismatches
        if (class_exists(\App\Http\Livewire\Patients\Form::class)) {
            Livewire::component('app.http.livewire.patients.form', \App\Http\Livewire\Patients\Form::class);
            Livewire::component('patients.form', \App\Http\Livewire\Patients\Form::class);
        }
        
        if (class_exists(\App\Http\Livewire\Patients\Index::class)) {
            Livewire::component('patients.index', \App\Http\Livewire\Patients\Index::class);
        }
        
        if (class_exists(\App\Http\Livewire\MedicalRecords\Form::class)) {
            Livewire::component('app.http.livewire.medicalrecords.form', \App\Http\Livewire\MedicalRecords\Form::class);
            Livewire::component('medical-records.form', \App\Http\Livewire\MedicalRecords\Form::class);
        }

        if (class_exists(\App\Http\Livewire\Organizations\Form::class)) {
            Livewire::component('organizations.form', \App\Http\Livewire\Organizations\Form::class);
            Livewire::component('app.http.livewire.organizations.form', \App\Http\Livewire\Organizations\Form::class);
        }

        if (class_exists(\App\Http\Livewire\Doctors\Form::class)) {
            Livewire::component('doctors.form', \App\Http\Livewire\Doctors\Form::class);
            Livewire::component('app.http.livewire.doctors.form', \App\Http\Livewire\Doctors\Form::class);
        }

        if (class_exists(\App\Http\Livewire\Doctors\Index::class)) {
            Livewire::component('doctors.index', \App\Http\Livewire\Doctors\Index::class);
        }

        if (class_exists(\App\Http\Livewire\Organizations\Index::class)) {
            Livewire::component('organizations.index', \App\Http\Livewire\Organizations\Index::class);
        }
        
        if (class_exists(\App\Livewire\Appointments\Index::class)) {
            Livewire::component('appointments.index', \App\Livewire\Appointments\Index::class);
        }
        
        if (class_exists(\App\Livewire\Appointments\Calendar::class)) {
            Livewire::component('appointments.calendar', \App\Livewire\Appointments\Calendar::class);
        }

        if (class_exists(\App\Http\Livewire\SearchableDropdown::class)) {
            Livewire::component('searchable-dropdown', \App\Http\Livewire\SearchableDropdown::class);
        }
    }
}
