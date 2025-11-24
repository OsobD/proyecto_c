<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    protected $table = 'permiso';

    protected $fillable = [
        'nombre',
        'modulo',
        'accion',
        'modificador',
        'descripcion',
    ];

    public $timestamps = true;

    // Relaciones
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_permiso', 'id_permiso', 'id_rol')->withTimestamps();
    }

    /**
     * Scope para buscar permisos por módulo
     */
    public function scopeModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    /**
     * Scope para buscar permisos por acción
     */
    public function scopeAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    /**
     * Obtener el nombre completo del permiso (módulo.acción.modificador)
     */
    public function getNombreCompletoAttribute()
    {
        $nombre = "{$this->modulo}.{$this->accion}";
        if ($this->modificador) {
            $nombre .= ".{$this->modificador}";
        }
        return $nombre;
    }
}
