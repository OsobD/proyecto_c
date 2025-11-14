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
        'correlativo',
        'estado',
        'total',
        'descripcion',
        'observaciones',
        'id_usuario',
        'id_persona',
        'id_bodega_origen',
        'id_bodega_destino',
        'id_tarjeta',
        'activo',
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

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function bodegaOrigen()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega_origen');
    }

    public function bodegaDestino()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega_destino');
    }

    // Alias para compatibilidad con código legacy
    public function bodega()
    {
        return $this->bodegaOrigen();
    }

    public function tarjetaProducto()
    {
        return $this->belongsTo(TarjetaProducto::class, 'id_tarjeta');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleTraslado::class, 'id_traslado');
    }

    // Alias para compatibilidad
    public function detallesTraslado()
    {
        return $this->detalles();
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
