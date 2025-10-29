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
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public $timestamps = false;

    // Accessor para password (Laravel Auth)
    public function getAuthPassword()
    {
        return $this->contrasena;
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
}
