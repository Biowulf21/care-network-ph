<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (! Schema::hasTable('prescription_items')) {
            Schema::create('prescription_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
                // create inventory_item_id as a nullable unsignedBigInteger; add FK only if inventory_items table exists
                $table->unsignedBigInteger('inventory_item_id')->nullable();
                $table->string('name');
                $table->string('dosage')->nullable();
                $table->string('quantity')->nullable();
                $table->text('instructions')->nullable();
                $table->timestamps();
            });

            // add foreign key constraint to inventory_items if that table exists (some deployments may not have inventory module)
            if (Schema::hasTable('inventory_items')) {
                Schema::table('prescription_items', function (Blueprint $table) {
                    $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->nullOnDelete();
                });
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('prescription_items');
    }
};
