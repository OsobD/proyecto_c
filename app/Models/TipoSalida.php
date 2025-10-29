<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoSalida extends Model
{
    use HasFactory;

    protected $table = 'tipo_salida';

    protected $fillable = [
        'nombre',
        'id_salida',
    ];

    public $timestamps = false;

    // Relaciones
    public function salida()
    {
        return $this->belongsTo(Salida::class, 'id_salida');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleSalida::class, 'id_salida');
    }

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'id_salida');
    }
}
