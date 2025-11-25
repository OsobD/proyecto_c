<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Representa la distribución de un lote en una bodega específica.
 *
 * Un lote puede estar distribuido en múltiples bodegas simultáneamente.
 * Esta tabla hace el tracking de cuánto de cada lote está en cada bodega.
 *
 * @property int $id
 * @property int $id_lote
 * @property int $id_bodega
 * @property int $cantidad Cantidad de este lote en esta bodega
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read Lote $lote
 * @property-read Bodega $bodega
 */
class LoteBodega extends Model
{
    use HasFactory;

    protected $table = 'lote_bodega';

    protected $fillable = [
        'id_lote',
        'id_bodega',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    /**
     * Relación: Este registro pertenece a un Lote
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class, 'id_lote');
    }

    /**
     * Relación: Este registro pertenece a una Bodega
     */
    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'id_bodega');
    }

    /**
     * Scope: Filtrar por lote
     */
    public function scopeDelLote($query, $idLote)
    {
        return $query->where('id_lote', $idLote);
    }

    /**
     * Scope: Filtrar por bodega
     */
    public function scopeEnBodega($query, $idBodega)
    {
        return $query->where('id_bodega', $idBodega);
    }

    /**
     * Scope: Solo registros con cantidad mayor a cero
     */
    public function scopeConStock($query)
    {
        return $query->where('cantidad', '>', 0);
    }

    /**
     * Obtiene o crea un registro de LoteBodega
     *
     * @param int $idLote
     * @param int $idBodega
     * @return LoteBodega
     */
    public static function obtenerOCrear($idLote, $idBodega)
    {
        return static::firstOrCreate(
            [
                'id_lote' => $idLote,
                'id_bodega' => $idBodega,
            ],
            [
                'cantidad' => 0,
            ]
        );
    }

    /**
     * Incrementa la cantidad de este lote en esta bodega
     *
     * @param int $cantidad
     * @return bool
     */
    public function incrementarCantidad($cantidad)
    {
        return $this->increment('cantidad', $cantidad);
    }

    /**
     * Decrementa la cantidad de este lote en esta bodega
     *
     * @param int $cantidad
     * @return bool
     */
    public function decrementarCantidad($cantidad)
    {
        $nuevaCantidad = $this->cantidad - $cantidad;

        if ($nuevaCantidad < 0) {
            throw new \Exception("No hay suficiente stock en esta bodega. Disponible: {$this->cantidad}, Solicitado: {$cantidad}");
        }

        $this->cantidad = $nuevaCantidad;
        return $this->save();
    }

    /**
     * Verifica si hay stock suficiente
     *
     * @param int $cantidadRequerida
     * @return bool
     */
    public function tieneSuficienteStock($cantidadRequerida)
    {
        return $this->cantidad >= $cantidadRequerida;
    }
}
