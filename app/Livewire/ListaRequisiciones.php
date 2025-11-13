<?php

namespace App\Livewire;

use App\Models\Salida;
use App\Models\Traslado;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente ListaRequisiciones
 *
 * Lista todas las requisiciones del sistema, tanto Salidas como Traslados
 * originados desde requisiciones.
 */
class ListaRequisiciones extends Component
{
    use WithPagination;

    /** @var string Término de búsqueda */
    public $search = '';

    /** @var string Filtro por tipo (todos, salida, traslado) */
    public $filtroTipo = 'todos';

    /** @var string Campo de ordenamiento */
    public $sortBy = 'fecha';

    /** @var string Dirección de ordenamiento (asc, desc) */
    public $sortDirection = 'desc';

    /**
     * Reinicia la paginación cuando cambia la búsqueda
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Reinicia la paginación cuando cambia el filtro
     */
    public function updatingFiltroTipo()
    {
        $this->resetPage();
    }

    /**
     * Cambia el ordenamiento
     */
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Obtiene las requisiciones combinadas de Salidas y Traslados
     */
    public function getRequisicionesProperty()
    {
        $requisiciones = collect();

        // Obtener Salidas (productos no consumibles)
        if ($this->filtroTipo === 'todos' || $this->filtroTipo === 'salida') {
            $salidas = Salida::with(['persona', 'bodega', 'detalles.producto'])
                ->when($this->search, function($query) {
                    $query->where(function($q) {
                        $q->where('ubicacion', 'like', '%' . $this->search . '%')
                          ->orWhereHas('persona', function($pq) {
                              $pq->whereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ['%' . $this->search . '%']);
                          });
                    });
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->get()
                ->map(function($salida) {
                    return [
                        'id' => $salida->id,
                        'tipo' => 'Salida',
                        'tipo_badge' => 'No Consumibles',
                        'tipo_color' => 'blue',
                        'correlativo' => $salida->ubicacion,
                        'fecha' => $salida->fecha,
                        'persona' => $salida->persona ? $salida->persona->nombres . ' ' . $salida->persona->apellidos : 'N/A',
                        'bodega' => $salida->bodega ? $salida->bodega->nombre : 'N/A',
                        'total' => $salida->total,
                        'productos_count' => $salida->detalles->count(),
                        'descripcion' => $salida->descripcion,
                    ];
                });

            $requisiciones = $requisiciones->merge($salidas);
        }

        // Obtener Traslados (productos consumibles desde requisiciones)
        if ($this->filtroTipo === 'todos' || $this->filtroTipo === 'traslado') {
            $traslados = Traslado::with(['tarjetaResponsabilidad.persona', 'bodegaOrigen', 'detalles.producto'])
                ->whereNotNull('no_requisicion') // Solo traslados de requisiciones
                ->when($this->search, function($query) {
                    $query->where(function($q) {
                        $q->where('correlativo', 'like', '%' . $this->search . '%')
                          ->orWhere('no_requisicion', 'like', '%' . $this->search . '%')
                          ->orWhereHas('tarjetaResponsabilidad.persona', function($pq) {
                              $pq->whereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ['%' . $this->search . '%']);
                          });
                    });
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->get()
                ->map(function($traslado) {
                    $persona = $traslado->tarjetaResponsabilidad && $traslado->tarjetaResponsabilidad->persona
                        ? $traslado->tarjetaResponsabilidad->persona->nombres . ' ' . $traslado->tarjetaResponsabilidad->persona->apellidos
                        : 'N/A';

                    return [
                        'id' => $traslado->id,
                        'tipo' => 'Traslado',
                        'tipo_badge' => 'Consumibles',
                        'tipo_color' => 'amber',
                        'correlativo' => $traslado->no_requisicion ?? $traslado->correlativo,
                        'fecha' => $traslado->fecha,
                        'persona' => $persona,
                        'bodega' => $traslado->bodegaOrigen ? $traslado->bodegaOrigen->nombre : 'N/A',
                        'total' => $traslado->total,
                        'productos_count' => $traslado->detalles->count(),
                        'descripcion' => $traslado->observaciones,
                    ];
                });

            $requisiciones = $requisiciones->merge($traslados);
        }

        // Ordenar la colección combinada
        if ($this->sortBy === 'fecha') {
            $requisiciones = $this->sortDirection === 'desc'
                ? $requisiciones->sortByDesc('fecha')
                : $requisiciones->sortBy('fecha');
        } elseif ($this->sortBy === 'correlativo') {
            $requisiciones = $this->sortDirection === 'desc'
                ? $requisiciones->sortByDesc('correlativo')
                : $requisiciones->sortBy('correlativo');
        }

        return $requisiciones->values();
    }

    public function render()
    {
        return view('livewire.lista-requisiciones', [
            'requisiciones' => $this->requisiciones
        ]);
    }
}
