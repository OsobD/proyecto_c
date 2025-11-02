<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDevolucion extends Model
{
    use HasFactory;

    protected $table = 'tipo_devolucion';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = false;

    // Relaciones
    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class, 'id_tipo_devolucion');
    }
}
