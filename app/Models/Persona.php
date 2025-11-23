<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory, Auditable;

    protected $table = 'persona';

    protected $fillable = [
        'nombres',
        'apellidos',
        'dpi',
        'telefono',
        'correo',
        'estado',
    ];

    protected $casts = [
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

    public function salidas()
    {
        return $this->hasMany(Salida::class, 'id_persona');
    }

    // Reglas de validación
    public static function rules($id = null)
    {
        return [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'dpi' => 'required|string|size:13|unique:persona,dpi' . ($id ? ",$id" : ''),
            'telefono' => 'nullable|string|max:20|unique:persona,telefono' . ($id ? ",$id" : ''),
            'correo' => 'nullable|email|max:255|unique:persona,correo' . ($id ? ",$id" : ''),
            'estado' => 'nullable|boolean',
        ];
    }
}
