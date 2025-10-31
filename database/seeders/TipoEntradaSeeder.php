<?php

namespace Database\Seeders;

use App\Models\TipoEntrada;
use Illuminate\Database\Seeder;

/**
 * Seeder para Tipos de Entrada
 *
 * Crea los subtipos de entradas al inventario. A diferencia de las compras
 * (que tienen su propia tabla), las entradas son ingresos manuales al
 * inventario por diversas causas.
 *
 * Tipos de entrada:
 * - Entrada por Donación: Productos recibidos como donación
 * - Entrada por Ajuste de Inventario: Correcciones de stock por conteo físico
 * - Entrada por Producción Interna: Productos fabricados internamente
 * - Entrada por Devolución de Cliente: Productos devueltos por clientes
 *
 * IMPORTANTE: La tabla `entrada` tiene un campo `id_tipo` que referencia
 * esta tabla para clasificar el motivo de la entrada.
 */
class TipoEntradaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipos = [
            ['nombre' => 'Entrada por Donación'],
            ['nombre' => 'Entrada por Ajuste de Inventario'],
            ['nombre' => 'Entrada por Producción Interna'],
            ['nombre' => 'Entrada por Devolución de Cliente'],
        ];

        foreach ($tipos as $tipo) {
            TipoEntrada::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }

        $this->command->info('✓ Tipos de entrada creados exitosamente.');
    }
}
