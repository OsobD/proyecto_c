<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente HistorialTraslados
 *
 * Lista completa de traslados (requisiciones, traslados, devoluciones) con filtros
 * avanzados por tipo, estado, fecha y búsqueda. Incluye paginación.
 *
 * @package App\Livewire
 * @see resources/views/livewire/historial-traslados.blade.php
 */
class HistorialTraslados extends Component
{
    use WithPagination;

    /** @var string Término de búsqueda */
    public $search = '';

    /** @var string Fecha inicio para filtro de rango */
    public $fechaInicio = '';

    /** @var string Fecha fin para filtro de rango */
    public $fechaFin = '';

    /** @var string Filtro por tipo (Requisición, Traslado, Devolución) */
    public $tipoFiltro = '';

    /** @var string Filtro por estado */
    public $estadoFiltro = '';

    /** @var array Listado de traslados */
    public $traslados = [];

    /** @var bool Controla visibilidad del modal de edición */
    public $showModalEditar = false;

    /** @var array|null Traslado seleccionado para editar */
    public $trasladoSeleccionado = null;

    /**
     * Inicializa el componente con datos mock
     *
     * @todo Conectar con BD: Traslado::with('origen', 'destino')->get()
     * @return void
     */
    public function mount()
    {
        $this->traslados = [
            [
                'id' => 1,
                'tipo' => 'Requisición',
                'correlativo' => 'REQ-001',
                'origen' => 'Bodega Central',
                'destino' => 'Área de Mantenimiento',
                'usuario' => 'Juan Pérez',
                'fecha' => '2025-10-18',
                'estado' => 'Completado',
                'activo' => true,
                'productos_count' => 3,
            ],
            [
                'id' => 2,
                'tipo' => 'Traslado',
                'correlativo' => 'TRA-005',
                'origen' => 'Bodega Norte',
                'destino' => 'Bodega Sur',
                'usuario' => 'María García',
                'fecha' => '2025-10-17',
                'estado' => 'Pendiente',
                'activo' => true,
                'productos_count' => 5,
            ],
            [
                'id' => 3,
                'tipo' => 'Devolución',
                'correlativo' => 'DEV-002',
                'origen' => 'Área de Producción',
                'destino' => 'Bodega Central',
                'usuario' => 'Carlos López',
                'fecha' => '2025-10-16',
                'estado' => 'Completado',
                'activo' => true,
                'productos_count' => 2,
            ],
            [
                'id' => 4,
                'tipo' => 'Requisición',
                'correlativo' => 'REQ-002',
                'origen' => 'Bodega Central',
                'destino' => 'Área de Electricidad',
                'usuario' => 'Ana Martínez',
                'fecha' => '2025-10-15',
                'estado' => 'Completado',
                'activo' => true,
                'productos_count' => 4,
            ],
            [
                'id' => 5,
                'tipo' => 'Traslado',
                'correlativo' => 'TRA-006',
                'origen' => 'Bodega Sur',
                'destino' => 'Bodega Este',
                'usuario' => 'David Bautista',
                'fecha' => '2025-10-14',
                'estado' => 'Completado',
                'activo' => false,
                'productos_count' => 6,
            ],
        ];
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
        $this->tipoFiltro = '';
        $this->estadoFiltro = '';
        $this->resetPage();
    }

    public function getTrasladosFiltrados()
    {
        $traslados = $this->traslados;

        // Filtro por búsqueda
        if ($this->search) {
            $search = strtolower($this->search);
            $traslados = array_filter($traslados, function($traslado) use ($search) {
                return str_contains(strtolower($traslado['correlativo']), $search) ||
                       str_contains(strtolower($traslado['origen']), $search) ||
                       str_contains(strtolower($traslado['destino']), $search) ||
                       str_contains(strtolower($traslado['usuario']), $search);
            });
        }

        // Filtro por tipo
        if ($this->tipoFiltro) {
            $traslados = array_filter($traslados, function($traslado) {
                return $traslado['tipo'] === $this->tipoFiltro;
            });
        }

        // Filtro por estado
        if ($this->estadoFiltro) {
            $traslados = array_filter($traslados, function($traslado) {
                return $traslado['estado'] === $this->estadoFiltro;
            });
        }

        // Filtro por fecha
        if ($this->fechaInicio) {
            $traslados = array_filter($traslados, function($traslado) {
                return $traslado['fecha'] >= $this->fechaInicio;
            });
        }

        if ($this->fechaFin) {
            $traslados = array_filter($traslados, function($traslado) {
                return $traslado['fecha'] <= $this->fechaFin;
            });
        }

        return array_values($traslados);
    }

    public function verDetalle($trasladoId)
    {
        session()->flash('message', 'Mostrando detalle de traslado #' . $trasladoId);
    }

    public function editarTraslado($trasladoId)
    {
        $this->trasladoSeleccionado = collect($this->traslados)->firstWhere('id', $trasladoId);
        $this->showModalEditar = true;
    }

    public function desactivarTraslado($trasladoId)
    {
        $key = array_search($trasladoId, array_column($this->traslados, 'id'));
        if ($key !== false) {
            $this->traslados[$key]['activo'] = false;
            session()->flash('message', 'Traslado desactivado exitosamente.');
        }
    }

    public function activarTraslado($trasladoId)
    {
        $key = array_search($trasladoId, array_column($this->traslados, 'id'));
        if ($key !== false) {
            $this->traslados[$key]['activo'] = true;
            session()->flash('message', 'Traslado activado exitosamente.');
        }
    }

    public function closeModalEditar()
    {
        $this->showModalEditar = false;
        $this->trasladoSeleccionado = null;
    }

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.historial-traslados', [
            'trasladosFiltrados' => $this->getTrasladosFiltrados(),
        ]);
    }
}
