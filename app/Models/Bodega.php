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

    /**
     * Relación con LoteBodega (nueva estructura)
     * Esta es la relación correcta para obtener los lotes que están en esta bodega
     */
    public function loteBodegas()
    {
        return $this->hasMany(LoteBodega::class, 'id_bodega');
    }

    /**
     * Relación con Lote a través de LoteBodega
     * Obtiene los lotes que tienen stock en esta bodega
     */
    public function lotes()
    {
        return $this->hasManyThrough(
            Lote::class,      // Modelo final
            LoteBodega::class, // Modelo intermedio
            'id_bodega',      // FK en lote_bodega
            'id',             // FK en lote
            'id',             // PK en bodega
            'id_lote'         // PK en lote_bodega
        );
    }

    /**
     * Método helper: Obtiene productos con su stock en esta bodega
     * Agrupa por producto y suma las cantidades de todos los lotes
     */
    public function productosConStock()
    {
        return \DB::table('lote_bodega as lb')
            ->join('lote as l', 'lb.id_lote', '=', 'l.id')
            ->join('producto as p', 'l.id_producto', '=', 'p.id')
            ->leftJoin('categoria as c', 'p.id_categoria', '=', 'c.id')
            ->where('lb.id_bodega', $this->id)
            ->where('lb.cantidad', '>', 0)
            ->where('l.estado', true)
            ->select(
                'p.id as producto_id',
                'p.descripcion',
                'p.es_consumible',
                'c.nombre as categoria',
                \DB::raw('SUM(lb.cantidad) as cantidad_total'),
                \DB::raw('GROUP_CONCAT(DISTINCT l.id) as lotes_ids')
            )
            ->groupBy('p.id', 'p.descripcion', 'p.es_consumible', 'c.nombre')
            ->get();
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

    public function trasladosOrigen()
    {
        return $this->hasMany(Traslado::class, 'id_bodega_origen');
    }

    public function trasladosDestino()
    {
        return $this->hasMany(Traslado::class, 'id_bodega_destino');
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
