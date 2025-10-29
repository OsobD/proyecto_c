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
        'id_permiso',
    ];

    public $timestamps = false;

    // Relaciones
    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'id_permiso');
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol');
    }
}
