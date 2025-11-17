<?php

namespace App\Livewire;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Livewire\Traits\TienePermisos;

/**
 * Componente ComprasHub
 *
 * Dashboard principal del módulo de compras. Muestra estadísticas del mes
 * y un resumen de las compras más recientes con funcionalidades de ver y editar.
 *
 * @package App\Livewire
 * @see resources/views/livewire/compras-hub.blade.php
 */
class ComprasHub extends Component
{
    use TienePermisos;

    /** @var bool Controla visibilidad del modal de visualización */
    public $showModalVer = false;

    /** @var bool Controla visibilidad del modal de edición */
    public $showModalEditar = false;

    /** @var bool Controla visibilidad del modal de confirmación de edición */
    public $showModalConfirmarEdicion = false;

    /** @var bool Controla visibilidad del modal de confirmación de desactivación */
    public $showModalConfirmarDesactivar = false;

    /** @var array|null Compra seleccionada para ver/editar */
    public $compraSeleccionada = null;

    /** @var int|null ID de compra a desactivar */
    public $compraIdDesactivar = null;

    /**
     * OPTIMIZACIÓN: Estadísticas del mes con caché
     * Solo se calcula una vez y se reutiliza
     *
     * @return array
     */
    #[Computed]
    public function estadisticas()
    {
        // Obtener compras del mes actual (una sola consulta con agregados)
        $comprasMes = Compra::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->selectRaw('COUNT(*) as total, SUM(total) as monto_total')
            ->first();

        // Calcular monto sin IVA (dividir entre 1.12 para quitar el 12% de IVA)
        $montoSinIva = ($comprasMes->monto_total ?? 0) / 1.12;

        return [
            'total_mes' => $comprasMes->total ?? 0,
            'monto_total_mes' => $montoSinIva,
            'pendientes_revision' => 0,
            'proveedores_activos' => Proveedor::where('activo', true)->count(),
        ];
    }

    /**
     * OPTIMIZACIÓN: Compras recientes con caché
     * Solo se carga una vez y se reutiliza
     *
     * @return array
     */
    #[Computed]
    public function comprasRecientes()
    {
        return Compra::with('proveedor')
            ->orderBy('fecha', 'desc')
            ->limit(5)
            ->get()
            ->map(function($compra) {
                return [
                    'id' => $compra->id,
                    'numero_factura' => $compra->no_factura ?? 'N/A',
                    'proveedor' => $compra->proveedor->nombre ?? 'Sin proveedor',
                    'fecha' => $compra->fecha->format('Y-m-d'),
                    'monto' => $compra->total / 1.12, // Calcular monto sin IVA
                    'estado' => 'Completada',
                    'activa' => $compra->activo ?? true,
                ];
            })
            ->toArray();
    }

    public function verDetalle($compraId)
    {
        $compra = Compra::with(['proveedor', 'detalles.producto', 'bodega'])->find($compraId);

        if ($compra) {
            // Mapear productos y calcular total correctamente
            $productos = $compra->detalles->map(function($detalle) {
                $subtotal = $detalle->cantidad * $detalle->precio_ingreso;
                return [
                    'codigo' => $detalle->id_producto,
                    'descripcion' => $detalle->producto->descripcion ?? 'N/A',
                    'cantidad' => $detalle->cantidad,
                    'precio' => $detalle->precio_ingreso,
                    'subtotal' => $subtotal,
                ];
            })->toArray();

            // Calcular el total sumando todos los subtotales
            $totalCalculado = array_sum(array_column($productos, 'subtotal'));

            $this->compraSeleccionada = [
                'id' => $compra->id,
                'numero_factura' => $compra->no_factura,
                'correlativo' => $compra->correlativo,
                'fecha' => $compra->fecha->format('Y-m-d H:i'),
                'proveedor' => $compra->proveedor->nombre ?? 'Sin proveedor',
                'bodega' => $compra->bodega->nombre ?? 'Sin bodega',
                'total' => $totalCalculado > 0 ? $totalCalculado : ($compra->total / 1.12),
                'productos' => $productos,
            ];
            $this->showModalVer = true;
        }
    }

    public function closeModalVer()
    {
        $this->showModalVer = false;
        $this->compraSeleccionada = null;
    }

    public function editarCompra($compraId)
    {
        if (!$this->verificarPermiso('compras.editar', 'Solo supervisores pueden editar compras.')) {
            return;
        }

        $compra = Compra::with(['proveedor', 'detalles.producto', 'bodega'])->find($compraId);

        if ($compra) {
            // Mapear productos con sus detalles para edición
            $productos = $compra->detalles->map(function($detalle) {
                $subtotal = $detalle->cantidad * $detalle->precio_ingreso;
                return [
                    'id_detalle' => $detalle->id,
                    'codigo' => $detalle->id_producto,
                    'descripcion' => $detalle->producto->descripcion ?? 'N/A',
                    'cantidad' => $detalle->cantidad,
                    'precio' => $detalle->precio_ingreso,
                    'subtotal' => $subtotal,
                ];
            })->toArray();

            // Calcular el total
            $totalCalculado = array_sum(array_column($productos, 'subtotal'));

            $this->compraSeleccionada = [
                'id' => $compra->id,
                'numero_factura' => $compra->no_factura,
                'correlativo' => $compra->correlativo,
                'fecha' => $compra->fecha->format('Y-m-d H:i'),
                'proveedor' => $compra->proveedor->nombre ?? 'Sin proveedor',
                'bodega' => $compra->bodega->nombre ?? 'Sin bodega',
                'total' => $totalCalculado > 0 ? $totalCalculado : ($compra->total / 1.12),
                'productos' => $productos,
            ];
            $this->showModalEditar = true;
        }
    }

    public function closeModalEditar()
    {
        $this->showModalEditar = false;
        $this->compraSeleccionada = null;
    }

    public function abrirModalConfirmarEdicion()
    {
        // Recalcular el total antes de confirmar
        if ($this->compraSeleccionada && isset($this->compraSeleccionada['productos'])) {
            $total = 0;
            foreach ($this->compraSeleccionada['productos'] as $index => $producto) {
                $subtotal = $producto['cantidad'] * $producto['precio'];
                $this->compraSeleccionada['productos'][$index]['subtotal'] = $subtotal;
                $total += $subtotal;
            }
            $this->compraSeleccionada['total'] = $total;
        }
        $this->showModalConfirmarEdicion = true;
    }

    public function closeModalConfirmarEdicion()
    {
        $this->showModalConfirmarEdicion = false;
    }

    public function guardarEdicion()
    {
        try {
            $compra = Compra::find($this->compraSeleccionada['id']);

            if (!$compra) {
                session()->flash('error', 'Compra no encontrada.');
                return;
            }

            // Actualizar los detalles de la compra (cantidad y precio)
            foreach ($this->compraSeleccionada['productos'] as $producto) {
                $detalle = DetalleCompra::find($producto['id_detalle']);
                if ($detalle) {
                    $detalle->cantidad = $producto['cantidad'];
                    $detalle->precio_ingreso = $producto['precio'];
                    $detalle->save();
                }
            }

            // Actualizar el total de la compra
            $compra->total = $this->compraSeleccionada['total'];
            $compra->save();

            session()->flash('message', 'Compra actualizada exitosamente.');

            $this->closeModalConfirmarEdicion();
            $this->closeModalEditar();

            // Recargar datos
            $this->mount();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la compra: ' . $e->getMessage());
        }
    }

    public function abrirModalDesactivar($compraId)
    {
        if (!$this->verificarPermiso('compras.desactivar', 'Solo supervisores pueden desactivar compras.')) {
            return;
        }

        $this->compraIdDesactivar = $compraId;
        $this->showModalConfirmarDesactivar = true;
    }

    public function closeModalConfirmarDesactivar()
    {
        $this->showModalConfirmarDesactivar = false;
        $this->compraIdDesactivar = null;
    }

    public function confirmarDesactivar()
    {
        try {
            $compra = Compra::find($this->compraIdDesactivar);

            if (!$compra) {
                session()->flash('error', 'Compra no encontrada.');
                return;
            }

            $compra->activo = false;
            $compra->save();

            session()->flash('message', 'Compra desactivada exitosamente.');

            $this->closeModalConfirmarDesactivar();

            // Recargar las compras recientes
            $this->mount();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al desactivar la compra: ' . $e->getMessage());
        }
    }

    public function activarCompra($compraId)
    {
        try {
            $compra = Compra::find($compraId);

            if (!$compra) {
                session()->flash('error', 'Compra no encontrada.');
                return;
            }

            $compra->activo = true;
            $compra->save();

            session()->flash('message', 'Compra activada exitosamente.');

            // Recargar las compras recientes
            $this->mount();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al activar la compra: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.compras-hub');
    }
}
