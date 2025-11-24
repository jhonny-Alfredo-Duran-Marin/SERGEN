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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->nullable()->cascadeOnDelete();
             $table->string('accion', 255);
            $table->enum('tipo', ['Ingreso', 'Egreso']); // prestamo = Egreso, devolucion = Ingreso
            $table->integer('cantidad'); // positivo
            $table->dateTime('fecha')->useCurrent();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('prestamo_id')->nullable()->constrained('prestamos')->nullOnDelete();
            $table->foreignId('devolucion_id')->nullable()->constrained('devoluciones')->nullOnDelete();
            $table->string('nota', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
