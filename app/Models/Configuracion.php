<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuracion';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = false;

    // Relaciones
    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'id_configuracion');
    }
}
