<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\Entrada;
use App\Models\Salida;
use App\Models\Traslado;
use App\Models\Devolucion;
use App\Models\Producto;
use App\Models\Bodega;
use Illuminate\Support\Collection;

/**
 * Servicio para generar reportes Kardex de inventario
 *
 * El Kardex es un reporte que muestra todos los movimientos de inventario
 * (entradas, salidas, traslados) con sus respectivos costos y saldos acumulativos.
 */
class KardexService
{
    /**
     * Genera el reporte Kardex con todos los movimientos de inventario
     *
     * @param array $filtros Filtros a aplicar:
     *   - fecha_inicio: Fecha inicial del rango
     *   - fecha_fin: Fecha final del rango
     *   - id_bodega: ID de bodega específica (null = todas)
     *   - id_producto: ID de producto específico (null = todos)
     *   - id_usuario: ID de usuario que realizó el movimiento (null = todos)
     * @return Collection Colección de movimientos ordenados cronológicamente
     */
    public function generarKardex(array $filtros = []): Collection
    {
        $movimientos = collect();

        // Recopilar todos los tipos de movimientos
        $movimientos = $movimientos->merge($this->obtenerMovimientosCompras($filtros));
        $movimientos = $movimientos->merge($this->obtenerMovimientosEntradas($filtros));
        $movimientos = $movimientos->merge($this->obtenerMovimientosSalidas($filtros));
        $movimientos = $movimientos->merge($this->obtenerMovimientosTraslados($filtros));
        $movimientos = $movimientos->merge($this->obtenerMovimientosDevoluciones($filtros));

        // Ordenar cronológicamente
        $movimientos = $movimientos->sortBy('fecha')->values();

        // Calcular saldos acumulativos y costos
        $movimientos = $this->calcularSaldosYCostos($movimientos);

        return $movimientos;
    }

    /**
     * Obtiene los movimientos de compras
     */
    private function obtenerMovimientosCompras(array $filtros): Collection
    {
        $query = Compra::with([
            'detalles.producto.categoria',
            'detalles.producto.lotes',
            'bodega',
            'usuario',
            'proveedor'
        ])->where(function($q) {
            $q->where('activo', true)
              ->orWhereNull('activo');
        });

        // Aplicar filtros
        if (isset($filtros['fecha_inicio'])) {
            $query->whereDate('fecha', '>=', $filtros['fecha_inicio']);
        }

        if (isset($filtros['fecha_fin'])) {
            $query->whereDate('fecha', '<=', $filtros['fecha_fin']);
        }

        if (isset($filtros['id_bodega']) && $filtros['id_bodega'] !== '') {
            $query->where('id_bodega', $filtros['id_bodega']);
        }

        if (isset($filtros['id_usuario']) && $filtros['id_usuario'] !== '') {
            $query->where('id_usuario', $filtros['id_usuario']);
        }

        $compras = $query->get();
        $movimientos = collect();

        foreach ($compras as $compra) {
            foreach ($compra->detalles as $detalle) {
                // Filtro por producto si está especificado
                if (isset($filtros['id_producto']) && $filtros['id_producto'] !== '' && $detalle->id_producto !== $filtros['id_producto']) {
                    continue;
                }

                $movimientos->push([
                    'fecha' => $compra->fecha,
                    'tipo_movimiento' => 'COMPRA',
                    'codigo' => $compra->correlativo ?? $compra->id,
                    'producto_id' => $detalle->id_producto,
                    'producto' => $detalle->producto->descripcion ?? 'N/A',
                    'categoria' => $detalle->producto->categoria->nombre ?? 'Sin categoría',
                    'descripcion' => 'Entrada por compra',
                    'documento' => 'Compra No. ' . ($compra->no_factura ?? $compra->correlativo ?? $compra->id),
                    'proveedor' => $compra->proveedor->nombre ?? 'N/A',
                    'bodega_id' => $compra->id_bodega,
                    'bodega' => $compra->bodega->nombre ?? 'N/A',
                    'cantidad_entrada' => $detalle->cantidad,
                    'cantidad_salida' => 0,
                    'costo_unitario' => $detalle->precio_ingreso,
                    'usuario' => $compra->usuario->name ?? 'N/A',
                    'observaciones' => $compra->no_serie ? "Serie: {$compra->no_serie}" : null,
                ]);
            }
        }

        return $movimientos;
    }

    /**
     * Obtiene los movimientos de entradas manuales
     */
    private function obtenerMovimientosEntradas(array $filtros): Collection
    {
        $query = Entrada::with([
            'detalles.producto.categoria',
            'bodega',
            'usuario',
            'tipoEntrada'
        ]);

        // Aplicar filtros
        if (isset($filtros['fecha_inicio'])) {
            $query->whereDate('fecha', '>=', $filtros['fecha_inicio']);
        }

        if (isset($filtros['fecha_fin'])) {
            $query->whereDate('fecha', '<=', $filtros['fecha_fin']);
        }

        if (isset($filtros['id_bodega']) && $filtros['id_bodega'] !== '') {
            $query->where('id_bodega', $filtros['id_bodega']);
        }

        if (isset($filtros['id_usuario']) && $filtros['id_usuario'] !== '') {
            $query->where('id_usuario', $filtros['id_usuario']);
        }

        $entradas = $query->get();
        $movimientos = collect();

        foreach ($entradas as $entrada) {
            foreach ($entrada->detalles as $detalle) {
                // Filtro por producto si está especificado
                if (isset($filtros['id_producto']) && $filtros['id_producto'] !== '' && $detalle->id_producto !== $filtros['id_producto']) {
                    continue;
                }

                $movimientos->push([
                    'fecha' => $entrada->fecha,
                    'tipo_movimiento' => 'ENTRADA',
                    'codigo' => $entrada->correlativo ?? $entrada->id,
                    'producto_id' => $detalle->id_producto,
                    'producto' => $detalle->producto->descripcion ?? 'N/A',
                    'categoria' => $detalle->producto->categoria->nombre ?? 'Sin categoría',
                    'descripcion' => $entrada->descripcion ?? 'Entrada manual',
                    'documento' => 'Entrada No. ' . ($entrada->no_serie ?? $entrada->correlativo ?? $entrada->id),
                    'proveedor' => $entrada->tipoEntrada->nombre ?? 'Entrada manual',
                    'bodega_id' => $entrada->id_bodega,
                    'bodega' => $entrada->bodega->nombre ?? 'N/A',
                    'cantidad_entrada' => $detalle->cantidad,
                    'cantidad_salida' => 0,
                    'costo_unitario' => $detalle->precio_ingreso ?? 0,
                    'usuario' => $entrada->usuario->name ?? 'N/A',
                    'observaciones' => null,
                ]);
            }
        }

        return $movimientos;
    }

    /**
     * Obtiene los movimientos de salidas (requisiciones)
     */
    private function obtenerMovimientosSalidas(array $filtros): Collection
    {
        $query = Salida::with([
            'detalles.producto.categoria',
            'detalles.lote',
            'bodega',
            'usuario',
            'tipoSalida',
            'persona'
        ])->where(function($q) {
            $q->where('activo', true)
              ->orWhereNull('activo');
        });

        // Aplicar filtros
        if (isset($filtros['fecha_inicio'])) {
            $query->whereDate('fecha', '>=', $filtros['fecha_inicio']);
        }

        if (isset($filtros['fecha_fin'])) {
            $query->whereDate('fecha', '<=', $filtros['fecha_fin']);
        }

        if (isset($filtros['id_bodega']) && $filtros['id_bodega'] !== '') {
            $query->where('id_bodega', $filtros['id_bodega']);
        }

        if (isset($filtros['id_usuario']) && $filtros['id_usuario'] !== '') {
            $query->where('id_usuario', $filtros['id_usuario']);
        }

        $salidas = $query->get();

        // Log temporal para debug
        \Log::info('KardexService - Salidas encontradas: ' . $salidas->count());
        if ($salidas->count() > 0) {
            \Log::info('Primera salida: ID=' . $salidas->first()->id . ', Fecha=' . $salidas->first()->fecha . ', Detalles=' . $salidas->first()->detalles->count());
        }

        $movimientos = collect();

        foreach ($salidas as $salida) {
            foreach ($salida->detalles as $detalle) {
                // Filtro por producto si está especificado
                if (isset($filtros['id_producto']) && $filtros['id_producto'] !== '' && $detalle->id_producto !== $filtros['id_producto']) {
                    continue;
                }

                $movimientos->push([
                    'fecha' => $salida->fecha,
                    'tipo_movimiento' => 'SALIDA',
                    'codigo' => $salida->id,
                    'producto_id' => $detalle->id_producto,
                    'producto' => $detalle->producto->descripcion ?? 'N/A',
                    'categoria' => $detalle->producto->categoria->nombre ?? 'Sin categoría',
                    'descripcion' => $salida->tipoSalida->nombre ?? 'Requisición',
                    'documento' => 'Requisición No. ' . $salida->id,
                    'proveedor' => $salida->persona->nombre ?? 'N/A',
                    'bodega_id' => $salida->id_bodega,
                    'bodega' => $salida->bodega->nombre ?? 'N/A',
                    'cantidad_entrada' => 0,
                    'cantidad_salida' => $detalle->cantidad,
                    'costo_unitario' => $detalle->lote->precio_ingreso ?? 0,
                    'usuario' => $salida->usuario->name ?? 'N/A',
                    'observaciones' => $salida->descripcion,
                ]);
            }
        }

        return $movimientos;
    }

    /**
     * Obtiene los movimientos de traslados
     * Un traslado genera dos movimientos: una salida de bodega origen y una entrada a bodega destino
     */
    private function obtenerMovimientosTraslados(array $filtros): Collection
    {
        $query = Traslado::with([
            'detalles.producto.categoria',
            'detalles.lote',
            'bodegaOrigen',
            'bodegaDestino',
            'usuario',
            'persona'
        ])->where(function($q) {
            $q->where('activo', true)
              ->orWhereNull('activo');
        });

        // Aplicar filtros
        if (isset($filtros['fecha_inicio'])) {
            $query->whereDate('fecha', '>=', $filtros['fecha_inicio']);
        }

        if (isset($filtros['fecha_fin'])) {
            $query->whereDate('fecha', '<=', $filtros['fecha_fin']);
        }

        if (isset($filtros['id_usuario']) && $filtros['id_usuario'] !== '') {
            $query->where('id_usuario', $filtros['id_usuario']);
        }

        // Para traslados, si hay filtro de bodega, mostrar tanto origen como destino
        if (isset($filtros['id_bodega']) && $filtros['id_bodega'] !== '') {
            $query->where(function ($q) use ($filtros) {
                $q->where('id_bodega_origen', $filtros['id_bodega'])
                  ->orWhere('id_bodega_destino', $filtros['id_bodega']);
            });
        }

        $traslados = $query->get();
        $movimientos = collect();

        foreach ($traslados as $traslado) {
            foreach ($traslado->detalles as $detalle) {
                // Filtro por producto si está especificado
                if (isset($filtros['id_producto']) && $filtros['id_producto'] !== '' && $detalle->id_producto !== $filtros['id_producto']) {
                    continue;
                }

                // SALIDA de bodega origen
                // Solo agregar si no hay filtro de bodega o si la bodega origen coincide
                if (!isset($filtros['id_bodega']) || $filtros['id_bodega'] === '' || $filtros['id_bodega'] == $traslado->id_bodega_origen) {
                    $movimientos->push([
                        'fecha' => $traslado->fecha,
                        'tipo_movimiento' => 'TRASLADO SALIDA',
                        'codigo' => $traslado->correlativo ?? $traslado->id,
                        'producto_id' => $detalle->id_producto,
                        'producto' => $detalle->producto->descripcion ?? 'N/A',
                        'categoria' => $detalle->producto->categoria->nombre ?? 'Sin categoría',
                        'descripcion' => 'Salida por traslado',
                        'documento' => 'Traslado No. ' . ($traslado->no_requisicion ?? $traslado->correlativo ?? $traslado->id),
                        'proveedor' => 'Destino: ' . ($traslado->bodegaDestino->nombre ?? 'N/A'),
                        'bodega_id' => $traslado->id_bodega_origen,
                        'bodega' => $traslado->bodegaOrigen->nombre ?? 'N/A',
                        'cantidad_entrada' => 0,
                        'cantidad_salida' => $detalle->cantidad,
                        'costo_unitario' => $detalle->lote->precio_ingreso ?? 0,
                        'usuario' => $traslado->usuario->name ?? 'N/A',
                        'observaciones' => $traslado->descripcion,
                    ]);
                }

                // ENTRADA a bodega destino
                // Solo agregar si no hay filtro de bodega o si la bodega destino coincide
                if (!isset($filtros['id_bodega']) || $filtros['id_bodega'] === '' || $filtros['id_bodega'] == $traslado->id_bodega_destino) {
                    $movimientos->push([
                        'fecha' => $traslado->fecha,
                        'tipo_movimiento' => 'TRASLADO ENTRADA',
                        'codigo' => $traslado->correlativo ?? $traslado->id,
                        'producto_id' => $detalle->id_producto,
                        'producto' => $detalle->producto->descripcion ?? 'N/A',
                        'categoria' => $detalle->producto->categoria->nombre ?? 'Sin categoría',
                        'descripcion' => 'Entrada por traslado',
                        'documento' => 'Traslado No. ' . ($traslado->no_requisicion ?? $traslado->correlativo ?? $traslado->id),
                        'proveedor' => 'Origen: ' . ($traslado->bodegaOrigen->nombre ?? 'N/A'),
                        'bodega_id' => $traslado->id_bodega_destino,
                        'bodega' => $traslado->bodegaDestino->nombre ?? 'N/A',
                        'cantidad_entrada' => $detalle->cantidad,
                        'cantidad_salida' => 0,
                        'costo_unitario' => $detalle->lote->precio_ingreso ?? 0,
                        'usuario' => $traslado->usuario->name ?? 'N/A',
                        'observaciones' => $traslado->descripcion,
                    ]);
                }
            }
        }

        return $movimientos;
    }

    /**
     * Obtiene los movimientos de devoluciones
     */
    private function obtenerMovimientosDevoluciones(array $filtros): Collection
    {
        $query = Devolucion::with([
            'detalles.producto.categoria',
            'detalles.lote',
            'bodega',
            'usuario',
            'persona'
        ]);

        // Aplicar filtros
        if (isset($filtros['fecha_inicio'])) {
            $query->whereDate('fecha', '>=', $filtros['fecha_inicio']);
        }

        if (isset($filtros['fecha_fin'])) {
            $query->whereDate('fecha', '<=', $filtros['fecha_fin']);
        }

        if (isset($filtros['id_bodega']) && $filtros['id_bodega'] !== '') {
            $query->where('id_bodega', $filtros['id_bodega']);
        }

        if (isset($filtros['id_usuario']) && $filtros['id_usuario'] !== '') {
            $query->where('id_usuario', $filtros['id_usuario']);
        }

        $devoluciones = $query->get();
        $movimientos = collect();

        foreach ($devoluciones as $devolucion) {
            foreach ($devolucion->detalles as $detalle) {
                // Filtro por producto si está especificado
                if (isset($filtros['id_producto']) && $filtros['id_producto'] !== '' && $detalle->id_producto !== $filtros['id_producto']) {
                    continue;
                }

                $movimientos->push([
                    'fecha' => $devolucion->fecha,
                    'tipo_movimiento' => 'DEVOLUCION',
                    'codigo' => $devolucion->correlativo ?? $devolucion->id,
                    'producto_id' => $detalle->id_producto,
                    'producto' => $detalle->producto->descripcion ?? 'N/A',
                    'categoria' => $detalle->producto->categoria->nombre ?? 'Sin categoría',
                    'descripcion' => 'Entrada por devolución',
                    'documento' => 'Devolución No. ' . ($devolucion->no_formulario ?? $devolucion->correlativo ?? $devolucion->id),
                    'proveedor' => $devolucion->persona->nombre ?? 'N/A',
                    'bodega_id' => $devolucion->id_bodega,
                    'bodega' => $devolucion->bodega->nombre ?? 'N/A',
                    'cantidad_entrada' => $detalle->cantidad,
                    'cantidad_salida' => 0,
                    'costo_unitario' => $detalle->precio ?? $detalle->lote->precio_ingreso ?? 0,
                    'usuario' => $devolucion->usuario->name ?? 'N/A',
                    'observaciones' => 'Estado: ' . ($detalle->estado ?? 'N/A'),
                ]);
            }
        }

        return $movimientos;
    }

    /**
     * Calcula saldos acumulativos y costos para cada movimiento
     *
     * @param Collection $movimientos Movimientos ordenados cronológicamente
     * @return Collection Movimientos con saldos y costos calculados
     */
    private function calcularSaldosYCostos(Collection $movimientos): Collection
    {
        // Agrupar por producto y bodega para calcular saldos independientes
        $saldosPorProductoBodega = [];

        return $movimientos->map(function ($movimiento) use (&$saldosPorProductoBodega) {
            $key = $movimiento['producto_id'] . '_' . $movimiento['bodega_id'];

            // Inicializar saldo si no existe
            if (!isset($saldosPorProductoBodega[$key])) {
                $saldosPorProductoBodega[$key] = 0;
            }

            // Calcular entrada y salida
            $entrada = $movimiento['cantidad_entrada'];
            $salida = $movimiento['cantidad_salida'];
            $costoUnitario = $movimiento['costo_unitario'];

            // Calcular costos
            $costoEntrada = $entrada * $costoUnitario;
            $costoSalida = $salida * $costoUnitario;

            // Actualizar saldo
            $saldosPorProductoBodega[$key] += ($entrada - $salida);
            $saldoActual = $saldosPorProductoBodega[$key];

            // Calcular costo de inventario (saldo * costo unitario)
            $costoInventario = $saldoActual * $costoUnitario;

            // Agregar campos calculados al movimiento
            $movimiento['saldo'] = $saldoActual;
            $movimiento['costo_entrada'] = round($costoEntrada, 2);
            $movimiento['costo_salida'] = round($costoSalida, 2);
            $movimiento['costo_inventario'] = round($costoInventario, 2);

            return $movimiento;
        });
    }

    /**
     * Obtiene todos los productos con movimientos en el período
     *
     * @param array $filtros
     * @return Collection
     */
    public function obtenerProductosConMovimientos(array $filtros = []): Collection
    {
        $productos = Producto::with('categoria')
            ->where('activo', true);

        if (isset($filtros['id_producto']) && $filtros['id_producto'] !== '') {
            $productos->where('id', $filtros['id_producto']);
        }

        return $productos->get();
    }

    /**
     * Obtiene todas las bodegas activas
     *
     * @return Collection
     */
    public function obtenerBodegas(): Collection
    {
        return Bodega::where('activo', true)->get();
    }
}
