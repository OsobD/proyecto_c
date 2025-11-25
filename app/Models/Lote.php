<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    protected $table = 'lote';

    protected $fillable = [
        'cantidad_disponible',  // Renombrado de 'cantidad'
        'cantidad_inicial',
        'fecha_ingreso',
        'precio_ingreso',
        'observaciones',
        'id_producto',
        'id_bodega',  // Mantenido temporalmente para compatibilidad
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
     * Relación: Este lote puede estar distribuido en múltiples bodegas
     */
    public function ubicaciones()
    {
        return $this->hasMany(LoteBodega::class, 'id_lote');
    }

    /**
     * Relación: Ubicaciones con stock mayor a cero
     */
    public function ubicacionesConStock()
    {
        return $this->ubicaciones()->where('cantidad', '>', 0);
    }

    // ==================== MÉTODOS DE GESTIÓN DE STOCK ====================

    /**
     * Obtiene la cantidad disponible de este lote en una bodega específica
     *
     * @param int $idBodega
     * @return int
     */
    public function cantidadEnBodega($idBodega)
    {
        $ubicacion = $this->ubicaciones()->where('id_bodega', $idBodega)->first();
        return $ubicacion ? $ubicacion->cantidad : 0;
    }

    /**
     * Obtiene el registro LoteBodega para una bodega específica, o lo crea si no existe
     *
     * @param int $idBodega
     * @return LoteBodega
     */
    public function obtenerUbicacion($idBodega)
    {
        return LoteBodega::obtenerOCrear($this->id, $idBodega);
    }

    /**
     * Incrementa el stock de este lote en una bodega específica
     *
     * @param int $idBodega
     * @param int $cantidad
     * @param bool $incrementarDisponible Si true, también incrementa cantidad_disponible (ej: devoluciones)
     *                                      Si false, solo registra ubicación (ej: creación inicial)
     * @return bool
     */
    public function incrementarEnBodega($idBodega, $cantidad, $incrementarDisponible = false)
    {
        $ubicacion = $this->obtenerUbicacion($idBodega);
        $ubicacion->incrementarCantidad($cantidad);

        // Solo incrementar cantidad_disponible si se solicita explícitamente
        // (por ejemplo, en devoluciones donde se está agregando stock nuevo)
        if ($incrementarDisponible) {
            return $this->increment('cantidad_disponible', $cantidad);
        }

        return true;
    }

    /**
     * Decrementa el stock de este lote en una bodega específica
     *
     * @param int $idBodega
     * @param int $cantidad
     * @param bool $esConsumo Si es consumo (requisición), también reduce cantidad_disponible
     * @return bool
     */
    public function decrementarEnBodega($idBodega, $cantidad, $esConsumo = false)
    {
        $ubicacion = $this->ubicaciones()->where('id_bodega', $idBodega)->first();

        if (!$ubicacion) {
            throw new \Exception("El lote {$this->id} no tiene stock en la bodega {$idBodega}");
        }

        if (!$ubicacion->tieneSuficienteStock($cantidad)) {
            throw new \Exception("Stock insuficiente en bodega. Disponible: {$ubicacion->cantidad}, Solicitado: {$cantidad}");
        }

        $ubicacion->decrementarCantidad($cantidad);

        // Si es consumo (requisición de consumibles), también reducir cantidad_disponible total
        if ($esConsumo) {
            $this->decrement('cantidad_disponible', $cantidad);

            // Si ya no hay stock disponible, marcar como inactivo
            if ($this->cantidad_disponible <= 0) {
                $this->estado = false;
                $this->save();
            }
        }

        return true;
    }

    /**
     * Mueve stock de este lote de una bodega a otra (usado en traslados)
     *
     * @param int $idBodegaOrigen
     * @param int $idBodegaDestino
     * @param int $cantidad
     * @return bool
     */
    public function moverEntreBodegas($idBodegaOrigen, $idBodegaDestino, $cantidad)
    {
        // Validar que hay stock en origen
        $ubicacionOrigen = $this->ubicaciones()->where('id_bodega', $idBodegaOrigen)->first();

        if (!$ubicacionOrigen || !$ubicacionOrigen->tieneSuficienteStock($cantidad)) {
            $disponible = $ubicacionOrigen ? $ubicacionOrigen->cantidad : 0;
            throw new \Exception("Stock insuficiente en bodega origen. Disponible: {$disponible}, Solicitado: {$cantidad}");
        }

        // Decrementar en origen
        $ubicacionOrigen->decrementarCantidad($cantidad);

        // Incrementar en destino
        $ubicacionDestino = $this->obtenerUbicacion($idBodegaDestino);
        $ubicacionDestino->incrementarCantidad($cantidad);

        // La cantidad_disponible total NO cambia (solo se movió de lugar)
        return true;
    }

    /**
     * Obtiene todos los lotes de un producto en una bodega, ordenados por FIFO
     *
     * @param string $idProducto
     * @param int $idBodega
     * @param bool $soloActivos
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function obtenerLotesFIFO($idProducto, $idBodega, $soloActivos = true)
    {
        $query = self::where('id_producto', $idProducto)
            ->whereHas('ubicaciones', function ($q) use ($idBodega) {
                $q->where('id_bodega', $idBodega)
                  ->where('cantidad', '>', 0);
            })
            ->with(['ubicaciones' => function ($q) use ($idBodega) {
                $q->where('id_bodega', $idBodega);
            }])
            ->orderBy('fecha_ingreso', 'asc');

        if ($soloActivos) {
            $query->where('estado', true);
        }

        return $query->get();
    }

    /**
     * Calcula el stock total de un producto en una bodega (sumando todos los lotes)
     *
     * @param string $idProducto
     * @param int $idBodega
     * @return int
     */
    public static function stockTotalEnBodega($idProducto, $idBodega)
    {
        return LoteBodega::where('id_bodega', $idBodega)
            ->whereHas('lote', function ($q) use ($idProducto) {
                $q->where('id_producto', $idProducto)
                  ->where('estado', true);
            })
            ->sum('cantidad');
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

    /**
     * Scope: Filtrar lotes con stock en una bodega específica
     */
    public function scopeConStockEnBodega($query, $idBodega)
    {
        return $query->whereHas('ubicaciones', function ($q) use ($idBodega) {
            $q->where('id_bodega', $idBodega)
              ->where('cantidad', '>', 0);
        });
    }

    /**
     * Scope: Solo lotes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Accessor: Alias para mantener compatibilidad con código antiguo
     * Devuelve cantidad_disponible cuando se accede a 'cantidad'
     */
    public function getCantidadAttribute()
    {
        return $this->cantidad_disponible;
    }

    /**
     * Mutator: Alias para mantener compatibilidad con código antiguo
     * Actualiza cantidad_disponible cuando se asigna 'cantidad'
     */
    public function setCantidadAttribute($value)
    {
        $this->attributes['cantidad_disponible'] = $value;
    }
}
