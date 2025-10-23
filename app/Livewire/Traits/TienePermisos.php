<?php

namespace App\Livewire\Traits;

trait TienePermisos
{
    /**
     * Usuario simulado actual con su rol
     * En producción, esto vendría de la sesión/auth
     */
    public function getUsuarioActual()
    {
        return [
            'id' => 1,
            'nombre' => 'David Bautista',
            'rol' => 'operador', // Roles: admin, supervisor, operador
        ];
    }

    /**
     * Verifica si el usuario tiene permiso para una acción
     */
    public function tienePermiso($accion)
    {
        $usuario = $this->getUsuarioActual();
        $rol = $usuario['rol'];

        $permisos = [
            'admin' => ['*'], // Admin tiene todos los permisos
            'supervisor' => [
                'compras.ver',
                'compras.crear',
                'compras.editar',
                'compras.desactivar',
                'traslados.ver',
                'traslados.crear',
                'traslados.editar',
                'traslados.desactivar',
                'reportes.generar',
                'reportes.exportar',
            ],
            'operador' => [
                'compras.ver',
                'compras.crear',
                'traslados.ver',
                'traslados.crear',
                'reportes.generar',
            ],
        ];

        // Admin tiene todos los permisos
        if (isset($permisos[$rol]) && in_array('*', $permisos[$rol])) {
            return true;
        }

        // Verificar si el rol tiene el permiso específico
        return isset($permisos[$rol]) && in_array($accion, $permisos[$rol]);
    }

    /**
     * Verifica si el usuario es supervisor o admin
     */
    public function esSupervisorOAdmin()
    {
        $usuario = $this->getUsuarioActual();
        return in_array($usuario['rol'], ['admin', 'supervisor']);
    }

    /**
     * Verifica si el usuario es admin
     */
    public function esAdmin()
    {
        $usuario = $this->getUsuarioActual();
        return $usuario['rol'] === 'admin';
    }

    /**
     * Lanza un mensaje de error si no tiene permiso
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
