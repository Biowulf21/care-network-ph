<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('medical_records') && Schema::hasColumn('medical_records', 'prescriptions')) {
            Schema::table('medical_records', function (Blueprint $table) {
                $table->dropColumn('prescriptions');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('medical_records') && ! Schema::hasColumn('medical_records', 'prescriptions')) {
            Schema::table('medical_records', function (Blueprint $table) {
                $table->json('prescriptions')->nullable()->after('emar_data');
            });
        }
    }
};
