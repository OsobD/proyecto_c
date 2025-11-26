<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migra todos los lotes existentes que tienen id_bodega a la nueva tabla lote_bodega.
     * Cada lote con id_bodega y cantidad > 0 crea un registro en lote_bodega.
     */
    public function up(): void
    {
        // Migrar todos los lotes que tienen bodega asignada
        DB::statement("
            INSERT INTO lote_bodega (id_lote, id_bodega, cantidad, created_at, updated_at)
            SELECT
                id,
                id_bodega,
                cantidad_disponible,
                NOW(),
                NOW()
            FROM lote
            WHERE id_bodega IS NOT NULL
        ");

        // Log de migración
        $count = DB::table('lote_bodega')->count();
        \Log::info("Migración completada: {$count} registros migrados a lote_bodega");
    }

    /**
     * Reverse the migrations.
     *
     * ADVERTENCIA: Esta reversión puede causar pérdida de datos si se han hecho
     * cambios después de la migración (ej: un lote distribuido en múltiples bodegas).
     */
    public function down(): void
    {
        // Restaurar id_bodega en lote desde el primer registro de lote_bodega
        DB::statement("
            UPDATE lote l
            INNER JOIN (
                SELECT id_lote, id_bodega, cantidad
                FROM lote_bodega
                GROUP BY id_lote
                HAVING COUNT(*) = 1
            ) lb ON l.id = lb.id_lote
            SET l.id_bodega = lb.id_bodega
        ");

        // Advertencia para lotes con múltiples bodegas
        $multiLocationLotes = DB::table('lote_bodega')
            ->select('id_lote')
            ->groupBy('id_lote')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($multiLocationLotes->isNotEmpty()) {
            \Log::warning(
                "ADVERTENCIA: " . $multiLocationLotes->count() .
                " lotes están distribuidos en múltiples bodegas y no se pueden revertir completamente."
            );
        }

        // Limpiar tabla lote_bodega
        DB::table('lote_bodega')->truncate();
    }
};
