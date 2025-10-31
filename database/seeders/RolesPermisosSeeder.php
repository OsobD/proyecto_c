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

        // NOTA: Bitácoras comentadas porque el modelo extendido requiere campos adicionales
        // que no están en el schema base SQL. Se implementarán cuando se defina
        // la funcionalidad completa de auditoría.
        // $bitacoraGeneral = Bitacora::create();
        // $bitacoraInventario = Bitacora::create();

        // Crear permisos básicos (sin bitácoras por ahora)
        $permisoAdmin = Permiso::create([
            'nombre' => 'Administrador Total',
            'id_configuracion' => $configGeneral->id,
            'id_bitacora' => null, // Será implementado cuando se active auditoría
        ]);

        $permisoInventario = Permiso::create([
            'nombre' => 'Gestión de Inventario',
            'id_configuracion' => $configInventario->id,
            'id_bitacora' => null, // Será implementado cuando se active auditoría
        ]);

        $permisoOperador = Permiso::create([
            'nombre' => 'Operador Básico',
            'id_configuracion' => $configGeneral->id,
            'id_bitacora' => null, // Será implementado cuando se active auditoría
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
