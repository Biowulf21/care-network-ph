<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('prescription_items') && ! Schema::hasColumn('prescription_items', 'frequency')) {
            Schema::table('prescription_items', function (Blueprint $table) {
                $table->string('frequency')->nullable()->after('quantity');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('prescription_items') && Schema::hasColumn('prescription_items', 'frequency')) {
            Schema::table('prescription_items', function (Blueprint $table) {
                $table->dropColumn('frequency');
            });
        }
    }
};
