<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_persona',
        'id_rol',
        'estado',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    /**
     * Relación con Persona
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    /**
     * Relación con Rol
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    /**
     * Relaciones con transacciones
     */
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
