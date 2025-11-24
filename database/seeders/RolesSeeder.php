<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los IDs de permisos para asignarlos por nombre
        $permisos = DB::table('permiso')->pluck('id', 'nombre')->toArray();

        // =====================================
        // ROL 1: Colaborador de Bodega
        // =====================================
        $colaboradorBodegaId = DB::table('rol')->insertGetId([
            'nombre' => 'Colaborador de Bodega',
            'descripcion' => 'Usuario operativo que registra movimientos diarios de inventario. Puede crear registros directamente, pero ediciones requieren aprobación.',
            'es_sistema' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Permisos del Colaborador de Bodega
        $permisosColaborador = [
            // Compras
            'compras.acceder',
            'compras.crear',
            'compras.editar', // Con aprobación

            // Traslados
            'traslados.acceder',
            'traslados.crear',
            'traslados.editar', // Con aprobación

            // Requisiciones
            'requisiciones.acceder',
            'requisiciones.crear',
            'requisiciones.editar', // Con aprobación

            // Devoluciones
            'devoluciones.acceder',
            'devoluciones.crear',
            'devoluciones.editar', // Con aprobación

            // Productos
            'productos.acceder',
            'productos.crear',
            'productos.editar', // Con aprobación

            // Categorías
            'categorias.acceder',
            'categorias.crear',
            'categorias.editar', // Con aprobación

            // Proveedores
            'proveedores.acceder',
            'proveedores.crear',
            'proveedores.editar', // Con aprobación

            // Personas
            'personas.acceder',
            'personas.crear',
            'personas.editar', // Con aprobación

            // Bodegas (solo lectura)
            'bodegas.acceder',

            // Tarjetas (solo lectura)
            'tarjetas.acceder',

            // Aprobaciones (ver propias)
            'aprobaciones.ver',
        ];

        foreach ($permisosColaborador as $permisoNombre) {
            if (isset($permisos[$permisoNombre])) {
                DB::table('rol_permiso')->insert([
                    'id_rol' => $colaboradorBodegaId,
                    'id_permiso' => $permisos[$permisoNombre],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // =====================================
        // ROL 2: Jefe de Bodega
        // =====================================
        $jefeBodegaId = DB::table('rol')->insertGetId([
            'nombre' => 'Jefe de Bodega',
            'descripcion' => 'Supervisa operaciones de bodega y aprueba cambios. Puede editar directamente sin aprobación y gestionar bodegas y tarjetas.',
            'es_sistema' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Permisos del Jefe de Bodega (hereda de Colaborador + adicionales)
        $permisosJefe = [
            // Compras (con edición y eliminación)
            'compras.acceder',
            'compras.crear',
            'compras.editar',
            'compras.editar.sin_aprobacion',
            'compras.eliminar',
            'compras.aprobar',

            // Traslados (con edición y eliminación)
            'traslados.acceder',
            'traslados.crear',
            'traslados.editar',
            'traslados.editar.sin_aprobacion',
            'traslados.eliminar',
            'traslados.aprobar',

            // Requisiciones (con edición y eliminación)
            'requisiciones.acceder',
            'requisiciones.crear',
            'requisiciones.editar',
            'requisiciones.editar.sin_aprobacion',
            'requisiciones.eliminar',
            'requisiciones.aprobar',

            // Devoluciones (con edición y eliminación)
            'devoluciones.acceder',
            'devoluciones.crear',
            'devoluciones.editar',
            'devoluciones.editar.sin_aprobacion',
            'devoluciones.eliminar',
            'devoluciones.aprobar',

            // Productos (con edición directa)
            'productos.acceder',
            'productos.crear',
            'productos.editar.sin_aprobacion',
            'productos.eliminar',

            // Categorías (con edición directa)
            'categorias.acceder',
            'categorias.crear',
            'categorias.editar.sin_aprobacion',
            'categorias.eliminar',

            // Proveedores (con edición directa)
            'proveedores.acceder',
            'proveedores.crear',
            'proveedores.editar.sin_aprobacion',
            'proveedores.eliminar',

            // Personas (con edición directa)
            'personas.acceder',
            'personas.crear',
            'personas.editar.sin_aprobacion',
            'personas.eliminar',

            // Bodegas (gestión completa)
            'bodegas.acceder',
            'bodegas.crear',
            'bodegas.editar',
            'bodegas.eliminar',

            // Tarjetas (gestión completa)
            'tarjetas.acceder',
            'tarjetas.crear',
            'tarjetas.editar',
            'tarjetas.eliminar',

            // Reportes
            'reportes.acceder',
            'reportes.exportar',

            // Bitácora
            'bitacora.acceder',

            // Aprobaciones
            'aprobaciones.ver',
            'aprobaciones.aprobar',
        ];

        foreach ($permisosJefe as $permisoNombre) {
            if (isset($permisos[$permisoNombre])) {
                DB::table('rol_permiso')->insert([
                    'id_rol' => $jefeBodegaId,
                    'id_permiso' => $permisos[$permisoNombre],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // =====================================
        // ROL 3: Colaborador de Contabilidad
        // =====================================
        $colaboradorContabilidadId = DB::table('rol')->insertGetId([
            'nombre' => 'Colaborador de Contabilidad',
            'descripcion' => 'Consulta reportes y bitácora para auditoría. Solo lectura, no puede crear, editar o eliminar nada.',
            'es_sistema' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Permisos del Colaborador de Contabilidad (solo lectura)
        $permisosContabilidad = [
            'reportes.acceder',
            'bitacora.acceder',
        ];

        foreach ($permisosContabilidad as $permisoNombre) {
            if (isset($permisos[$permisoNombre])) {
                DB::table('rol_permiso')->insert([
                    'id_rol' => $colaboradorContabilidadId,
                    'id_permiso' => $permisos[$permisoNombre],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // =====================================
        // ROL 4: Administrador TI
        // =====================================
        $adminTiId = DB::table('rol')->insertGetId([
            'nombre' => 'Administrador TI',
            'descripcion' => 'Control total del sistema. Puede gestionar usuarios, puestos, roles, permisos y toda la configuración del sistema.',
            'es_sistema' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Administrador TI tiene TODOS los permisos
        foreach ($permisos as $permisoNombre => $permisoId) {
            DB::table('rol_permiso')->insert([
                'id_rol' => $adminTiId,
                'id_permiso' => $permisoId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Se crearon 4 roles con sus permisos correspondientes:');
        $this->command->info('   1. Colaborador de Bodega (' . count($permisosColaborador) . ' permisos)');
        $this->command->info('   2. Jefe de Bodega (' . count($permisosJefe) . ' permisos)');
        $this->command->info('   3. Colaborador de Contabilidad (' . count($permisosContabilidad) . ' permisos)');
        $this->command->info('   4. Administrador TI (' . count($permisos) . ' permisos - TODOS)');
    }
}
