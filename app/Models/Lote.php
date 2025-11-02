<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    protected $table = 'lote';

    protected $fillable = [
        'cantidad',
        'cantidad_inicial',
        'fecha_ingreso',
        'precio_ingreso',
        'observaciones',
        'id_producto',
        'id_bodega',
        'estado',
        'id_transaccion',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'precio_ingreso' => 'double',
        'estado' => 'boolean',
    ];

    public $timestamps = false;

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega');
    }

    public function transaccion()
    {
        return $this->belongsTo(Transaccion::class, 'id_transaccion');
    }

    public function tarjetasProducto()
    {
        return $this->hasMany(TarjetaProducto::class, 'id_lote');
    }

    public function detallesDevolucion()
    {
        return $this->hasMany(DetalleDevolucion::class, 'id_lote');
    }

    public function detallesTraslado()
    {
        return $this->hasMany(DetalleTraslado::class, 'id_lote');
    }

    public function detallesSalida()
    {
        return $this->hasMany(DetalleSalida::class, 'id_lote');
    }

    /**
     * Obtiene o crea el lote especial de ajuste para una bodega específica
     * Este lote se usa para equipo no registrado que se devuelve en buen estado
     *
     * @param int $id_bodega ID de la bodega
     * @return Lote|null
     */
    public static function obtenerLoteAjuste($id_bodega)
    {
        return self::where('id_bodega', $id_bodega)
            ->where('observaciones', 'LIKE', '%Lote especial para equipo no registrado recuperado%')
            ->first();
    }
}
