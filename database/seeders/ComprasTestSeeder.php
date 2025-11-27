<?php

namespace Database\Seeders;

use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\Bodega;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

/**
 * Seeder de Compras de Prueba
 *
 * Crea 20 compras de ejemplo para facilitar testing de paginación
 * y funcionalidades del sistema.
 *
 * IMPORTANTE: Este seeder es OPCIONAL y solo debe usarse en desarrollo.
 */
class ComprasTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener datos necesarios
        $proveedores = Proveedor::where('activo', true)->get();
        $bodegas = Bodega::where('activo', true)->get();
        $usuarios = Usuario::get();

        if ($proveedores->isEmpty() || $bodegas->isEmpty() || $usuarios->isEmpty()) {
            $this->command->error('❌ Error: Se requieren proveedores, bodegas y usuarios existentes.');
            return;
        }

        $seriesPrefijos = ['A', 'B', 'C', 'D', 'E'];

        for ($i = 1; $i <= 20; $i++) {
            $fecha = now()->subDays(rand(1, 180));
            $proveedor = $proveedores->random();
            $bodega = $bodegas->random();
            $usuario = $usuarios->random();

            $total = rand(100, 50000) + (rand(0, 99) / 100);
            $precioFactura = $total * (rand(95, 105) / 100); // Precio factura cercano al total

            $compra = [
                'fecha' => $fecha,
                'no_factura' => str_pad($i, 8, '0', STR_PAD_LEFT),
                'no_serie' => $seriesPrefijos[array_rand($seriesPrefijos)] . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'correlativo' => str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'total' => $total,
                'precio_factura' => $precioFactura,
                'id_proveedor' => $proveedor->id,
                'id_bodega' => $bodega->id,
                'id_usuario' => $usuario->id,
                'activo' => $i <= 18, // 18 activas, 2 inactivas para probar filtros
            ];

            Compra::firstOrCreate(
                [
                    'no_factura' => $compra['no_factura'],
                    'no_serie' => $compra['no_serie']
                ],
                $compra
            );
        }

        $this->command->info('✓ 20 compras de prueba creadas exitosamente.');
    }
}
