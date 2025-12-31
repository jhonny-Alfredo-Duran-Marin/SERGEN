<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidente_devolucions', function (Blueprint $table) {

            $table->id();
            $table->foreignId('incident_id')->constrained('incidentes')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();

            $table->integer('cantidad_devuelta');

            $table->enum('resultado', [
                'DEVUELTO_OK',
                'DEVUELTO_DANADO',
                'NO_RECUPERADO',
                'REPARABLE'
            ]);
            $table->enum('tipo', ['Perdido', 'Dañado']);
            $table->text('observacion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidente_devolucions');
    }
};
