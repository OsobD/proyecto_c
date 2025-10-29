<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Configuracion;
use App\Models\Bitacora;
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

        // Crear bitácoras básicas
        $bitacoraGeneral = Bitacora::create();
        $bitacoraInventario = Bitacora::create();

        // Crear permisos básicos
        $permisoAdmin = Permiso::create([
            'nombre' => 'Administrador Total',
            'id_configuracion' => $configGeneral->id,
            'id_bitacora' => $bitacoraGeneral->id,
        ]);

        $permisoInventario = Permiso::create([
            'nombre' => 'Gestión de Inventario',
            'id_configuracion' => $configInventario->id,
            'id_bitacora' => $bitacoraInventario->id,
        ]);

        $permisoOperador = Permiso::create([
            'nombre' => 'Operador Básico',
            'id_configuracion' => $configGeneral->id,
            'id_bitacora' => $bitacoraGeneral->id,
        ]);

        // Crear roles básicos
        Rol::create([
            'nombre' => 'Administrador',
            'id_permiso' => $permisoAdmin->id,
        ]);

        Rol::create([
            'nombre' => 'Gestor de Inventario',
            'id_permiso' => $permisoInventario->id,
        ]);

        Rol::create([
            'nombre' => 'Operador',
            'id_permiso' => $permisoOperador->id,
        ]);
    }
}
