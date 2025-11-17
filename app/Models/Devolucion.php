<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    use HasFactory;

    protected $table = 'devolucion';

    protected $fillable = [
        'fecha',
        'no_formulario',
        'correlativo',
        'no_serie',
        'foto',
        'total',
        'id_usuario',
        'id_tarjeta',
        'id_bodega',
        'id_traslado',
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

    public function tarjetaProducto()
    {
        return $this->belongsTo(TarjetaProducto::class, 'id_tarjeta');
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega');
    }

    public function traslado()
    {
        return $this->belongsTo(Traslado::class, 'id_traslado');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleDevolucion::class, 'id_devolucion');
    }

    // Alias para compatibilidad
    public function detallesDevolucion()
    {
        return $this->detalles();
    }

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'id_devolucion');
    }

    // Obtener la persona que devuelve (a través de tarjeta_producto -> tarjeta_responsabilidad -> persona)
    public function getPersonaAttribute()
    {
        if (!$this->id_tarjeta) {
            return null;
        }

        $tarjetaProducto = TarjetaProducto::find($this->id_tarjeta);
        if (!$tarjetaProducto || !$tarjetaProducto->tarjetaResponsabilidad) {
            return null;
        }

        return $tarjetaProducto->tarjetaResponsabilidad->persona;
    }
}
