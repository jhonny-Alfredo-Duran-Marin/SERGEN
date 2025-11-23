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
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamo_id')->constrained('prestamos')->cascadeOnDelete();
            $table->enum('estado', ['Pendiente', 'Parcial', 'Completa'])->default('Pendiente');
            $table->timestamp('fecha')->useCurrent();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('nota')->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_devoluciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devolucion_id')->constrained('devoluciones')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->unsignedInteger('cantidad'); // devuelta en ESTA devolución
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('detalle_devoluciones');
        Schema::dropIfExists('devoluciones');
    }
};
