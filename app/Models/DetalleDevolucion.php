<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleDevolucion extends Model
{
    use HasFactory;

    protected $table = 'detalle_devolucion';

    protected $fillable = [
        'id_devolucion',
        'id_producto',
        'id_lote',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public $timestamps = false;

    // Relaciones
    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'id_devolucion');
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
        return $this->hasMany(Detalle::class, 'id_det_devolucion');
    }
}
