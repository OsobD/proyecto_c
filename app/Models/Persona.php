<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'persona';

    protected $fillable = [
        'nombres',
        'apellidos',
        'telefono',
        'correo',
        'fecha_nacimiento',
        'genero',
        'estado',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'estado' => 'boolean',
    ];

    public $timestamps = false;

    // Relaciones
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_persona');
    }

    public function tarjetasResponsabilidad()
    {
        return $this->hasMany(TarjetaResponsabilidad::class, 'id_persona');
    }
}
