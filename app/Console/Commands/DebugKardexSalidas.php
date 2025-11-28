<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Salida;
use Carbon\Carbon;

class DebugKardexSalidas extends Command
{
    protected $signature = 'debug:kardex-salidas';
    protected $description = 'Debug por qué las salidas no aparecen en el Kardex';

    public function handle()
    {
        $this->info('=== DEBUG KARDEX - SALIDAS ===');
        $this->newLine();

        // Ver todas las salidas
        $this->info('1. Total de salidas en la BD:');
        $totalSalidas = Salida::count();
        $this->line("   Total: {$totalSalidas}");
        $this->newLine();

        // Ver salidas recientes
        $this->info('2. Últimas 5 salidas:');
        $salidas = Salida::orderBy('id', 'desc')->limit(5)->get();

        foreach ($salidas as $salida) {
            $this->line("   ID: {$salida->id}");
            $this->line("   Fecha: {$salida->fecha}");
            $this->line("   Activo: " . ($salida->activo === null ? 'NULL' : ($salida->activo ? 'true' : 'false')));
            $this->line("   Bodega ID: {$salida->id_bodega}");
            $this->line("   Usuario ID: {$salida->id_usuario}");
            $this->line("   Detalles: " . $salida->detalles()->count());
            $this->line("   ---");
        }
        $this->newLine();

        // Ver salidas con activo = true
        $this->info('3. Salidas con activo = true:');
        $salidasActivas = Salida::where('activo', true)->count();
        $this->line("   Total: {$salidasActivas}");
        $this->newLine();

        // Ver salidas con activo = null
        $this->info('4. Salidas con activo = NULL:');
        $salidasNull = Salida::whereNull('activo')->count();
        $this->line("   Total: {$salidasNull}");
        $this->newLine();

        // Ver salidas con activo = false
        $this->info('5. Salidas con activo = false:');
        $salidasInactivas = Salida::where('activo', false)->count();
        $this->line("   Total: {$salidasInactivas}");
        $this->newLine();

        // Verificar la salida específica del 28/11/2025
        $this->info('6. Buscar salida del 28/11/2025:');
        $fecha = '2025-11-28';
        $salidaEspecifica = Salida::whereDate('fecha', $fecha)->get();

        if ($salidaEspecifica->count() > 0) {
            $this->line("   Encontradas: " . $salidaEspecifica->count());
            foreach ($salidaEspecifica as $s) {
                $this->line("   ID: {$s->id}, Correlativo: {$s->id}, Activo: " . ($s->activo === null ? 'NULL' : ($s->activo ? 'true' : 'false')));

                // Ver detalles
                $detalles = $s->detalles;
                $this->line("   Detalles: " . $detalles->count());
                foreach ($detalles as $det) {
                    $producto = $det->producto;
                    $lote = $det->lote;
                    $this->line("     - Producto: " . ($producto ? $producto->descripcion : 'NULL'));
                    $this->line("       Cantidad: {$det->cantidad}");
                    $this->line("       Lote: " . ($lote ? $lote->id : 'NULL'));
                }
            }
        } else {
            $this->error("   No se encontró ninguna salida del 28/11/2025");
        }
        $this->newLine();

        // Probar la query del servicio
        $this->info('7. Probar query del servicio (últimos 30 días):');
        $fechaInicio = Carbon::now()->subDays(30)->format('Y-m-d');
        $fechaFin = Carbon::now()->format('Y-m-d');

        $this->line("   Rango: {$fechaInicio} a {$fechaFin}");

        // Query ANTIGUA (con where)
        $queryAntigua = Salida::where(function($q) {
            $q->where('activo', true)
              ->orWhereNull('activo');
        })
        ->where('fecha', '>=', $fechaInicio)
        ->where('fecha', '<=', $fechaFin);
        $this->line("   Con WHERE (antigua): " . $queryAntigua->count());

        // Query NUEVA (con whereDate)
        $queryNueva = Salida::where(function($q) {
            $q->where('activo', true)
              ->orWhereNull('activo');
        })
        ->whereDate('fecha', '>=', $fechaInicio)
        ->whereDate('fecha', '<=', $fechaFin);
        $this->line("   Con WHEREDATE (nueva): " . $queryNueva->count());

        return 0;
    }
}
