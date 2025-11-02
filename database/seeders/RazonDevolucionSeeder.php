<?php

namespace Database\Seeders;

use App\Models\RazonDevolucion;
use Illuminate\Database\Seeder;

/**
 * Seeder para Razones de Devolución
 *
 * Crea el catálogo de razones por las cuales se puede realizar una devolución.
 * Estas razones complementan el campo `motivo` (texto libre) de la devolución
 * proporcionando categorías predefinidas para análisis y reportes.
 *
 * Razones de devolución:
 * - Término laboral: El empleado finalizó su relación laboral
 * - Mal estado / Deterioro: El producto se devuelve por estar dañado o deteriorado
 * - Sobrante de requisición: Se pidieron más insumos de los necesarios
 * - Cambio de área: El empleado cambió de área y devuelve equipo específico
 * - Reubicación de personal: Cambio de ubicación física del personal
 * - Equipo innecesario: El equipo ya no es necesario para las tareas asignadas
 *
 * IMPORTANTE: La tabla `devolucion` tiene un campo `id_razon_devolucion` que referencia
 * esta tabla para clasificar la razón de la devolución.
 */
class RazonDevolucionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $razones = [
            ['nombre' => 'Término laboral'],
            ['nombre' => 'Mal estado / Deterioro'],
            ['nombre' => 'Sobrante de requisición'],
            ['nombre' => 'Cambio de área'],
            ['nombre' => 'Reubicación de personal'],
            ['nombre' => 'Equipo innecesario'],
        ];

        foreach ($razones as $razon) {
            RazonDevolucion::firstOrCreate(
                ['nombre' => $razon['nombre']],
                $razon
            );
        }

        $this->command->info('✓ Razones de devolución creadas exitosamente.');
    }
}
