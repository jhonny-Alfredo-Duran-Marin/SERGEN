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
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->date('fecha')->default(now());
            $table->enum('estado', ['Activo', 'Observado', 'Completo'])->default('Activo')->index();
            $table->enum('tipo_destino', ['Persona', 'Proyecto', 'Otro'])->default('Persona');

            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete();
            $table->foreignId('proyecto_id')->nullable()->constrained('proyectos')->nullOnDelete();
            $table->foreignId('kit_emergencia_id')->nullable()->constrained('kit_emergencias')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // quien registra

            $table->text('nota')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
