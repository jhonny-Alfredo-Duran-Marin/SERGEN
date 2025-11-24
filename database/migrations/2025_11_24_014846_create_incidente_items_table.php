<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidente_items', function (Blueprint $table) {

            $table->id();
            $table->foreignId('incidente_id')->constrained('incidentes')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('prestamo_id')->nullable()->constrained('prestamos')->nullOnDelete();
            $table->foreignId('dotacion_id')->nullable()->constrained('dotacions')->nullOnDelete();

            $table->enum('estado_item', [
                'PERDIDO',
                'DANADO',
                'NO_DEVUELTO',
                'BAJA',
                'OTRO'
            ]);

            $table->integer('cantidad')->default(1);
            $table->text('observacion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidente_items');
    }
};
