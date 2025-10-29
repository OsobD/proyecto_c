<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    use HasFactory;

    protected $table = 'detalle';

    protected $fillable = [
        'id_tipo',
        'id_det_compra',
        'id_det_entrada',
        'id_det_devolucion',
        'id_det_traslado',
        'id_det_salida',
    ];

    public $timestamps = false;

    // Relaciones
    public function tipoTransaccion()
    {
        return $this->belongsTo(TipoTransaccion::class, 'id_tipo');
    }

    public function detalleCompra()
    {
        return $this->belongsTo(DetalleCompra::class, 'id_det_compra');
    }

    public function detalleEntrada()
    {
        return $this->belongsTo(DetalleEntrada::class, 'id_det_entrada');
    }

    public function detalleDevolucion()
    {
        return $this->belongsTo(DetalleDevolucion::class, 'id_det_devolucion');
    }

    public function detalleTraslado()
    {
        return $this->belongsTo(DetalleTraslado::class, 'id_det_traslado');
    }

    public function detalleSalida()
    {
        return $this->belongsTo(DetalleSalida::class, 'id_det_salida');
    }

    public function kardex()
    {
        return $this->hasMany(Kardex::class, 'id_detalle');
    }
}
