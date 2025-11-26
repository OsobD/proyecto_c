<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Esta tabla representa la distribución de un lote en diferentes bodegas.
     * Un lote puede estar distribuido en múltiples bodegas simultáneamente.
     */
    public function up(): void
    {
        Schema::create('lote_bodega', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lote');
            $table->unsignedBigInteger('id_bodega');
            $table->integer('cantidad')->default(0)->comment('Cantidad de este lote en esta bodega');
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_lote')
                ->references('id')
                ->on('lote')
                ->onDelete('cascade');

            $table->foreign('id_bodega')
                ->references('id')
                ->on('bodega')
                ->onDelete('cascade');

            // Índices para mejorar rendimiento
            $table->index(['id_lote', 'id_bodega'], 'idx_lote_bodega_lookup');
            $table->index('id_bodega', 'idx_bodega_lotes');
            $table->index(['id_lote', 'cantidad'], 'idx_lote_cantidad');

            // Un lote no puede estar duplicado en la misma bodega
            $table->unique(['id_lote', 'id_bodega'], 'unique_lote_bodega');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lote_bodega');
    }
};
