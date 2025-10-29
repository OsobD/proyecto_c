<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    use HasFactory;

    protected $table = 'detalle_compra';

    protected $fillable = [
        'id_compra',
        'id_producto',
        'precio_ingreso',
        'cantidad',
    ];

    protected $casts = [
        'precio_ingreso' => 'double',
    ];

    public $timestamps = false;

    // Relaciones
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function detalles()
    {
        return $this->hasMany(Detalle::class, 'id_det_compra');
    }
}
