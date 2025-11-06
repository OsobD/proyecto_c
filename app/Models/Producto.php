<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'producto';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'descripcion',
        'id_categoria',
        'es_consumible',
        'activo'
    ];

    protected $casts = [
        'es_consumible' => 'boolean',
    ];

    public $timestamps = false;

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'id_producto');
    }

    public function tarjetasProducto()
    {
        return $this->hasMany(TarjetaProducto::class, 'id_producto');
    }

    public function detallesCompra()
    {
        return $this->hasMany(DetalleCompra::class, 'id_producto');
    }

    public function detallesEntrada()
    {
        return $this->hasMany(DetalleEntrada::class, 'id_producto');
    }

    public function detallesDevolucion()
    {
        return $this->hasMany(DetalleDevolucion::class, 'id_producto');
    }

    public function detallesTraslado()
    {
        return $this->hasMany(DetalleTraslado::class, 'id_producto');
    }

    public function detallesSalida()
    {
        return $this->hasMany(DetalleSalida::class, 'id_producto');
    }
}
