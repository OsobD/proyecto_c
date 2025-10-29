<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarjetaResponsabilidad extends Model
{
    use HasFactory;

    protected $table = 'tarjeta_responsabilidad';

    protected $fillable = [
        'fecha_creacion',
        'total',
        'id_persona',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'total' => 'double',
    ];

    public $timestamps = false;

    // Relaciones
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function tarjetasProducto()
    {
        return $this->hasMany(TarjetaProducto::class, 'id_tarjeta');
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'id_tarjeta');
    }
}
