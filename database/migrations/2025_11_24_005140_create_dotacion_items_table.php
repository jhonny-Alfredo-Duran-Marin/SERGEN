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
        Schema::create('dotacion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dotacion_id')->constrained('dotacions')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->date('fecha_entrega');
            $table->integer('cantidad');

            $table->enum('estado_item', [
                'USO_PROPIO',
                'DE_VENTA',
                'COMPRADO',
            ])->default('USO_PROPIO');

            $table->date('fecha_siguiente')->nullable();
            $table->text('observacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dotacion_items');
    }
};
