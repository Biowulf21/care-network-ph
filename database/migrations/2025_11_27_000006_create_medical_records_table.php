<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // doctor or delegate
            $table->date('consultation_date')->nullable();
            $table->json('vitals')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->json('medical_history')->nullable();
            $table->json('philhealth')->nullable();
            $table->json('documents_checklist')->nullable();
            $table->json('admission')->nullable();
            $table->json('discharge')->nullable();

            // Enhanced Clinical Data for BlueEHR functionality
            $table->json('emar_data')->nullable(); // Electronic Medical Administration Record
            $table->string('chief_complaint')->nullable();
            $table->text('history_present_illness')->nullable();
            $table->text('physical_examination')->nullable();
            $table->text('assessment_plan')->nullable();
            $table->json('prescriptions')->nullable();
            $table->string('disposition')->nullable();
            $table->string('encounter_type')->default('General Consultation');
            $table->string('consultation_type')->nullable();
            $table->json('allergies')->nullable();
            $table->json('family_history')->nullable();
            $table->json('immunization_history')->nullable();
            $table->json('social_history')->nullable();
            $table->date('next_appointment')->nullable();
            $table->text('provider_notes')->nullable();
            $table->string('diagnosis_codes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
