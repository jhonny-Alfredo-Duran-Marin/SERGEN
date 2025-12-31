<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidentes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->enum('tipo', ['PRESTAMO', 'DOTACION'])->default('PRESTAMO');
            $table->enum('estado', ['ACTIVO', 'EN_PROCESO', 'COMPLETADO'])
                ->default('ACTIVO');
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->date('fecha_incidente');
            $table->text('descripcion')->nullable();
            $table->foreignId('prestamo_id')->nullable()->constrained('prestamos')->nullOnDelete();
            $table->foreignId('dotacion_id')->nullable()->constrained('dotacions')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidentes');
    }
};
