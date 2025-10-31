<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $table = 'entrada';

    protected $fillable = [
        'fecha',
        'total',
        'descripcion',
        'id_usuario',
        'id_tarjeta',
        'id_bodega',
        'id_tipo',
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

    public function tarjetaResponsabilidad()
    {
        return $this->belongsTo(TarjetaResponsabilidad::class, 'id_tarjeta');
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega');
    }

    public function tipoEntrada()
    {
        return $this->belongsTo(TipoEntrada::class, 'id_tipo');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleEntrada::class, 'id_entrada');
    }

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'id_entrada');
    }
}
