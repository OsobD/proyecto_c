<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoTransaccion extends Model
{
    use HasFactory;

    protected $table = 'tipo_transacion';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = false;

    // Relaciones
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'id_tipo');
    }

    public function detalles()
    {
        return $this->hasMany(Detalle::class, 'id_tipo');
    }
}
