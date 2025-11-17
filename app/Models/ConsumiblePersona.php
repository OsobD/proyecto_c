<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumiblePersona extends Model
{
    use HasFactory;

    protected $table = 'consumible_persona';

    protected $fillable = [
        'correlativo',
        'fecha',
        'id_persona',
        'id_producto',
        'id_lote',
        'cantidad',
        'precio_unitario',
        'observaciones',
        'id_bodega',
    ];

    protected $casts = [
        'fecha' => 'date',
        'precio_unitario' => 'double',
    ];

    // Relaciones
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'id_lote');
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega');
    }
}
