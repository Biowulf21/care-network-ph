<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (! Schema::hasColumn('medical_records', 'doctor_id')) {
            Schema::table('medical_records', function (Blueprint $table) {
                $table->foreignId('doctor_id')->nullable()->after('user_id')->constrained('doctors')->nullOnDelete();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('medical_records', 'doctor_id')) {
            Schema::table('medical_records', function (Blueprint $table) {
                $table->dropForeign([$table->getTable().'_doctor_id_foreign'] ?? ['doctor_id']);
                $table->dropColumn('doctor_id');
            });
        }
    }
};
