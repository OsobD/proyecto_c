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
        'id_configuracion',
    ];

    public $timestamps = false;

    // Relaciones
    public function configuracion()
    {
        return $this->belongsTo(Configuracion::class, 'id_configuracion');
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_permiso', 'id_permiso', 'id_rol');
    }
}
