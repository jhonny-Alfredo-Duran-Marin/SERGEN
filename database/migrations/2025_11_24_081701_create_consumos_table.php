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
        Schema::create('consumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('prestamo_id')->nullable()->constrained('prestamos');
            $table->foreignId('persona_id')->nullable()->constrained('personas');
            $table->foreignId('proyecto_id')->nullable()->constrained('proyectos');
            $table->integer('cantidad_consumida');
            $table->float('precio_unitario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumos');
    }
};
