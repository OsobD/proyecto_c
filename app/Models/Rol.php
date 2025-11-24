<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'rol';

    protected $fillable = [
        'nombre',
        'descripcion',
        'es_sistema',
    ];

    protected $casts = [
        'es_sistema' => 'boolean',
    ];

    public $timestamps = true;

    // Relaciones
    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permiso', 'id_rol', 'id_permiso')
                    ->withTimestamps();
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol');
    }

    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function tienePermiso($permisoNombre)
    {
        return $this->permisos()
                    ->where('nombre', $permisoNombre)
                    ->exists();
    }

    /**
     * Asignar permiso al rol
     */
    public function asignarPermiso($permisoId)
    {
        if (!$this->permisos()->where('id', $permisoId)->exists()) {
            $this->permisos()->attach($permisoId);
        }
    }

    /**
     * Remover permiso del rol
     */
    public function removerPermiso($permisoId)
    {
        $this->permisos()->detach($permisoId);
    }

    /**
     * Sincronizar permisos (reemplazar todos)
     */
    public function sincronizarPermisos(array $permisosIds)
    {
        $this->permisos()->sync($permisosIds);
    }
}
