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
        Schema::create('detalle_prestamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamo_id')->constrained('prestamos')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();

            $table->unsignedInteger('cantidad_prestada');
            $table->unsignedInteger('cantidad_devuelta')->default(0);
            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);

            $table->timestamps();
            $table->unique(['prestamo_id', 'item_id']); // 1 línea por item
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_prestamos');
    }
};
