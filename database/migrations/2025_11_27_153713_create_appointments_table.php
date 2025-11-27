<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // doctor
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('appointment_type')->default('Consultation');
            $table->string('status')->default('scheduled'); // scheduled, confirmed, completed, cancelled
            $table->text('notes')->nullable();
            $table->string('service_type')->nullable();
            $table->string('specialty')->nullable();
            $table->integer('duration')->default(30); // minutes
            $table->boolean('is_urgent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
