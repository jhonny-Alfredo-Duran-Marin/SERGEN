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
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 50)->unique();
            $table->text('descripcion');
            $table->string('empresa', 150);
            $table->string('orden_compra', 100)->nullable();
            $table->string('sitio', 150)->nullable();

            $table->decimal('monto', 12, 2)->unsigned()->default(0);
            $table->boolean('es_facturado')->default(false);

            $table->enum('estado', ['Abierto', 'Cerrado'])->default('Abierto');

            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();

            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};
