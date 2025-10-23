<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Traits\TienePermisos;

/**
 * @class HistorialCompras
 * @package App\Livewire
 * @brief Componente para visualizar y gestionar el historial de compras.
 *
 * Este componente muestra una lista paginada de las compras registradas,
 * permitiendo a los usuarios buscar y filtrar por rango de fechas, estado y
 * proveedor. También incluye funcionalidades para editar, activar y desactivar
 * compras, protegidas por un sistema de permisos a través del trait `TienePermisos`.
 */
class HistorialCompras extends Component
{
    use WithPagination, TienePermisos;

    // --- PROPIEDADES DE FILTRADO Y BÚSQUEDA ---

    /** @var string Término de búsqueda para facturas o proveedores. */
    public $search = '';
    /** @var string Fecha de inicio para el filtro de rango. */
    public $fechaInicio = '';
    /** @var string Fecha de fin para el filtro de rango. */
    public $fechaFin = '';
    /** @var string Estado seleccionado para filtrar ('Completada', 'Pendiente'). */
    public $estadoFiltro = '';
    /** @var int|string ID del proveedor seleccionado para filtrar. */
    public $proveedorFiltro = '';

    // --- PROPIEDADES DE DATOS ---

    /** @var array Lista de todas las compras (datos de ejemplo). */
    public $compras = [];
    /** @var array Lista de proveedores para el filtro. */
    public $proveedores = [];

    // --- PROPIEDADES DEL MODAL DE EDICIÓN ---

    /** @var bool Controla la visibilidad del modal de edición. */
    public $showModalEditar = false;
    /** @var array|null Datos de la compra seleccionada para editar. */
    public $compraSeleccionada = null;

    // --- MÉTODOS DE CICLO DE VIDA ---

    /**
     * @brief Se ejecuta al inicializar el componente. Carga datos de ejemplo.
     * @return void
     */
    public function mount()
    {
        $this->proveedores = [
            ['id' => 1, 'nombre' => 'Ferretería El Martillo Feliz'],
            ['id' => 2, 'nombre' => 'Suministros Industriales S.A.'],
        ];

        $this->compras = [
            ['id' => 1, 'numero_factura' => 'FAC-001', 'proveedor' => 'Ferretería El Martillo Feliz', 'proveedor_id' => 1, 'fecha' => '2025-10-18', 'monto' => 5250.00, 'estado' => 'Completada', 'activa' => true],
            ['id' => 2, 'numero_factura' => 'FAC-002', 'proveedor' => 'Suministros Industriales S.A.', 'proveedor_id' => 2, 'fecha' => '2025-10-17', 'monto' => 12800.00, 'estado' => 'Pendiente', 'activa' => true],
            ['id' => 3, 'numero_factura' => 'FAC-003', 'proveedor' => 'Ferretería El Martillo Feliz', 'proveedor_id' => 1, 'fecha' => '2025-10-16', 'monto' => 8450.00, 'estado' => 'Completada', 'activa' => false],
        ];
    }

    /**
     * @brief Hook que se ejecuta antes de actualizar la propiedad de búsqueda.
     * Reinicia la paginación para mostrar los resultados desde la primera página.
     * @return void
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // --- MÉTODOS DE FILTRADO ---

    /**
     * @brief Aplica todos los filtros seleccionados a la lista de compras.
     * @return array La lista de compras filtrada.
     */
    public function getComprasFiltradas()
    {
        $compras = collect($this->compras);
        $search = strtolower($this->search);

        if ($this->search) {
            $compras = $compras->filter(fn($c) => str_contains(strtolower($c['numero_factura']), $search) || str_contains(strtolower($c['proveedor']), $search));
        }
        if ($this->proveedorFiltro) {
            $compras = $compras->where('proveedor_id', $this->proveedorFiltro);
        }
        if ($this->estadoFiltro) {
            $compras = $compras->where('estado', $this->estadoFiltro);
        }
        if ($this->fechaInicio) {
            $compras = $compras->where('fecha', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $compras = $compras->where('fecha', '<=', $this->fechaFin);
        }

        return $compras->values()->all();
    }

    /**
     * @brief Reinicia todos los filtros a sus valores por defecto.
     * @return void
     */
    public function limpiarFiltros()
    {
        $this->reset(['search', 'fechaInicio', 'fechaFin', 'estadoFiltro', 'proveedorFiltro']);
        $this->resetPage();
    }

    // --- MÉTODOS DE ACCIONES ---

    /**
     * @brief Simula la visualización del detalle de una compra.
     * @param int $compraId ID de la compra.
     * @return void
     */
    public function verDetalle($compraId)
    {
        session()->flash('message', 'Mostrando detalle de compra #' . $compraId);
    }

    /**
     * @brief Abre el modal para editar una compra, verificando permisos.
     * @param int $compraId ID de la compra a editar.
     * @return void
     */
    public function editarCompra($compraId)
    {
        if (!$this->verificarPermiso('compras.editar', 'Solo supervisores pueden editar.')) return;

        $this->compraSeleccionada = collect($this->compras)->firstWhere('id', $compraId);
        $this->showModalEditar = true;
    }

    /**
     * @brief Desactiva una compra, verificando permisos.
     * @param int $compraId ID de la compra a desactivar.
     * @return void
     */
    public function desactivarCompra($compraId)
    {
        if (!$this->verificarPermiso('compras.desactivar', 'Solo supervisores pueden desactivar.')) return;

        $this->compras = array_map(function($c) use ($compraId) {
            if ($c['id'] === $compraId) $c['activa'] = false;
            return $c;
        }, $this->compras);
        session()->flash('message', 'Compra desactivada.');
    }

    /**
     * @brief Activa una compra.
     * @param int $compraId ID de la compra a activar.
     * @return void
     */
    public function activarCompra($compraId)
    {
        $this->compras = array_map(function($c) use ($compraId) {
            if ($c['id'] === $compraId) $c['activa'] = true;
            return $c;
        }, $this->compras);
        session()->flash('message', 'Compra activada.');
    }

    /**
     * @brief Cierra el modal de edición.
     * @return void
     */
    public function closeModalEditar()
    {
        $this->showModalEditar = false;
        $this->compraSeleccionada = null;
    }

    /**
     * @brief Renderiza la vista del componente, pasando las compras filtradas.
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.historial-compras', [
            'comprasFiltradas' => $this->getComprasFiltradas(),
        ]);
    }
}
