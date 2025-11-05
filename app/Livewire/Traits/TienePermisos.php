<?php

namespace App\Livewire\Traits;

/**
 * @trait TienePermisos
 * @package App\Livewire\Traits
 * @brief Trait para gestionar un sistema de permisos simulado basado en roles.
 *
 * Este trait proporciona métodos para verificar si un usuario tiene permiso para
 * realizar ciertas acciones. Simula un usuario autenticado y define un conjunto
 * de permisos para los roles 'admin', 'supervisor' y 'operador'. Está diseñado
 * para ser utilizado en componentes de Livewire que requieren control de acceso.
 */
trait TienePermisos
{
    /**
     * @brief Obtiene el usuario simulado actual.
     * En una aplicación real, esta información debería provenir del sistema de
     * autenticación de Laravel (ej. `auth()->user()`).
     *
     * @return array Datos del usuario simulado.
     */
    public function getUsuarioActual()
    {
        // Simulación del usuario actual. Roles posibles: 'admin', 'supervisor', 'operador'.
        return [
            'id' => 1,
            'nombre' => 'David Bautista',
            'rol' => 'supervisor',
        ];
    }

    /**
     * @brief Verifica si el usuario actual tiene un permiso específico.
     * Comprueba el rol del usuario contra una matriz de permisos predefinida.
     * El rol 'admin' tiene acceso a todas las acciones ('*').
     *
     * @param string $accion El permiso a verificar (ej. 'compras.editar').
     * @return bool `true` si el usuario tiene el permiso, `false` en caso contrario.
     */
    public function tienePermiso($accion)
    {
        $usuario = $this->getUsuarioActual();
        $rol = $usuario['rol'];

        $permisos = [
            'admin' => ['*'],
            'supervisor' => [
                'compras.ver', 'compras.crear', 'compras.editar', 'compras.desactivar',
                'traslados.ver', 'traslados.crear', 'traslados.editar', 'traslados.desactivar',
                'reportes.generar', 'reportes.exportar',
            ],
            'operador' => [
                'compras.ver', 'compras.crear',
                'traslados.ver', 'traslados.crear',
                'reportes.generar',
            ],
        ];

        if (isset($permisos[$rol]) && in_array('*', $permisos[$rol])) {
            return true;
        }

        return isset($permisos[$rol]) && in_array($accion, $permisos[$rol]);
    }

    /**
     * @brief Comprueba si el rol del usuario actual es 'supervisor' o 'admin'.
     *
     * @return bool
     */
    public function esSupervisorOAdmin()
    {
        $usuario = $this->getUsuarioActual();
        return in_array($usuario['rol'], ['admin', 'supervisor']);
    }

    /**
     * @brief Comprueba si el rol del usuario actual es 'admin'.
     *
     * @return bool
     */
    public function esAdmin()
    {
        $usuario = $this->getUsuarioActual();
        return $usuario['rol'] === 'admin';
    }

    /**
     * @brief Método de conveniencia para verificar un permiso y mostrar un mensaje de error.
     * Si el usuario no tiene el permiso, muestra un mensaje flash de error y retorna `false`.
     *
     * @param string $accion El permiso a verificar.
     * @param string|null $mensaje Mensaje de error personalizado.
     * @return bool `true` si la verificación es exitosa, `false` si no tiene permiso.
     */
    public function verificarPermiso($accion, $mensaje = null)
    {
        if (!$this->tienePermiso($accion)) {
            $mensajeError = $mensaje ?? 'No tiene permisos suficientes para realizar esta acción.';
            session()->flash('error', $mensajeError);
            return false;
        }
        return true;
    }
}
