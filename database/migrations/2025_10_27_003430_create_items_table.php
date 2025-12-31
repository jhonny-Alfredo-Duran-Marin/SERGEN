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
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('medida_id')->constrained('medidas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('codigo', 50)->unique();
            $table->string('descripcion', 255);
            $table->string('fabricante', 150)->nullable();

            $table->unsignedInteger('cantidad')->default(0);
            $table->unsignedInteger('piezas')->default(0);

            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->enum('estado', ['Activo', 'Pasivo','Disponible','Prestado','Dotado','Observacion','Baja','En_Kit'])->default('Activo')->index();
            $table->enum('tipo', ['Herramienta', 'Material', 'Dotacion'])->default('Material')->index();

            $table->string('ubicacion', 250)->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->string('imagen_path', 255)->nullable();
            $table->string('imagen_thumb', 255)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['categoria_id', 'medida_id', 'area_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
