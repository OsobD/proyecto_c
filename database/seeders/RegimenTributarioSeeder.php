<?php

namespace Database\Seeders;

use App\Models\RegimenTributario;
use Illuminate\Database\Seeder;

/**
 * Seeder para Regímenes Tributarios
 *
 * Crea los regímenes tributarios válidos para clasificar a los proveedores
 * según la normativa fiscal de Guatemala (SAT).
 *
 * Tipos de régimen:
 * - Régimen General: Contribuyentes con facturación mayor a Q150,000 anuales
 * - Pequeño Contribuyente: Contribuyentes con facturación menor a Q150,000 anuales
 * - Exento: Organizaciones sin fines de lucro, entidades gubernamentales, etc.
 *
 * IMPORTANTE: Cada proveedor debe estar asociado a un régimen tributario.
 */
class RegimenTributarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regimenes = [
            ['nombre' => 'Régimen General'],
            ['nombre' => 'Pequeño Contribuyente'],
            ['nombre' => 'Exento'],
        ];

        foreach ($regimenes as $regimen) {
            RegimenTributario::firstOrCreate(
                ['nombre' => $regimen['nombre']],
                $regimen
            );
        }

        $this->command->info('✓ Regímenes tributarios creados exitosamente.');
    }
}
