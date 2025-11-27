<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->string('sex')->nullable();
            $table->string('philhealth_number')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            // Enhanced Patient Demographics for BlueEHR functionality
            $table->string('patient_id')->unique(); // BlueHealth-style patient ID\n            $table->string('gender')->nullable();", "oldString": "            $table->string('patient_id')->unique(); // BlueHealth-style patient ID\n            $table->string('name')->virtualAs(\"CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name)\");\n            $table->string('gender')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('region')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->string('philhealth_id')->nullable();
            $table->json('insurance_info')->nullable();
            $table->decimal('height', 8, 2)->nullable(); // in cm
            $table->decimal('weight', 8, 2)->nullable(); // in kg
            $table->string('blood_type')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
