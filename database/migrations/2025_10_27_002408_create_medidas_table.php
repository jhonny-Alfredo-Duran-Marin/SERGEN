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
        Schema::create('medidas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 150); // p.ej. "Metro", "Kilogramo", "Unidad"
            $table->string('simbolo', 20);      // "m", "kg", "u"
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['descripcion']);
            $table->unique(['simbolo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medidas');
    }
};
