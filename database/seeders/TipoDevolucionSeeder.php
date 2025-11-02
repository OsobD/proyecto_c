<?php

namespace Database\Seeders;

use App\Models\TipoDevolucion;
use Illuminate\Database\Seeder;

/**
 * Seeder para Tipos de Devolución
 *
 * Crea los tipos de devolución que se pueden registrar en el sistema.
 *
 * Tipos de devolución:
 * - Normal: Devolución estándar de productos asignados en tarjeta de responsabilidad
 * - Equipo No Registrado: Devolución de equipo previo al sistema que no está registrado
 * - Insumos No Utilizados: Devolución de insumos solicitados pero no utilizados
 *
 * IMPORTANTE: La tabla `devolucion` tiene un campo `id_tipo_devolucion` que referencia
 * esta tabla para clasificar el tipo de devolución.
 */
class TipoDevolucionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipos = [
            ['nombre' => 'Normal'],
            ['nombre' => 'Equipo No Registrado'],
            ['nombre' => 'Insumos No Utilizados'],
        ];

        foreach ($tipos as $tipo) {
            TipoDevolucion::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }

        $this->command->info('✓ Tipos de devolución creados exitosamente.');
    }
}
