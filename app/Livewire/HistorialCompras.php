<?php

namespace App\Livewire;

use App\Models\Compra;
use App\Models\Proveedor;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Traits\TienePermisos;

/**
 * Componente HistorialCompras
 *
 * Lista completa de compras con filtros avanzados por proveedor, estado, fecha y búsqueda.
 * Incluye paginación y control de permisos para editar/desactivar registros.
 *
 * @package App\Livewire
 * @see resources/views/livewire/historial-compras.blade.php
 */
class HistorialCompras extends Component
{
    use WithPagination, TienePermisos;

    /** @var string Término de búsqueda */
    public $search = '';

    /** @var string Fecha inicio para filtro de rango */
    public $fechaInicio = '';

    /** @var string Fecha fin para filtro de rango */
    public $fechaFin = '';

    /** @var string Filtro por estado de compra */
    public $estadoFiltro = '';

    /** @var string Filtro por ID de proveedor */
    public $proveedorFiltro = '';

    /** @var array Listado de proveedores para filtro */
    public $proveedores = [];

    /** @var bool Controla visibilidad del modal de edición */
    public $showModalEditar = false;

    /** @var array|null Compra seleccionada para editar */
    public $compraSeleccionada = null;

    /**
     * Inicializa el componente con datos reales de la base de datos
     *
     * @return void
     */
    public function mount()
    {
        // Cargar proveedores activos para el filtro
        $this->proveedores = Proveedor::where('activo', true)
            ->get()
            ->map(fn($p) => ['id' => $p->id, 'nombre' => $p->nombre])
            ->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->search = '';
        $this->fechaInicio = '';
        $this->fechaFin = '';
        $this->estadoFiltro = '';
        $this->proveedorFiltro = '';
        $this->resetPage();
    }

    public function getComprasFiltradas()
    {
        $query = Compra::with(['proveedor', 'detalles']);

        // Filtro por búsqueda (factura o proveedor)
        if ($this->search) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                $q->where('no_factura', 'like', "%{$search}%")
                  ->orWhere('correlativo', 'like', "%{$search}%")
                  ->orWhereHas('proveedor', function($pq) use ($search) {
                      $pq->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por proveedor
        if ($this->proveedorFiltro) {
            $query->where('id_proveedor', $this->proveedorFiltro);
        }

        // Filtro por rango de fechas
        if ($this->fechaInicio) {
            $query->whereDate('fecha', '>=', $this->fechaInicio);
        }

        if ($this->fechaFin) {
            $query->whereDate('fecha', '<=', $this->fechaFin);
        }

        // Ordenar por fecha descendente
        $query->orderBy('fecha', 'desc');

        return $query->get()->map(function($compra) {
            return [
                'id' => $compra->id,
                'numero_factura' => $compra->no_factura ?? 'N/A',
                'numero_serie' => $compra->correlativo ?? 'N/A',
                'proveedor' => $compra->proveedor->nombre ?? 'Sin proveedor',
                'proveedor_id' => $compra->id_proveedor,
                'fecha' => $compra->fecha->format('Y-m-d'),
                'monto' => $compra->total,
                'estado' => 'Completada',
                'activa' => true,
                'productos_count' => $compra->detalles->count(),
            ];
        })->toArray();
    }

    public function verDetalle($compraId)
    {
        $compra = Compra::with(['proveedor', 'detalles.producto', 'bodega'])->find($compraId);

        if ($compra) {
            $this->compraSeleccionada = [
                'id' => $compra->id,
                'numero_factura' => $compra->no_factura,
                'correlativo' => $compra->correlativo,
                'fecha' => $compra->fecha->format('Y-m-d H:i'),
                'proveedor' => $compra->proveedor->nombre ?? 'Sin proveedor',
                'bodega' => $compra->bodega->nombre ?? 'Sin bodega',
                'total' => $compra->total,
                'productos' => $compra->detalles->map(function($detalle) {
                    return [
                        'codigo' => $detalle->id_producto,
                        'descripcion' => $detalle->producto->descripcion ?? 'N/A',
                        'cantidad' => $detalle->cantidad,
                        'precio' => $detalle->precio_ingreso,
                        'subtotal' => $detalle->cantidad * $detalle->precio_ingreso,
                    ];
                })->toArray(),
            ];
            $this->showModalEditar = true;
        }
    }

    public function editarCompra($compraId)
    {
        if (!$this->verificarPermiso('compras.editar', 'Solo supervisores pueden editar compras.')) {
            return;
        }

        // Por ahora solo mostramos el detalle
        $this->verDetalle($compraId);
    }

    public function desactivarCompra($compraId)
    {
        if (!$this->verificarPermiso('compras.desactivar', 'Solo supervisores pueden desactivar compras.')) {
            return;
        }

        // Nota: La tabla compra no tiene campo 'activo', por lo que esto es solo informativo
        session()->flash('message', 'Funcionalidad de desactivar compra pendiente de implementación.');
    }

    public function activarCompra($compraId)
    {
        // Nota: La tabla compra no tiene campo 'activo', por lo que esto es solo informativo
        session()->flash('message', 'Funcionalidad de activar compra pendiente de implementación.');
    }

    public function closeModalEditar()
    {
        $this->showModalEditar = false;
        $this->compraSeleccionada = null;
    }

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.historial-compras', [
            'comprasFiltradas' => $this->getComprasFiltradas(),
        ]);
    }
}
