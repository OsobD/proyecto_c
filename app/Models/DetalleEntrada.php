<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleEntrada extends Model
{
    use HasFactory;

    protected $table = 'detalle_entrada';

    protected $fillable = [
        'id_entrada',
        'id_producto',
        'cantidad',
        'precio_ingreso',
    ];

    protected $casts = [
        'precio_ingreso' => 'decimal:2',
    ];

    public $timestamps = false;

    // Relaciones
    public function entrada()
    {
        return $this->belongsTo(Entrada::class, 'id_entrada');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function detalles()
    {
        return $this->hasMany(Detalle::class, 'id_det_entrada');
    }
}
