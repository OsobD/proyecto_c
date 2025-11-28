<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudAprobacion extends Model
{
    protected $table = 'solicitud_aprobacion';

    protected $fillable = [
        'tipo',
        'tabla',
        'registro_id',
        'datos',
        'solicitante_id',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'datos' => 'array',
    ];

    public function solicitante()
    {
        return $this->belongsTo(Usuario::class, 'solicitante_id');
    }
}
