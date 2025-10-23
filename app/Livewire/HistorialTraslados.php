<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

/**
 * @class HistorialTraslados
 * @package App\Livewire
 * @brief Componente para visualizar y gestionar el historial de traslados.
 *
 * Muestra una lista paginada de todos los movimientos de inventario que no son
 * compras, como requisiciones, devoluciones y traslados entre bodegas.
 * Permite filtrar por tipo de movimiento, estado, fecha y un término de búsqueda general.
 * Incluye acciones para ver detalles, editar, activar y desactivar registros.
 */
class HistorialTraslados extends Component
{
    use WithPagination;

    // --- PROPIEDADES DE FILTRADO Y BÚSQUEDA ---

    /** @var string Término de búsqueda general (correlativo, origen, destino, usuario). */
    public $search = '';
    /** @var string Fecha de inicio para el filtro de rango. */
    public $fechaInicio = '';
    /** @var string Fecha de fin para el filtro de rango. */
    public $fechaFin = '';
    /** @var string Tipo de movimiento a filtrar ('Requisición', 'Traslado', 'Devolución'). */
    public $tipoFiltro = '';
    /** @var string Estado del movimiento a filtrar ('Completado', 'Pendiente'). */
    public $estadoFiltro = '';

    // --- PROPIEDADES DE DATOS ---

    /** @var array Lista de todos los traslados (datos de ejemplo). */
    public $traslados = [];

    // --- PROPIEDADES DEL MODAL ---

    /** @var bool Controla la visibilidad del modal de edición. */
    public $showModalEditar = false;
    /** @var array|null Datos del traslado seleccionado para editar. */
    public $trasladoSeleccionado = null;

    // --- MÉTODOS DE CICLO DE VIDA ---

    /**
     * @brief Se ejecuta al inicializar el componente. Carga datos de ejemplo.
     * @return void
     */
    public function mount()
    {
        $this->traslados = [
            ['id' => 1, 'tipo' => 'Requisición', 'correlativo' => 'REQ-001', 'origen' => 'Bodega Central', 'destino' => 'Área de Mantenimiento', 'usuario' => 'Juan Pérez', 'fecha' => '2025-10-18', 'estado' => 'Completado', 'activo' => true],
            ['id' => 2, 'tipo' => 'Traslado', 'correlativo' => 'TRA-005', 'origen' => 'Bodega Norte', 'destino' => 'Bodega Sur', 'usuario' => 'María García', 'fecha' => '2025-10-17', 'estado' => 'Pendiente', 'activo' => true],
            ['id' => 3, 'tipo' => 'Devolución', 'correlativo' => 'DEV-002', 'origen' => 'Área de Producción', 'destino' => 'Bodega Central', 'usuario' => 'Carlos López', 'fecha' => '2025-10-16', 'estado' => 'Completado', 'activo' => false],
        ];
    }

    /**
     * @brief Hook que se ejecuta antes de actualizar la propiedad de búsqueda.
     * Reinicia la paginación.
     * @return void
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // --- MÉTODOS DE FILTRADO ---

    /**
     * @brief Aplica todos los filtros a la lista de traslados.
     * @return array La lista de traslados filtrada.
     */
    public function getTrasladosFiltrados()
    {
        return collect($this->traslados)
            ->when($this->search, function ($query, $search) {
                $search = strtolower($search);
                return $query->filter(fn($t) =>
                    str_contains(strtolower($t['correlativo']), $search) ||
                    str_contains(strtolower($t['origen']), $search) ||
                    str_contains(strtolower($t['destino']), $search) ||
                    str_contains(strtolower($t['usuario']), $search)
                );
            })
            ->when($this->tipoFiltro, fn($query) => $query->where('tipo', $this->tipoFiltro))
            ->when($this->estadoFiltro, fn($query) => $query->where('estado', $this->estadoFiltro))
            ->when($this->fechaInicio, fn($query) => $query->where('fecha', '>=', $this->fechaInicio))
            ->when($this->fechaFin, fn($query) => $query->where('fecha', '<=', $this->fechaFin))
            ->values()
            ->all();
    }

    /**
     * @brief Reinicia todos los filtros a sus valores por defecto.
     * @return void
     */
    public function limpiarFiltros()
    {
        $this->reset(['search', 'fechaInicio', 'fechaFin', 'tipoFiltro', 'estadoFiltro']);
        $this->resetPage();
    }

    // --- MÉTODOS DE ACCIONES ---

    /**
     * @brief Simula la visualización del detalle de un traslado.
     * @param int $trasladoId ID del traslado.
     * @return void
     */
    public function verDetalle($trasladoId)
    {
        session()->flash('message', 'Mostrando detalle de traslado #' . $trasladoId);
    }

    /**
     * @brief Abre el modal para editar un traslado.
     * @param int $trasladoId ID del traslado a editar.
     * @return void
     */
    public function editarTraslado($trasladoId)
    {
        $this->trasladoSeleccionado = collect($this->traslados)->firstWhere('id', $trasladoId);
        $this->showModalEditar = true;
    }

    /**
     * @brief Desactiva un traslado.
     * @param int $trasladoId ID del traslado a desactivar.
     * @return void
     */
    public function desactivarTraslado($trasladoId)
    {
        $this->traslados = array_map(function($t) use ($trasladoId) {
            if ($t['id'] === $trasladoId) $t['activo'] = false;
            return $t;
        }, $this->traslados);
        session()->flash('message', 'Traslado desactivado.');
    }

    /**
     * @brief Activa un traslado.
     * @param int $trasladoId ID del traslado a activar.
     * @return void
     */
    public function activarTraslado($trasladoId)
    {
        $this->traslados = array_map(function($t) use ($trasladoId) {
            if ($t['id'] === $trasladoId) $t['activo'] = true;
            return $t;
        }, $this->traslados);
        session()->flash('message', 'Traslado activado.');
    }

    /**
     * @brief Cierra el modal de edición.
     * @return void
     */
    public function closeModalEditar()
    {
        $this->showModalEditar = false;
        $this->trasladoSeleccionado = null;
    }

    /**
     * @brief Renderiza la vista del componente.
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.historial-traslados', [
            'trasladosFiltrados' => $this->getTrasladosFiltrados(),
        ]);
    }
}
