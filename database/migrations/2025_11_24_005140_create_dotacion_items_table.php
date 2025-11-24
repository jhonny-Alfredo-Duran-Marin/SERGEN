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

            $table->integer('cantidad');

            $table->enum('estado_item', [
                'EN_USO',        // entregado
                'OK',            // devuelto ok
                'BAJA',          // final de vida útil
                'DANADO',        // devuelto con daño
                'PERDIDO',       // no entregado
            ])->default('EN_USO');

            $table->date('fecha_devolucion')->nullable();
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
