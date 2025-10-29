<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    use HasFactory;

    protected $table = 'transaccion';

    protected $fillable = [
        'id_tipo',
        'id_compra',
        'id_entrada',
        'id_devolucion',
        'id_traslaado',
        'id_salida',
    ];

    public $timestamps = false;

    // Relaciones
    public function tipo()
    {
        return $this->belongsTo(TipoTransaccion::class, 'id_tipo');
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra');
    }

    public function entrada()
    {
        return $this->belongsTo(Entrada::class, 'id_entrada');
    }

    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'id_devolucion');
    }

    public function traslado()
    {
        return $this->belongsTo(Traslado::class, 'id_traslaado');
    }

    public function tipoSalida()
    {
        return $this->belongsTo(TipoSalida::class, 'id_salida');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'id_transaccion');
    }
}
