<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarjetaResponsabilidad extends Model
{
    use HasFactory, Auditable;

    protected $table = 'tarjeta_responsabilidad';

    protected $fillable = [
        'nombre',
        'fecha_creacion',
        'total',
        'id_persona',
        'activo',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'total' => 'double',
    ];

    public $timestamps = true;

    // Relaciones de auditoría
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(Usuario::class, 'updated_by');
    }

    // Relaciones de negocio
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function tarjetasProducto()
    {
        return $this->hasMany(TarjetaProducto::class, 'id_tarjeta');
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'id_tarjeta');
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class, 'id_tarjeta');
    }

    public function traslados()
    {
        return $this->hasMany(Traslado::class, 'id_tarjeta');
    }

    public function devoluciones()
    {
        return $this->hasMany(Devolucion::class, 'id_tarjeta');
    }

    // Boot method para auto-asignar usuario
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Solo establecer created_by/updated_by si:
            // 1. El usuario está autenticado
            // 2. Los campos NO han sido establecidos explícitamente (ni siquiera como null)
            // Usamos array_key_exists en lugar de isset porque isset retorna false para valores null
            if (auth()->check() && !array_key_exists('created_by', $model->getAttributes())) {
                $model->created_by = auth()->id();
            }
            if (auth()->check() && !array_key_exists('updated_by', $model->getAttributes())) {
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check() && !$model->isDirty('updated_by')) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
