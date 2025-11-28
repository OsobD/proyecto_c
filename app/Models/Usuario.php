<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';

    protected $fillable = [
        'nombre_usuario',
        'contrasena',
        'id_persona',
        'id_rol',
        'estado',
        'debe_cambiar_contrasena',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'contrasena' => 'hashed',
        'debe_cambiar_contrasena' => 'boolean',
    ];

    // Accessor para password (Laravel Auth)
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Override para remember token
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    // Accessor para name (compatibilidad)
    public function getNameAttribute()
    {
        return $this->nombre_usuario;
    }

    // Relaciones
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }



    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_usuario');
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'id_usuario');
    }

    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class, 'id_usuario');
    }

    public function traslados()
    {
        return $this->hasMany(Traslado::class, 'id_usuario');
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class, 'id_usuario');
    }

    public function cambiosSolicitados()
    {
        return $this->hasMany(CambioPendiente::class, 'usuario_solicitante_id');
    }

    public function cambiosAprobados()
    {
        return $this->hasMany(CambioPendiente::class, 'usuario_aprobador_id');
    }

    // =====================================
    // MÉTODOS DE PERMISOS
    // =====================================

    /**
     * Verificar si el usuario tiene un permiso específico
     *
     * @param string $permisoNombre - Nombre del permiso (ej: 'compras.crear', 'compras.editar.sin_aprobacion')
     * @return bool
     */
    public function tienePermiso($permisoNombre)
    {
        if (!$this->rol) {
            return false;
        }

        return $this->rol->permisos()
                    ->where('nombre', $permisoNombre)
                    ->exists();
    }

    /**
     * Alias de tienePermiso() para usar con @can en Blade
     *
     * @param string $permiso
     * @return bool
     */
    public function puede($permiso)
    {
        return $this->tienePermiso($permiso);
    }

    /**
     * Verificar si puede CREAR en un módulo
     *
     * @param string $modulo - Nombre del módulo (ej: 'compras', 'productos')
     * @return bool
     */
    public function puedeCrear($modulo)
    {
        return $this->tienePermiso("{$modulo}.crear");
    }

    /**
     * Verificar si puede EDITAR DIRECTAMENTE (sin aprobación)
     *
     * @param string $modulo
     * @return bool
     */
    public function puedeEditarDirecto($modulo)
    {
        return $this->tienePermiso("{$modulo}.editar.sin_aprobacion");
    }

    /**
     * Verificar si puede EDITAR (con o sin aprobación)
     *
     * @param string $modulo
     * @return bool
     */
    public function puedeEditar($modulo)
    {
        return $this->tienePermiso("{$modulo}.editar") ||
               $this->tienePermiso("{$modulo}.editar.sin_aprobacion");
    }

    /**
     * Verificar si puede ELIMINAR
     *
     * @param string $modulo
     * @return bool
     */
    public function puedeEliminar($modulo)
    {
        return $this->tienePermiso("{$modulo}.eliminar");
    }

    /**
     * Verificar si puede APROBAR cambios
     *
     * @param string $modulo
     * @return bool
     */
    public function puedeAprobar($modulo)
    {
        return $this->tienePermiso("{$modulo}.aprobar");
    }

    /**
     * Verificar si puede acceder a un módulo
     *
     * @param string $modulo
     * @return bool
     */
    public function puedeAcceder($modulo)
    {
        return $this->tienePermiso("{$modulo}.acceder");
    }

    /**
     * Verificar si el usuario es Administrador TI
     *
     * @return bool
     */
    public function esAdministrador()
    {
        return $this->rol && $this->rol->nombre === 'Administrador TI';
    }

    /**
     * Verificar si el usuario es Jefe de Bodega
     *
     * @return bool
     */
    public function esJefeBodega()
    {
        return $this->rol && $this->rol->nombre === 'Jefe de Bodega';
    }

    /**
     * Verificar si el usuario es Colaborador de Bodega
     *
     * @return bool
     */
    public function esColaboradorBodega()
    {
        return $this->rol && $this->rol->nombre === 'Colaborador de Bodega';
    }

    /**
     * Verificar si el usuario es Colaborador de Contabilidad
     *
     * @return bool
     */
    public function esColaboradorContabilidad()
    {
        return $this->rol && $this->rol->nombre === 'Colaborador de Contabilidad';
    }

    /**
     * Obtener todos los permisos del usuario (vía rol)
     *
     * @return \Illuminate\Support\Collection
     */
    public function obtenerPermisos()
    {
        if (!$this->rol) {
            return collect([]);
        }

        return $this->rol->permisos;
    }

    /**
     * Verificar si tiene ALGUNO de los permisos especificados
     *
     * @param array $permisos
     * @return bool
     */
    public function tieneAlgunPermiso(array $permisos)
    {
        foreach ($permisos as $permiso) {
            if ($this->tienePermiso($permiso)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verificar si tiene TODOS los permisos especificados
     *
     * @param array $permisos
     * @return bool
     */
    public function tieneTodosLosPermisos(array $permisos)
    {
        foreach ($permisos as $permiso) {
            if (!$this->tienePermiso($permiso)) {
                return false;
            }
        }
        return true;
    }
}
