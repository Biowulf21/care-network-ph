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
        if (Schema::hasTable('patients') && ! Schema::hasColumn('patients', 'gender')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->string('gender')->nullable()->after('patient_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('patients') && Schema::hasColumn('patients', 'gender')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropColumn('gender');
            });
        }
    }
};
