<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compra';

    protected $fillable = [
        'fecha',
        'no_serie',
        'no_factura',
        'correltivo',
        'total',
        'id_proveedor',
        'id_bodega',
        'id_usuario',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'total' => 'double',
    ];

    public $timestamps = false;

    // Relaciones
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'id_compra');
    }

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'id_compra');
    }
}
