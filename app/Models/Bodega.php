<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    use HasFactory;

    protected $table = 'bodega';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = false;

    // Relaciones
    public function lotes()
    {
        return $this->hasMany(Lote::class, 'id_bodega');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_bodega');
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'id_bodega');
    }

    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class, 'id_bodega');
    }

    public function traslados()
    {
        return $this->hasMany(Traslado::class, 'id_bodega');
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class, 'id_bodega');
    }
}
