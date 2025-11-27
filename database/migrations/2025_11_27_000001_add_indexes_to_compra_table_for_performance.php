<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega índices a la tabla compra para mejorar el rendimiento
     * de las consultas de filtrado en el Historial de Compras.
     */
    public function up(): void
    {
        Schema::table('compra', function (Blueprint $table) {
            // Índice para filtro por proveedor (usado frecuentemente)
            $table->index('id_proveedor', 'idx_compra_proveedor');

            // Índice para filtro por bodega (usado frecuentemente)
            $table->index('id_bodega', 'idx_compra_bodega');

            // Índice para filtro por fecha (usado en rangos de fechas)
            $table->index('fecha', 'idx_compra_fecha');

            // Índice para búsqueda por número de factura
            $table->index('no_factura', 'idx_compra_factura');

            // Índice para filtro por estado activo/inactivo
            $table->index('activo', 'idx_compra_activo');

            // Índice compuesto para las consultas más comunes:
            // Fecha + Proveedor (usado cuando se filtra por ambos)
            $table->index(['fecha', 'id_proveedor'], 'idx_compra_fecha_proveedor');

            // Índice compuesto: Fecha + Bodega
            $table->index(['fecha', 'id_bodega'], 'idx_compra_fecha_bodega');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compra', function (Blueprint $table) {
            // Eliminar índices en orden inverso
            $table->dropIndex('idx_compra_fecha_bodega');
            $table->dropIndex('idx_compra_fecha_proveedor');
            $table->dropIndex('idx_compra_activo');
            $table->dropIndex('idx_compra_factura');
            $table->dropIndex('idx_compra_fecha');
            $table->dropIndex('idx_compra_bodega');
            $table->dropIndex('idx_compra_proveedor');
        });
    }
};
