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
        Schema::create('prestamo_incidentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamo_id')->constrained('prestamos')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('tipo', ['Falta', 'Daño', 'Pérdida', 'Otro'])->default('Otro')->index();
            $table->text('nota')->nullable();

            $table->timestamps();
            $table->index(['prestamo_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamo_incidentes');
    }
};
