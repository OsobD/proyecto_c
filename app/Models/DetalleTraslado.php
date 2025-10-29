<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTraslado extends Model
{
    use HasFactory;

    protected $table = 'detalle_traslado';

    protected $fillable = [
        'id_traslado',
        'id_producto',
        'cantidad',
        'id_lote',
        'precio_traslado',
    ];

    protected $casts = [
        'precio_traslado' => 'double',
    ];

    public $timestamps = false;

    // Relaciones
    public function traslado()
    {
        return $this->belongsTo(Traslado::class, 'id_traslado');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'id_lote');
    }

    public function detalles()
    {
        return $this->hasMany(Detalle::class, 'id_det_traslado');
    }
}
