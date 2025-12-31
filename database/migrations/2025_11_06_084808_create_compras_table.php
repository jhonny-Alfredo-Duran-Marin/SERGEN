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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_compra'); // Para el control por día
            $table->string('descripcion');
            $table->string('imagen');
              $table->string('qr')->nullable();
            $table->decimal('costo_total', 10, 2);
            $table->integer('cantidad')->default(1);
            $table->enum('tipo_compra', ['Herramienta', 'Material', 'Insumos', 'Otros'])->default('Material');
            $table->enum('estado_procesamiento', ['Pendiente', 'Resuelto'])->default('Pendiente');
            $table->foreignId('user_id')->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
