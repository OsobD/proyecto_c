<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

/**
 * Seeder para Categorías de Productos
 *
 * Crea las categorías iniciales para clasificar productos en el sistema
 * de inventario. Las categorías permiten organizar los productos y
 * facilitan la búsqueda y generación de reportes.
 *
 * IMPORTANTE: Toda creación de producto requiere una categoría asignada.
 */
class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categorias = [
            [
                'nombre' => 'General',
                'activo' => true,
            ],
            [
                'nombre' => 'Materiales de Oficina',
                'activo' => true,
            ],
            [
                'nombre' => 'Equipos Electrónicos',
                'activo' => true,
            ],
            [
                'nombre' => 'Mobiliario',
                'activo' => true,
            ],
            [
                'nombre' => 'Herramientas',
                'activo' => true,
            ],
        ];

        foreach ($categorias as $categoria) {
            Categoria::firstOrCreate(
                ['nombre' => $categoria['nombre']],
                $categoria
            );
        }

        $this->command->info('✓ Categorías de productos creadas exitosamente.');
    }
}
