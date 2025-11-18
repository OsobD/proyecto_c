<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Configuracion;
use App\Models\Permiso;
use App\Models\Rol;

class RolesPermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear configuraciones básicas
        $configGeneral = Configuracion::create(['nombre' => 'Configuración General']);
        $configInventario = Configuracion::create(['nombre' => 'Configuración Inventario']);

        // Crear permisos básicos
        $permisoAdmin = Permiso::create([
            'nombre' => 'Administrador Total',
            'id_configuracion' => $configGeneral->id,
        ]);

        $permisoInventario = Permiso::create([
            'nombre' => 'Gestión de Inventario',
            'id_configuracion' => $configInventario->id,
        ]);

        $permisoOperador = Permiso::create([
            'nombre' => 'Operador Básico',
            'id_configuracion' => $configGeneral->id,
        ]);

        // Crear roles básicos usando relación many-to-many correcta
        $rolAdmin = Rol::create(['nombre' => 'Administrador']);
        $rolAdmin->permisos()->attach($permisoAdmin->id);

        $rolInventario = Rol::create(['nombre' => 'Gestor de Inventario']);
        $rolInventario->permisos()->attach($permisoInventario->id);

        $rolOperador = Rol::create(['nombre' => 'Operador']);
        $rolOperador->permisos()->attach($permisoOperador->id);
    }
}
