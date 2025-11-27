<?php

use App\Models\Clinic;
use App\Models\Organization;
use App\Models\Patient;
use App\Models\User;
use Livewire\Livewire;

test('patients index page renders', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('patients.index'))
        ->assertStatus(200);
});

test('can create a patient', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->create();
    $clinic = Clinic::factory()->create(['organization_id' => $org->id]);

    Livewire::test(\App\Http\Livewire\Patients\Form::class)
        ->actingAs($user)
        ->set('state.first_name', 'Juan')
        ->set('state.last_name', 'Dela Cruz')
        ->set('state.date_of_birth', '1990-01-01')
        ->set('state.clinic_id', $clinic->id)
        ->call('save')
        ->assertHasNoErrors();

    expect(Patient::where('first_name', 'Juan')->exists())->toBeTrue();
});

test('can update a patient', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->create();
    $clinic = Clinic::factory()->create(['organization_id' => $org->id]);
    $patient = Patient::factory()->create([
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'clinic_id' => $clinic->id,
    ]);

    Livewire::test(\App\Http\Livewire\Patients\Form::class, ['patient' => $patient])
        ->actingAs($user)
        ->set('state.first_name', 'Maria Updated')
        ->call('save')
        ->assertHasNoErrors();

    expect($patient->fresh()->first_name)->toBe('Maria Updated');
});

test('validation works on patient form', function () {
    $user = User::factory()->create();

    Livewire::test(\App\Http\Livewire\Patients\Form::class)
        ->actingAs($user)
        ->set('state.first_name', '')
        ->set('state.last_name', '')
        ->call('save')
        ->assertHasErrors(['state.first_name', 'state.last_name']);
});
