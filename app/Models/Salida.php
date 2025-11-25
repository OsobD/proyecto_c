<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;

    protected $table = 'salida';

    protected $fillable = [
        'fecha',
        'total',
        'descripcion',
        'ubicacion',
        'id_usuario',
        'id_tarjeta',
        'id_bodega',
        'id_tipo',
        'id_persona',
        'activo',
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

    public function tarjetaProducto()
    {
        return $this->belongsTo(TarjetaProducto::class, 'id_tarjeta');
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega');
    }

    public function tipoSalida()
    {
        return $this->belongsTo(TipoSalida::class, 'id_tipo');
    }

    // Alias para compatibilidad
    public function tipo()
    {
        return $this->tipoSalida();
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleSalida::class, 'id_salida');
    }

    // Alias para compatibilidad
    public function detallesSalida()
    {
        return $this->detalles();
    }

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'id_salida');
    }

    // Scopes para soft delete
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopeInactivo($query)
    {
        return $query->where('activo', false);
    }

    // Helper methods
    public function estaActivo()
    {
        return $this->activo === true || $this->activo === 1;
    }
}
