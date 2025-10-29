<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;

    protected $table = 'bitacora';

    protected $fillable = [];

    public $timestamps = false;

    // Relaciones
    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'id_bitacora');
    }
}
