<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('patients') && ! Schema::hasColumn('patients', 'photo')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->string('photo')->nullable()->after('patient_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('patients') && Schema::hasColumn('patients', 'photo')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropColumn('photo');
            });
        }
    }
};
