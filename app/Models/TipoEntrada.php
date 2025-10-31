<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEntrada extends Model
{
    use HasFactory;

    protected $table = 'tipo_entrada';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = false;

    // Relaciones
    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'id_tipo');
    }
}
