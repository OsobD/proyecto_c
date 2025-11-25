<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Actualiza los índices de rendimiento para reflejar el cambio de 'cantidad' a 'cantidad_disponible'
     * y optimiza las consultas con la nueva tabla lote_bodega
     */
    public function up(): void
    {
        // Eliminar el índice FIFO antiguo que usa 'cantidad'
        Schema::table('lote', function (Blueprint $table) {
            $table->dropIndex('idx_lote_fifo');
        });

        // Crear nuevo índice FIFO usando 'cantidad_disponible'
        DB::statement('CREATE INDEX idx_lote_fifo ON lote(id_producto, cantidad_disponible, fecha_ingreso, estado)');

        // Crear índices adicionales para la tabla lote_bodega
        // Estos índices ya están en la migración de creación de lote_bodega, pero se documentan aquí por referencia:
        // - idx_lote_bodega_lookup (id_lote, id_bodega)
        // - idx_bodega_lotes (id_bodega)
        // - idx_lote_cantidad (id_lote, cantidad)
        // - unique_lote_bodega (id_lote, id_bodega)

        // Crear índice compuesto para consultas frecuentes de stock por bodega
        DB::statement('
            CREATE INDEX idx_lote_bodega_stock ON lote_bodega(id_bodega, cantidad)
            WHERE cantidad > 0
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar el índice de lote_bodega
        Schema::table('lote_bodega', function (Blueprint $table) {
            $table->dropIndex('idx_lote_bodega_stock');
        });

        // Eliminar el nuevo índice FIFO
        Schema::table('lote', function (Blueprint $table) {
            $table->dropIndex('idx_lote_fifo');
        });

        // Recrear el índice FIFO antiguo con 'cantidad'
        DB::statement('CREATE INDEX idx_lote_fifo ON lote(id_bodega, id_producto, cantidad, fecha_ingreso)');
    }
};
