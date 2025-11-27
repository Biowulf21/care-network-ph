<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

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

    // Livewire-powered lists
    Route::get('/patients', \App\Http\Livewire\Patients\Index::class)->name('patients.index');
    Route::get('/patients/create', \App\Http\Livewire\Patients\Form::class)->name('patients.create');
    Route::get('/patients/{patient}/edit', \App\Http\Livewire\Patients\Form::class)->name('patients.edit');
    Route::get('/medical-records', \App\Http\Livewire\MedicalRecords\Index::class)->name('medical-records.index');
    Route::get('/medical-records/create', \App\Http\Livewire\MedicalRecords\Form::class)->name('medical-records.create');
    Route::get('/medical-records/{record}/edit', \App\Http\Livewire\MedicalRecords\Form::class)->name('medical-records.edit');
});
