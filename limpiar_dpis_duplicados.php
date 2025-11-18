<?php

/**
 * Script para limpiar DPIs duplicados en la tabla persona
 *
 * Este script:
 * 1. Busca personas con DPIs duplicados
 * 2. Mantiene el registro más antiguo (menor ID)
 * 3. Elimina los registros duplicados más recientes
 *
 * IMPORTANTE: Ejecutar ANTES de la migración que agrega el índice único
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Persona;
use Illuminate\Support\Facades\DB;

echo "\n=== LIMPIEZA DE DPIs DUPLICADOS ===\n\n";

try {
    // 1. Buscar DPIs duplicados
    echo "Buscando DPIs duplicados...\n";

    $duplicados = DB::table('persona')
        ->select('dpi', DB::raw('COUNT(*) as cantidad'))
        ->whereNotNull('dpi')
        ->where('dpi', '!=', '')
        ->groupBy('dpi')
        ->having('cantidad', '>', 1)
        ->get();

    if ($duplicados->isEmpty()) {
        echo "✅ No se encontraron DPIs duplicados. La base de datos está limpia.\n";
        echo "\nPuedes ejecutar la migración con: php artisan migrate\n\n";
        exit(0);
    }

    echo "⚠️  Se encontraron " . $duplicados->count() . " DPIs duplicados:\n\n";

    $totalEliminados = 0;

    DB::beginTransaction();

    foreach ($duplicados as $duplicado) {
        echo "DPI: {$duplicado->dpi} ({$duplicado->cantidad} registros)\n";

        // Obtener todas las personas con este DPI
        $personas = Persona::where('dpi', $duplicado->dpi)
            ->orderBy('id', 'asc')
            ->get();

        // Mostrar detalles
        foreach ($personas as $index => $persona) {
            $mantener = $index === 0 ? '✅ MANTENER' : '❌ ELIMINAR';
            echo "  - ID: {$persona->id} | {$persona->nombres} {$persona->apellidos} | {$mantener}\n";
        }

        // Eliminar todos excepto el primero (más antiguo)
        $personasAEliminar = $personas->slice(1);

        foreach ($personasAEliminar as $persona) {
            // Verificar si tiene usuario asociado
            if ($persona->usuario) {
                echo "    ⚠️  ADVERTENCIA: Persona ID {$persona->id} tiene un usuario asociado (ID: {$persona->usuario->id})\n";
                echo "    Se eliminará el usuario también.\n";

                // Eliminar el usuario primero
                $persona->usuario->delete();
            }

            // Verificar si tiene tarjetas de responsabilidad
            $tarjetas = $persona->tarjetasResponsabilidad;
            if ($tarjetas->count() > 0) {
                echo "    ⚠️  ADVERTENCIA: Persona ID {$persona->id} tiene {$tarjetas->count()} tarjeta(s) de responsabilidad\n";
                echo "    Se eliminarán las tarjetas también.\n";

                // Eliminar tarjetas
                foreach ($tarjetas as $tarjeta) {
                    $tarjeta->delete();
                }
            }

            // Eliminar la persona
            $persona->delete();
            $totalEliminados++;
            echo "    ✅ Persona ID {$persona->id} eliminada\n";
        }

        echo "\n";
    }

    DB::commit();

    echo "=== RESUMEN ===\n";
    echo "DPIs duplicados encontrados: {$duplicados->count()}\n";
    echo "Personas eliminadas: {$totalEliminados}\n";
    echo "\n✅ Limpieza completada exitosamente.\n";
    echo "\nAhora puedes ejecutar la migración con: php artisan migrate\n\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
}
