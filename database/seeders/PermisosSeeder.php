<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            // =====================================
            // COMPRAS
            // =====================================
            [
                'nombre' => 'compras.acceder',
                'modulo' => 'compras',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder al módulo de compras',
            ],
            [
                'nombre' => 'compras.crear',
                'modulo' => 'compras',
                'accion' => 'crear',
                'modificador' => null,
                'descripcion' => 'Puede crear compras',
            ],
            [
                'nombre' => 'compras.editar',
                'modulo' => 'compras',
                'accion' => 'editar',
                'modificador' => null,
                'descripcion' => 'Puede solicitar edición de compras (requiere aprobación)',
            ],
            [
                'nombre' => 'compras.editar.sin_aprobacion',
                'modulo' => 'compras',
                'accion' => 'editar',
                'modificador' => 'sin_aprobacion',
                'descripcion' => 'Puede editar compras directamente sin aprobación',
            ],
            [
                'nombre' => 'compras.eliminar',
                'modulo' => 'compras',
                'accion' => 'eliminar',
                'modificador' => null,
                'descripcion' => 'Puede eliminar/desactivar compras',
            ],
            [
                'nombre' => 'compras.aprobar',
                'modulo' => 'compras',
                'accion' => 'aprobar',
                'modificador' => null,
                'descripcion' => 'Puede aprobar cambios en compras',
            ],

            // =====================================
            // TRASLADOS
            // =====================================
            [
                'nombre' => 'traslados.acceder',
                'modulo' => 'traslados',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder al módulo de traslados',
            ],
            [
                'nombre' => 'traslados.crear',
                'modulo' => 'traslados',
                'accion' => 'crear',
                'modificador' => null,
                'descripcion' => 'Puede crear traslados',
            ],
            [
                'nombre' => 'traslados.editar',
                'modulo' => 'traslados',
                'accion' => 'editar',
                'modificador' => null,
                'descripcion' => 'Puede solicitar edición de traslados (requiere aprobación)',
            ],
            [
                'nombre' => 'traslados.editar.sin_aprobacion',
                'modulo' => 'traslados',
                'accion' => 'editar',
                'modificador' => 'sin_aprobacion',
                'descripcion' => 'Puede editar traslados directamente sin aprobación',
            ],
            [
                'nombre' => 'traslados.eliminar',
                'modulo' => 'traslados',
                'accion' => 'eliminar',
                'modificador' => null,
                'descripcion' => 'Puede eliminar/desactivar traslados',
            ],
            [
                'nombre' => 'traslados.aprobar',
                'modulo' => 'traslados',
                'accion' => 'aprobar',
                'modificador' => null,
                'descripcion' => 'Puede aprobar cambios en traslados',
            ],

            // =====================================
            // REQUISICIONES
            // =====================================
            [
                'nombre' => 'requisiciones.acceder',
                'modulo' => 'requisiciones',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder al módulo de requisiciones',
            ],
            [
                'nombre' => 'requisiciones.crear',
                'modulo' => 'requisiciones',
                'accion' => 'crear',
                'modificador' => null,
                'descripcion' => 'Puede crear requisiciones',
            ],
            [
                'nombre' => 'requisiciones.editar',
                'modulo' => 'requisiciones',
                'accion' => 'editar',
                'modificador' => null,
                'descripcion' => 'Puede solicitar edición de requisiciones (requiere aprobación)',
            ],
            [
                'nombre' => 'requisiciones.editar.sin_aprobacion',
                'modulo' => 'requisiciones',
                'accion' => 'editar',
                'modificador' => 'sin_aprobacion',
                'descripcion' => 'Puede editar requisiciones directamente sin aprobación',
            ],
            [
                'nombre' => 'requisiciones.eliminar',
                'modulo' => 'requisiciones',
                'accion' => 'eliminar',
                'modificador' => null,
                'descripcion' => 'Puede eliminar/desactivar requisiciones',
            ],
            [
                'nombre' => 'requisiciones.aprobar',
                'modulo' => 'requisiciones',
                'accion' => 'aprobar',
                'modificador' => null,
                'descripcion' => 'Puede aprobar cambios en requisiciones',
            ],

            // =====================================
            // DEVOLUCIONES
            // =====================================
            [
                'nombre' => 'devoluciones.acceder',
                'modulo' => 'devoluciones',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder al módulo de devoluciones',
            ],
            [
                'nombre' => 'devoluciones.crear',
                'modulo' => 'devoluciones',
                'accion' => 'crear',
                'modificador' => null,
                'descripcion' => 'Puede crear devoluciones',
            ],
            [
                'nombre' => 'devoluciones.editar',
                'modulo' => 'devoluciones',
                'accion' => 'editar',
                'modificador' => null,
                'descripcion' => 'Puede solicitar edición de devoluciones (requiere aprobación)',
            ],
            [
                'nombre' => 'devoluciones.editar.sin_aprobacion',
                'modulo' => 'devoluciones',
                'accion' => 'editar',
                'modificador' => 'sin_aprobacion',
                'descripcion' => 'Puede editar devoluciones directamente sin aprobación',
            ],
            [
                'nombre' => 'devoluciones.eliminar',
                'modulo' => 'devoluciones',
                'accion' => 'eliminar',
                'modificador' => null,
                'descripcion' => 'Puede eliminar/desactivar devoluciones',
            ],
            [
                'nombre' => 'devoluciones.aprobar',
                'modulo' => 'devoluciones',
                'accion' => 'aprobar',
                'modificador' => null,
                'descripcion' => 'Puede aprobar cambios en devoluciones',
            ],

            // =====================================
            // PRODUCTOS
            // =====================================
            [
                'nombre' => 'productos.acceder',
                'modulo' => 'productos',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder al módulo de productos',
            ],
            [
                'nombre' => 'productos.crear',
                'modulo' => 'productos',
                'accion' => 'crear',
                'modificador' => null,
                'descripcion' => 'Puede crear productos',
            ],
            [
                'nombre' => 'productos.editar',
                'modulo' => 'productos',
                'accion' => 'editar',
                'modificador' => null,
                'descripcion' => 'Puede solicitar edición de productos (requiere aprobación)',
            ],
            [
                'nombre' => 'productos.editar.sin_aprobacion',
                'modulo' => 'productos',
                'accion' => 'editar',
                'modificador' => 'sin_aprobacion',
                'descripcion' => 'Puede editar productos directamente sin aprobación',
            ],
            [
                'nombre' => 'productos.eliminar',
                'modulo' => 'productos',
                'accion' => 'eliminar',
                'modificador' => null,
                'descripcion' => 'Puede eliminar/desactivar productos',
            ],

            // =====================================
            // CATEGORÍAS
            // =====================================
            [
                'nombre' => 'categorias.acceder',
                'modulo' => 'categorias',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder al módulo de categorías',
            ],
            [
                'nombre' => 'categorias.crear',
                'modulo' => 'categorias',
                'accion' => 'crear',
                'modificador' => null,
                'descripcion' => 'Puede crear categorías',
            ],
            [
                'nombre' => 'categorias.editar',
                'modulo' => 'categorias',
                'accion' => 'editar',
                'modificador' => null,
                'descripcion' => 'Puede solicitar edición de categorías (requiere aprobación)',
            ],
            [
                'nombre' => 'categorias.editar.sin_aprobacion',
                'modulo' => 'categorias',
                'accion' => 'editar',
                'modificador' => 'sin_aprobacion',
                'descripcion' => 'Puede editar categorías directamente sin aprobación',
            ],
            [
                'nombre' => 'categorias.eliminar',
                'modulo' => 'categorias',
                'accion' => 'eliminar',
                'modificador' => null,
                'descripcion' => 'Puede eliminar/desactivar categorías',
            ],

            // =====================================
            // PROVEEDORES
            // =====================================
            [
                'nombre' => 'proveedores.acceder',
                'modulo' => 'proveedores',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder al módulo de proveedores',
            ],
            [
                'nombre' => 'proveedores.crear',
                'modulo' => 'proveedores',
                'accion' => 'crear',
                'modificador' => null,
                'descripcion' => 'Puede crear proveedores',
            ],
            [
                'nombre' => 'proveedores.editar',
                'modulo' => 'proveedores',
                'accion' => 'editar',
                'modificador' => null,
                'descripcion' => 'Puede solicitar edición de proveedores (requiere aprobación)',
            ],
            [
                'nombre' => 'proveedores.editar.sin_aprobacion',
                'modulo' => 'proveedores',
                'accion' => 'editar',
                'modificador' => 'sin_aprobacion',
                'descripcion' => 'Puede editar proveedores directamente sin aprobación',
            ],
            [
                'nombre' => 'proveedores.eliminar',
                'modulo' => 'proveedores',
                'accion' => 'eliminar',
                'modificador' => null,
                'descripcion' => 'Puede eliminar/desactivar proveedores',
            ],

            // =====================================
            // PERSONAS
            // =====================================
            [
                'nombre' => 'personas.acceder',
                'modulo' => 'personas',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder al módulo de personas',
            ],
            [
                'nombre' => 'personas.crear',
                'modulo' => 'personas',
                'accion' => 'crear',
                'modificador' => null,
                'descripcion' => 'Puede crear personas',
            ],
            [
                'nombre' => 'personas.editar',
                'modulo' => 'personas',
                'accion' => 'editar',
                'modificador' => null,
                'descripcion' => 'Puede solicitar edición de personas (requiere aprobación)',
            ],
            [
                'nombre' => 'personas.editar.sin_aprobacion',
                'modulo' => 'personas',
                'accion' => 'editar',
                'modificador' => 'sin_aprobacion',
                'descripcion' => 'Puede editar personas directamente sin aprobación',
            ],
            [
                'nombre' => 'personas.eliminar',
                'modulo' => 'personas',
                'accion' => 'eliminar',
                'modificador' => null,
                'descripcion' => 'Puede eliminar/desactivar personas',
            ],

            // =====================================
            // BODEGAS
            // =====================================
            [
                'nombre' => 'bodegas.acceder',
                'modulo' => 'bodegas',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder al módulo de bodegas',
            ],
            [
                'nombre' => 'bodegas.crear',
                'modulo' => 'bodegas',
                'accion' => 'crear',
                'modificador' => null,
                'descripcion' => 'Puede crear bodegas',
            ],
            [
                'nombre' => 'bodegas.editar',
                'modulo' => 'bodegas',
                'accion' => 'editar',
                'modificador' => null,
                'descripcion' => 'Puede editar bodegas',
            ],
            [
                'nombre' => 'bodegas.eliminar',
                'modulo' => 'bodegas',
                'accion' => 'eliminar',
                'modificador' => null,
                'descripcion' => 'Puede eliminar/desactivar bodegas',
            ],

            // =====================================
            // TARJETAS DE RESPONSABILIDAD
            // =====================================
            [
                'nombre' => 'tarjetas.acceder',
                'modulo' => 'tarjetas',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder al módulo de tarjetas de responsabilidad',
            ],
            [
                'nombre' => 'tarjetas.crear',
                'modulo' => 'tarjetas',
                'accion' => 'crear',
                'modificador' => null,
                'descripcion' => 'Puede crear tarjetas de responsabilidad',
            ],
            [
                'nombre' => 'tarjetas.editar',
                'modulo' => 'tarjetas',
                'accion' => 'editar',
                'modificador' => null,
                'descripcion' => 'Puede editar tarjetas de responsabilidad',
            ],
            [
                'nombre' => 'tarjetas.eliminar',
                'modulo' => 'tarjetas',
                'accion' => 'eliminar',
                'modificador' => null,
                'descripcion' => 'Puede eliminar/desactivar tarjetas de responsabilidad',
            ],

            // =====================================
            // USUARIOS
            // =====================================
            [
                'nombre' => 'usuarios.acceder',
                'modulo' => 'usuarios',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder al módulo de usuarios',
            ],
            [
                'nombre' => 'usuarios.crear',
                'modulo' => 'usuarios',
                'accion' => 'crear',
                'modificador' => null,
                'descripcion' => 'Puede crear usuarios',
            ],
            [
                'nombre' => 'usuarios.editar',
                'modulo' => 'usuarios',
                'accion' => 'editar',
                'modificador' => null,
                'descripcion' => 'Puede editar usuarios',
            ],
            [
                'nombre' => 'usuarios.eliminar',
                'modulo' => 'usuarios',
                'accion' => 'eliminar',
                'modificador' => null,
                'descripcion' => 'Puede eliminar/desactivar usuarios',
            ],
            [
                'nombre' => 'usuarios.cambiar_estado',
                'modulo' => 'usuarios',
                'accion' => 'cambiar_estado',
                'modificador' => null,
                'descripcion' => 'Puede activar/desactivar usuarios',
            ],
            [
                'nombre' => 'usuarios.resetear_password',
                'modulo' => 'usuarios',
                'accion' => 'resetear_password',
                'modificador' => null,
                'descripcion' => 'Puede resetear contraseñas de usuarios',
            ],

            // =====================================
            // PUESTOS
            // =====================================
            [
                'nombre' => 'puestos.acceder',
                'modulo' => 'puestos',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder al módulo de puestos',
            ],
            [
                'nombre' => 'puestos.crear',
                'modulo' => 'puestos',
                'accion' => 'crear',
                'modificador' => null,
                'descripcion' => 'Puede crear puestos',
            ],
            [
                'nombre' => 'puestos.editar',
                'modulo' => 'puestos',
                'accion' => 'editar',
                'modificador' => null,
                'descripcion' => 'Puede editar puestos',
            ],
            [
                'nombre' => 'puestos.eliminar',
                'modulo' => 'puestos',
                'accion' => 'eliminar',
                'modificador' => null,
                'descripcion' => 'Puede eliminar/desactivar puestos',
            ],

            // =====================================
            // REPORTES
            // =====================================
            [
                'nombre' => 'reportes.acceder',
                'modulo' => 'reportes',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder al módulo de reportes',
            ],
            [
                'nombre' => 'reportes.exportar',
                'modulo' => 'reportes',
                'accion' => 'exportar',
                'modificador' => null,
                'descripcion' => 'Puede exportar reportes a Excel/PDF',
            ],

            // =====================================
            // BITÁCORA
            // =====================================
            [
                'nombre' => 'bitacora.acceder',
                'modulo' => 'bitacora',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder a la bitácora del sistema',
            ],

            // =====================================
            // CONFIGURACIÓN
            // =====================================
            [
                'nombre' => 'configuracion.acceder',
                'modulo' => 'configuracion',
                'accion' => 'acceder',
                'modificador' => null,
                'descripcion' => 'Puede acceder a la configuración del sistema',
            ],
            [
                'nombre' => 'configuracion.roles',
                'modulo' => 'configuracion',
                'accion' => 'roles',
                'modificador' => null,
                'descripcion' => 'Puede gestionar roles',
            ],
            [
                'nombre' => 'configuracion.permisos',
                'modulo' => 'configuracion',
                'accion' => 'permisos',
                'modificador' => null,
                'descripcion' => 'Puede gestionar permisos',
            ],

            // =====================================
            // APROBACIONES
            // =====================================
            [
                'nombre' => 'aprobaciones.ver',
                'modulo' => 'aprobaciones',
                'accion' => 'ver',
                'modificador' => null,
                'descripcion' => 'Puede ver aprobaciones pendientes',
            ],
            [
                'nombre' => 'aprobaciones.aprobar',
                'modulo' => 'aprobaciones',
                'accion' => 'aprobar',
                'modificador' => null,
                'descripcion' => 'Puede aprobar/rechazar cambios pendientes',
            ],
        ];

        foreach ($permisos as $permiso) {
            DB::table('permiso')->insert([
                'nombre' => $permiso['nombre'],
                'modulo' => $permiso['modulo'],
                'accion' => $permiso['accion'],
                'modificador' => $permiso['modificador'],
                'descripcion' => $permiso['descripcion'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Se crearon ' . count($permisos) . ' permisos correctamente.');
    }
}
