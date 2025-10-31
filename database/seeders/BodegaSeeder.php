<?php

namespace Database\Seeders;

use App\Models\Bodega;
use Illuminate\Database\Seeder;

/**
 * Seeder para Bodegas
 *
 * Crea las bodegas (almacenes físicos) iniciales del sistema.
 * Las bodegas son los lugares físicos donde se almacenan los productos
 * y son requeridas para poder registrar compras, entradas, salidas y
 * traslados de inventario.
 *
 * IMPORTANTE: Sin al menos una bodega activa, el sistema de compras
 * no puede funcionar ya que no hay destino para los productos.
 */
class BodegaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bodegas = [
            [
                'nombre' => 'Bodega Principal',
                'activo' => true,
            ],
            [
                'nombre' => 'Bodega Secundaria',
                'activo' => true,
            ],
            [
                'nombre' => 'Almacén Temporal',
                'activo' => true,
            ],
        ];

        foreach ($bodegas as $bodega) {
            Bodega::firstOrCreate(
                ['nombre' => $bodega['nombre']],
                $bodega
            );
        }

        $this->command->info('✓ Bodegas creadas exitosamente.');
    }
}
