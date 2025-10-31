<?php

namespace Database\Seeders;

use App\Models\TipoTransaccion;
use Illuminate\Database\Seeder;

/**
 * Seeder para Tipos de Transacción
 *
 * Crea los 5 tipos principales de transacciones del sistema de inventario.
 * Estos tipos clasifican el origen de cada movimiento de inventario y son
 * utilizados por la tabla `transaccion` para rastrear la procedencia de
 * cada lote creado.
 *
 * Tipos de transacción:
 * - Compra: Ingreso de productos mediante compra a proveedor
 * - Entrada: Ingreso manual o ajuste de inventario
 * - Devolución: Devolución de productos al proveedor
 * - Traslado: Movimiento de productos entre bodegas
 * - Salida: Egreso de productos del inventario
 */
class TipoTransaccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipos = [
            ['nombre' => 'Compra'],
            ['nombre' => 'Entrada'],
            ['nombre' => 'Devolución'],
            ['nombre' => 'Traslado'],
            ['nombre' => 'Salida'],
        ];

        foreach ($tipos as $tipo) {
            TipoTransaccion::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }

        $this->command->info('✓ Tipos de transacción creados exitosamente.');
    }
}
