<?php

namespace Database\Seeders;

use App\Models\Puesto;
use Illuminate\Database\Seeder;

class PuestoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $puestos = [
            'Colaborador de bodega',
            'Jefe de Bodega',
            'Administrador TI',
            'Colaborador de contabilidad',
        ];

        foreach ($puestos as $puesto) {
            Puesto::firstOrCreate(['nombre' => $puesto]);
        }
    }
}
