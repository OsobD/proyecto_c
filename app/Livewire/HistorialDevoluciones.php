<?php

namespace App\Livewire;

use App\Models\Devolucion;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente HistorialDevoluciones
 *
 * Lista completa de devoluciones con filtros avanzados por tipo, fecha y búsqueda.
 * Incluye paginación.
 *
 * @package App\Livewire
 * @see resources/views/livewire/historial-devoluciones.blade.php
 */
class HistorialDevoluciones extends Component
{
    use WithPagination;

    /** @var string Término de búsqueda */
    public $search = '';

    /** @var string Fecha inicio para filtro de rango */
    public $fechaInicio = '';

    /** @var string Fecha fin para filtro de rango */
    public $fechaFin = '';

    /** @var bool Controla visibilidad del modal de detalle */
    public $showModalVer = false;

    /** @var array|null Devolución seleccionada para ver */
    public $devolucionSeleccionada = null;

    /**
     * Se ejecuta cuando cambia el término de búsqueda
     *
     * @return void
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Limpia todos los filtros
     *
     * @return void
     */
    public function limpiarFiltros()
    {
        $this->search = '';
        $this->fechaInicio = '';
        $this->fechaFin = '';
        $this->resetPage();
    }

    /**
     * Obtiene todas las devoluciones filtradas desde la BD
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getDevolucionesFiltradas()
    {
        return Devolucion::with([
                'bodega',
                'detalles.producto',
                'usuario'
            ])
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('no_formulario', 'like', '%' . $this->search . '%')
                        ->orWhereHas('bodega', function($q) {
                            $q->where('nombre', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->fechaInicio, function($q) {
                $q->where('fecha', '>=', $this->fechaInicio);
            })
            ->when($this->fechaFin, function($q) {
                $q->where('fecha', '<=', $this->fechaFin);
            })
            ->orderBy('fecha', 'desc')
            ->paginate(15);
    }

    /**
     * Muestra el detalle de una devolución
     *
     * @param int $id
     * @return void
     */
    public function verDetalle($id)
    {
        try {
            $devolucion = Devolucion::with([
                    'bodega',
                    'detalles.producto',
                    'detalles.lote',
                    'usuario'
                ])
                ->findOrFail($id);

            $this->devolucionSeleccionada = [
                'id' => $devolucion->id,
                'no_formulario' => $devolucion->no_formulario ?? 'DEV-' . $devolucion->id,
                'bodega' => $devolucion->bodega->nombre ?? 'N/A',
                'fecha' => $devolucion->fecha->format('d/m/Y H:i'),
                'total' => $devolucion->total,
                'usuario' => $devolucion->usuario->name ?? 'Sistema',
                'productos' => $devolucion->detalles->map(function($detalle) {
                    $precio = $detalle->lote ? $detalle->lote->precio_ingreso : 0;
                    return [
                        'codigo' => $detalle->producto->id,
                        'descripcion' => $detalle->producto->descripcion,
                        'cantidad' => $detalle->cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => $detalle->cantidad * $precio,
                    ];
                })->toArray(),
            ];

            $this->showModalVer = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el detalle: ' . $e->getMessage());
        }
    }

    /**
     * Cierra el modal de detalle
     *
     * @return void
     */
    public function closeModalVer()
    {
        $this->showModalVer = false;
        $this->devolucionSeleccionada = null;
    }

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.historial-devoluciones', [
            'devoluciones' => $this->getDevolucionesFiltradas(),
        ]);
    }
}
