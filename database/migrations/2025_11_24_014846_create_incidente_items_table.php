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
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->enum('estado_item', [
                'PERDIDO',
                'DAÑADO',
                'FALTANTE',
                'BAJA',
                'OTRO'
            ]);

            $table->integer('cantidad')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidente_items');
    }
};
