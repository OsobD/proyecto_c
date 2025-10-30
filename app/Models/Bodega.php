<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    use HasFactory, Auditable;

    protected $table = 'bodega';

    protected $fillable = [
        'nombre',
        'activo',
    ];

    public $timestamps = true;

    // Relaciones de auditoría
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relaciones de negocio
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

    // Reglas de validación
    public static function rules($id = null)
    {
        return [
            'nombre' => 'required|string|max:255',
        ];
    }

    // Boot method para auto-asignar usuario
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
