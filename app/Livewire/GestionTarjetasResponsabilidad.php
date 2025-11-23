<?php

namespace App\Livewire;

use App\Models\Persona;
use App\Models\TarjetaResponsabilidad;
use App\Models\Bitacora;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class GestionTarjetasResponsabilidad extends Component
{
    use WithPagination;

    public $search = '';
    public $tarjetaId; // ID de la tarjeta a desactivar
    public $showInactive = false; // Mostrar tarjetas desactivadas

    // Modal de filtros
    public $showFilterModal = false;

    // Ordenamiento
    public $sortField = 'fecha_creacion';
    public $sortDirection = 'desc';

    // Para el acordeón de productos
    public $tarjetaIdExpandida = null;

    protected $paginationTheme = 'tailwind';

    protected $listeners = ['personaCreada' => 'handlePersonaCreada'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Alterna el ordenamiento por campo
     */
    public function sortBy($field)
    {
        if ($this->sortField !== $field) {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        } else {
            if ($this->sortDirection === 'asc') {
                $this->sortDirection = 'desc';
            } elseif ($this->sortDirection === 'desc') {
                $this->sortField = null;
                $this->sortDirection = null;
            }
        }
        $this->resetPage();
    }

    /**
     * Abre el modal de filtros
     */
    public function openFilterModal()
    {
        $this->showFilterModal = true;
    }

    /**
     * Cierra el modal de filtros
     */
    public function closeFilterModal()
    {
        $this->showFilterModal = false;
    }

    /**
     * Limpia los filtros
     */
    public function clearFilters()
    {
        $this->showInactive = false;
        $this->sortField = 'fecha_creacion';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }


    public function render()
    {
        $query = TarjetaResponsabilidad::with('persona');

        // Filtrar por estado
        if (!$this->showInactive) {
            $query->where('activo', true);
        }

        // Si hay búsqueda, filtrar por persona
        if (!empty($this->search)) {
            $query->whereHas('persona', function($q) {
                $q->where(function($subq) {
                    $subq->where('nombres', 'like', '%' . $this->search . '%')
                         ->orWhere('apellidos', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Aplicar ordenamiento
        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('fecha_creacion', 'desc');
        }

        $tarjetas = $query->paginate(10);

        return view('livewire.gestion-tarjetas-responsabilidad', [
            'tarjetas' => $tarjetas
        ]);
    }

    public function confirmDelete($id)
    {
        $tarjeta = TarjetaResponsabilidad::with([
            'tarjetasProducto',
            'entradas',
            'salidas',
            'traslados',
            'devoluciones'
        ])->findOrFail($id);

        // Verificar si tiene relaciones activas
        $tieneProductos = $tarjeta->tarjetasProducto()->exists();
        $tieneEntradas = $tarjeta->entradas()->exists();
        $tieneSalidas = $tarjeta->salidas()->exists();
        $tieneTraslados = $tarjeta->traslados()->exists();
        $tieneDevoluciones = $tarjeta->devoluciones()->exists();

        if ($tieneProductos) {
            session()->flash('error', 'No se puede desactivar la tarjeta porque tiene productos asignados.');
            return;
        }

        if ($tieneEntradas) {
            session()->flash('error', 'No se puede desactivar la tarjeta porque tiene entradas registradas.');
            return;
        }

        if ($tieneSalidas) {
            session()->flash('error', 'No se puede desactivar la tarjeta porque tiene salidas registradas.');
            return;
        }

        if ($tieneTraslados) {
            session()->flash('error', 'No se puede desactivar la tarjeta porque tiene traslados registrados.');
            return;
        }

        if ($tieneDevoluciones) {
            session()->flash('error', 'No se puede desactivar la tarjeta porque tiene devoluciones registradas.');
            return;
        }

        $this->tarjetaId = $id;
        $this->dispatch('confirm-delete');
    }

    public function delete()
    {
        try {
            $tarjeta = TarjetaResponsabilidad::with('persona')->findOrFail($this->tarjetaId);

            $tarjeta->update([
                'activo' => false,
                'updated_by' => Auth::id(),
            ]);

            $personaDescripcion = $tarjeta->persona
                ? "{$tarjeta->persona->nombres} {$tarjeta->persona->apellidos}"
                : "Tarjeta #{$tarjeta->id}";

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Desactivar',
                'modelo' => 'TarjetaResponsabilidad',
                'modelo_id' => $tarjeta->id,
                'descripcion' => "Tarjeta de responsabilidad desactivada para: {$personaDescripcion}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Tarjeta de responsabilidad desactivada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al desactivar la tarjeta: ' . $e->getMessage());
        }
    }

    public function handlePersonaCreada($personaData, $mensaje)
    {
        // Cuando se crea una persona desde ModalPersona, mostrar mensaje de éxito
        session()->flash('message', $mensaje);
        $this->reset(['search']); // Limpiar búsqueda para mostrar todas las tarjetas
    }

    /**
     * Toggle para expandir/contraer la lista de productos de una tarjeta
     */
    public function toggleProductos($tarjetaId)
    {
        if ($this->tarjetaIdExpandida === $tarjetaId) {
            $this->tarjetaIdExpandida = null;
        } else {
            $this->tarjetaIdExpandida = $tarjetaId;
        }
    }
}
