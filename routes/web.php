<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Models\MedicalRecord;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // EMAR & dashboards
    Route::get('/dashboard/superadmin', [\App\Http\Controllers\DashboardController::class, 'superadmin'])->name('dashboard.superadmin');
    Route::get('/dashboard/admin', [\App\Http\Controllers\DashboardController::class, 'admin'])->name('dashboard.admin');
    Route::get('/dashboard/delegate', [\App\Http\Controllers\DashboardController::class, 'delegate'])->name('dashboard.delegate');

    // Management pages
    Route::get('/users', \App\Http\Livewire\Users\Index::class)->name('users.index');
    Route::get('/users/create', \App\Livewire\Users\Form::class)->name('users.create');
    Route::get('/users/{user}/edit', \App\Livewire\Users\Form::class)->name('users.edit');

    Route::get('/organizations', \App\Http\Livewire\Organizations\Index::class)->name('organizations.index');
    Route::get('/organizations/create', \App\Livewire\Organizations\Form::class)->name('organizations.create');
    Route::get('/organizations/{organization}/edit', \App\Livewire\Organizations\Form::class)->name('organizations.edit');

    Route::get('/clinics', \App\Http\Livewire\Clinics\Index::class)->name('clinics.index');
    Route::get('/clinics/create', \App\Livewire\Clinics\Form::class)->name('clinics.create');
    Route::get('/clinics/{clinic}/edit', \App\Livewire\Clinics\Form::class)->name('clinics.edit');

    // Livewire-powered lists
    Route::get('/patients', \App\Http\Livewire\Patients\Index::class)->name('patients.index');
    Route::get('/patients/create', \App\Http\Livewire\Patients\Form::class)->name('patients.create');
    Route::get('/patients/{patient}/edit', \App\Http\Livewire\Patients\Form::class)->name('patients.edit');
    Route::get('/patients/{patient}', \App\Livewire\Patients\Profile::class)->name('patients.profile');
    Route::get('/medical-records', \App\Http\Livewire\MedicalRecords\Index::class)->name('medical-records.index');
    Route::get('/medical-records/create', \App\Http\Livewire\MedicalRecords\Form::class)->name('medical-records.create');
    Route::get('/medical-records/{record}', \App\Http\Livewire\MedicalRecords\Show::class)->name('medical-records.show');
    Route::get('/medical-records/{record}/edit', \App\Http\Livewire\MedicalRecords\Form::class)->name('medical-records.edit');
    // Per-patient medical history viewer (with inline edit)
    Route::get('/patients/{patient}/medical-history', \App\Http\Livewire\MedicalRecords\History::class)->name('patients.medical-history');

    // Appointment management
    Route::get('/appointments', \App\Livewire\Appointments\Index::class)->name('appointments.index');
    Route::get('/appointments/calendar', \App\Livewire\Appointments\Calendar::class)->name('appointments.calendar');

    // Reports and analytics
    Route::get('/reports', \App\Livewire\Reports\Analytics::class)->name('reports.analytics');
});

// Printable prescription route
Route::get('/medical-records/{record}/prescription', function (MedicalRecord $record) {
    // simple auth check
    if (! auth()->check()) {
        abort(403);
    }

    return view('prescriptions.print', ['record' => $record]);
})->middleware('auth')->name('medical-records.prescription');

// Doctors CRUD (separate auth group to avoid editing the main block)
Route::middleware(['auth'])->group(function () {
    Route::get('/doctors', \App\Http\Livewire\Doctors\Index::class)->name('doctors.index');
    Route::get('/doctors/create', \App\Http\Livewire\Doctors\Form::class)->name('doctors.create');
    Route::get('/doctors/{doctor}/edit', \App\Http\Livewire\Doctors\Form::class)->name('doctors.edit');
});
