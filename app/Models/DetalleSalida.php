<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleSalida extends Model
{
    use HasFactory;

    protected $table = 'detalle_salida';

    protected $fillable = [
        'id_salida',
        'id_producto',
        'id_lote',
        'cantidad',
        'precio_salida',
    ];

    protected $casts = [
        'precio_salida' => 'decimal:2',
    ];

    public $timestamps = false;

    // Relaciones
    public function salida()
    {
        return $this->belongsTo(Salida::class, 'id_salida');
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
        return $this->hasMany(Detalle::class, 'id_det_salida');
    }
}
