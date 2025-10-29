<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    use HasFactory;

    protected $table = 'kardex';

    protected $fillable = [
        'timestamp',
        'tipo_movimiento',
        'id_detalle',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public $timestamps = false;

    // Relaciones
    public function detalle()
    {
        return $this->belongsTo(Detalle::class, 'id_detalle');
    }
}
