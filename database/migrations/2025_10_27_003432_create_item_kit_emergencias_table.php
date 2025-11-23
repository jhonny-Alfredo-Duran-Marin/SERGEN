<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kit_emergencia_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kit_emergencia_id')->constrained('kit_emergencias')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();

            $table->unique(['kit_emergencia_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kit_emergencia_item');
    }
};
