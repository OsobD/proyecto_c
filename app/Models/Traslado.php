<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    use HasFactory;

    protected $table = 'traslado';

    protected $fillable = [
        'fecha',
        'no_requisicion',
        'total',
        'descripcion',
        'id_usuario',
        'id_bodega',
        'id_tarjeta',
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

    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega');
    }

    public function tarjetaProducto()
    {
        return $this->belongsTo(TarjetaProducto::class, 'id_tarjeta');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleTraslado::class, 'id_traslado');
    }

    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class, 'id_traslado');
    }

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'id_traslado');
    }
}
