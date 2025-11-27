<?php

use App\Models\Clinic;
use App\Models\MedicalRecord;
use App\Models\Organization;
use App\Models\Patient;
use App\Models\User;
use Livewire\Livewire;

test('medical records index page renders', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('medical-records.index'))
        ->assertStatus(200);
});

test('can create a medical record', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->create();
    $clinic = Clinic::factory()->create(['organization_id' => $org->id]);
    $patient = Patient::factory()->create(['clinic_id' => $clinic->id]);

    Livewire::test(\App\Http\Livewire\MedicalRecords\Form::class)
        ->actingAs($user)
        ->set('state.patient_id', $patient->id)
        ->set('state.clinic_id', $clinic->id)
        ->set('state.consultation_date', '2025-11-28')
        ->set('state.chief_complaint', 'Fever and cough')
        ->set('state.diagnosis', 'Upper Respiratory Infection')
        ->call('save')
        ->assertHasNoErrors();

    expect(MedicalRecord::where('patient_id', $patient->id)->exists())->toBeTrue();
});

test('can update a medical record', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->create();
    $clinic = Clinic::factory()->create(['organization_id' => $org->id]);
    $patient = Patient::factory()->create(['clinic_id' => $clinic->id]);
    $record = MedicalRecord::factory()->create([
        'patient_id' => $patient->id,
        'clinic_id' => $clinic->id,
        'diagnosis' => 'Original diagnosis',
    ]);

    Livewire::test(\App\Http\Livewire\MedicalRecords\Form::class, ['record' => $record])
        ->actingAs($user)
        ->set('state.diagnosis', 'Updated diagnosis')
        ->call('save')
        ->assertHasNoErrors();

    expect($record->fresh()->diagnosis)->toBe('Updated diagnosis');
});

test('validation works on medical record form', function () {
    $user = User::factory()->create();

    Livewire::test(\App\Http\Livewire\MedicalRecords\Form::class)
        ->actingAs($user)
        ->set('state.patient_id', null)
        ->set('state.clinic_id', null)
        ->call('save')
        ->assertHasErrors(['state.patient_id', 'state.clinic_id']);
});
