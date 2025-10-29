<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarjetaProducto extends Model
{
    use HasFactory;

    protected $table = 'tarjeta_producto';

    protected $fillable = [
        'precio_asignacion',
        'id_tarjeta',
        'id_producto',
        'id_lote',
    ];

    protected $casts = [
        'precio_asignacion' => 'double',
    ];

    public $timestamps = false;

    // Relaciones
    public function tarjetaResponsabilidad()
    {
        return $this->belongsTo(TarjetaResponsabilidad::class, 'id_tarjeta');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'id_lote');
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class, 'id_tarjeta');
    }

    public function traslados()
    {
        return $this->hasMany(Traslado::class, 'id_tarjeta');
    }

    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class, 'id_tarjeta');
    }
}
