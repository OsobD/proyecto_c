<?php

namespace Database\Seeders;

use App\Models\TipoSalida;
use Illuminate\Database\Seeder;

/**
 * Seeder para Tipos de Salida
 *
 * Crea los subtipos de salidas del inventario. Las salidas representan
 * egresos de productos por diferentes motivos operativos.
 *
 * Tipos de salida:
 * - Salida por Venta: Productos vendidos a clientes
 * - Salida por Uso Interno: Productos consumidos internamente
 * - Salida por Merma: Productos perdidos por deterioro o vencimiento
 * - Salida por Baja: Productos dados de baja por daño irreparable
 * - Salida por Donación: Productos donados a terceros
 * - Salida por Robo/Extravío: Productos perdidos por robo o extravío
 *
 * IMPORTANTE: La tabla `salida` tiene un campo `id_tipo` que referencia
 * esta tabla para clasificar el motivo de la salida.
 */
class TipoSalidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipos = [
            ['nombre' => 'Salida por Venta'],
            ['nombre' => 'Salida por Uso Interno'],
            ['nombre' => 'Salida por Merma'],
            ['nombre' => 'Salida por Baja'],
            ['nombre' => 'Salida por Donación'],
            ['nombre' => 'Salida por Robo/Extravío'],
        ];

        foreach ($tipos as $tipo) {
            TipoSalida::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }

        $this->command->info('✓ Tipos de salida creados exitosamente.');
    }
}
