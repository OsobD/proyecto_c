<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;

    protected $table = 'salida';

    protected $fillable = [
        'fecha',
        'total',
        'descripcion',
        'ubicacion',
        'id_usuario',
        'id_tarjeta',
        'id_bodega',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'total' => 'double',
    ];

    public $timestamps = false;

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function tarjetaProducto()
    {
        return $this->belongsTo(TarjetaProducto::class, 'id_tarjeta');
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega');
    }

    public function tiposSalida()
    {
        return $this->hasMany(TipoSalida::class, 'id_salida');
    }
}
