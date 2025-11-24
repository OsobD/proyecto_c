<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class NavigationService
{
    /**
     * Obtener los elementos del menú de navegación filtrados por permisos
     *
     * @return array
     */
    public static function getMenuItems()
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        // FALLBACK: Si el usuario no tiene rol, mostrar todo (modo desarrollo)
        // Esto evita que el navbar esté vacío si olvidaste asignar roles
        if (!$user->rol) {
            \Log::warning("Usuario {$user->id} ({$user->nombre_usuario}) no tiene rol asignado. Mostrando navbar completo por defecto.");
        }

        $menu = [
            // COMPRAS
            [
                'label' => 'Compras',
                'permission' => 'compras.acceder',
                'route_pattern' => ['compras', 'compras.*'],
                'children' => [
                    [
                        'label' => 'Inicio',
                        'route' => 'compras',
                        'permission' => 'compras.acceder',
                    ],
                    [
                        'label' => 'Nueva Compra',
                        'route' => 'compras.nueva',
                        'permission' => 'compras.crear',
                    ],
                    [
                        'label' => 'Historial',
                        'route' => 'compras.historial',
                        'permission' => 'compras.acceder',
                    ],
                ],
            ],

            // TRASLADOS
            [
                'label' => 'Traslados',
                'permission' => 'traslados.acceder',
                'route_pattern' => ['traslados', 'traslados.*', 'requisiciones', 'requisiciones.*', 'devoluciones', 'devoluciones.*'],
                'children' => [
                    [
                        'label' => 'Inicio',
                        'route' => 'traslados',
                        'permission' => 'traslados.acceder',
                    ],
                    [
                        'label' => 'Requisición',
                        'route' => 'requisiciones',
                        'permission' => 'requisiciones.acceder',
                    ],
                    [
                        'label' => 'Devolución',
                        'route' => 'devoluciones',
                        'permission' => 'devoluciones.acceder',
                    ],
                    [
                        'label' => 'Nuevo Traslado',
                        'route' => 'traslados.nuevo',
                        'permission' => 'traslados.crear',
                    ],
                    [
                        'label' => 'Historial',
                        'route' => 'traslados.historial',
                        'permission' => 'traslados.acceder',
                    ],
                ],
            ],

            // CATÁLOGO (Productos, Categorías, Proveedores)
            [
                'label' => 'Catálogo',
                'permission_or' => ['productos.acceder', 'categorias.acceder', 'proveedores.acceder'],
                'route_pattern' => ['proveedores', 'productos', 'productos.*'],
                'children' => [
                    [
                        'label' => 'Productos',
                        'route' => 'productos',
                        'permission' => 'productos.acceder',
                    ],
                    [
                        'label' => 'Categorías',
                        'route' => 'productos.categorias',
                        'permission' => 'categorias.acceder',
                    ],
                    [
                        'label' => 'Proveedores',
                        'route' => 'proveedores',
                        'permission' => 'proveedores.acceder',
                    ],
                ],
            ],

            // COLABORADORES (Personas, Usuarios, Puestos)
            [
                'label' => 'Colaboradores',
                'permission_or' => ['personas.acceder', 'usuarios.acceder', 'puestos.acceder'],
                'route_pattern' => ['personas', 'personas.*', 'usuarios', 'usuarios.*', 'puestos', 'puestos.*'],
                'children' => [
                    [
                        'label' => 'Personas',
                        'route' => 'personas',
                        'permission' => 'personas.acceder',
                    ],
                    [
                        'label' => 'Usuarios',
                        'route' => 'usuarios',
                        'permission' => 'usuarios.acceder',
                    ],
                    [
                        'label' => 'Puestos',
                        'route' => 'puestos',
                        'permission' => 'puestos.acceder',
                    ],
                ],
            ],

            // ALMACENES (Bodegas, Tarjetas)
            [
                'label' => 'Almacenes',
                'permission_or' => ['bodegas.acceder', 'tarjetas.acceder'],
                'route_pattern' => ['bodegas', 'bodegas.*', 'tarjetas.responsabilidad', 'tarjetas.*'],
                'children' => [
                    [
                        'label' => 'Bodegas',
                        'route' => 'bodegas',
                        'permission' => 'bodegas.acceder',
                    ],
                    [
                        'label' => 'Tarjetas de Responsabilidad',
                        'route' => 'tarjetas.responsabilidad',
                        'permission' => 'tarjetas.acceder',
                    ],
                ],
            ],

            // REPORTES (incluyendo Bitácora)
            [
                'label' => 'Reportes',
                'permission' => 'reportes.acceder',
                'route_pattern' => ['reportes', 'bitacora'],
                'children' => [
                    [
                        'label' => 'Reportes Generales',
                        'route' => 'reportes',
                        'permission' => 'reportes.acceder',
                    ],
                    [
                        'label' => 'Bitácora del Sistema',
                        'route' => 'bitacora',
                        'permission' => 'bitacora.acceder',
                    ],
                ],
            ],

            // APROBACIONES
            [
                'label' => 'Aprobaciones',
                'permission' => 'aprobaciones.ver',
                'route_pattern' => ['aprobaciones'],
                'children' => [
                    [
                        'label' => 'Pendientes',
                        'route' => 'aprobaciones',
                        'permission' => 'aprobaciones.ver',
                    ],
                ],
            ],

            // CONFIGURACIÓN
            [
                'label' => 'Configuración',
                'permission' => 'configuracion.acceder',
                'route_pattern' => ['configuracion', 'configuracion.*'],
                'children' => [
                    [
                        'label' => 'General',
                        'route' => 'configuracion',
                        'permission' => 'configuracion.acceder',
                    ],
                    [
                        'label' => 'Roles',
                        'route' => 'configuracion.roles',
                        'permission' => 'configuracion.roles',
                    ],
                    [
                        'label' => 'Permisos',
                        'route' => 'configuracion.permisos',
                        'permission' => 'configuracion.permisos',
                    ],
                ],
            ],
        ];

        // Filtrar menú según permisos
        return collect($menu)->filter(function ($item) use ($user) {
            return self::tieneAcceso($user, $item);
        })->map(function ($item) use ($user) {
            // Filtrar children si existen
            if (isset($item['children'])) {
                $item['children'] = collect($item['children'])->filter(function ($child) use ($user) {
                    return self::tieneAcceso($user, $child);
                })->values()->all();
            }
            return $item;
        })->values()->all();
    }

    /**
     * Verificar si el usuario tiene acceso a un elemento del menú
     *
     * @param \App\Models\Usuario $user
     * @param array $item
     * @return bool
     */
    private static function tieneAcceso($user, $item)
    {
        // FALLBACK: Si el usuario no tiene rol asignado, mostrar TODO
        // (Evita navbar vacío en desarrollo)
        if (!$user->rol) {
            return true;
        }

        // Si no requiere permiso, todos tienen acceso
        if (!isset($item['permission']) && !isset($item['permission_or'])) {
            return true;
        }

        // Si tiene permission_or (requiere al menos uno)
        if (isset($item['permission_or'])) {
            foreach ($item['permission_or'] as $permiso) {
                if ($user->tienePermiso($permiso)) {
                    return true;
                }
            }
            return false;
        }

        // Si tiene permission (requiere exactamente ese)
        if (isset($item['permission'])) {
            return $user->tienePermiso($item['permission']);
        }

        return false;
    }

    /**
     * Convertir un dropdown con solo un elemento a link directo
     *
     * @param array $menuItems
     * @return array
     */
    public static function simplifyDropdowns($menuItems)
    {
        return collect($menuItems)->map(function ($item) {
            if (isset($item['children']) && count($item['children']) === 1) {
                // Si solo tiene un hijo, usar el label y ruta del hijo directamente
                $child = $item['children'][0];
                $item['label'] = $child['label'];
                $item['route'] = $child['route'];
                $item['route_param'] = $child['route_param'] ?? null;
                $item['permission'] = $child['permission'] ?? null;
                unset($item['children']);
            }
            return $item;
        })->all();
    }
}
