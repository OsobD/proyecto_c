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
    public $mostrarDesactivadas = false; // Checkbox para mostrar tarjetas desactivadas

    // Para el acordeón de productos (similar a bodegas)
    public $tarjetaIdExpandida = null;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['personaCreada' => 'handlePersonaCreada'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMostrarDesactivadas()
    {
        $this->resetPage();
    }


    public function render()
    {
        $query = TarjetaResponsabilidad::with('persona')
            ->where('activo', $this->mostrarDesactivadas ? false : true);

        // Si hay búsqueda, filtrar por persona
        if (!empty($this->search)) {
            $query->whereHas('persona', function($q) {
                $q->where('estado', true)
                  ->where(function($subq) {
                      $subq->where('nombres', 'like', '%' . $this->search . '%')
                           ->orWhere('apellidos', 'like', '%' . $this->search . '%');
                  });
            });
        } else {
            // Sin búsqueda, mostrar todas las tarjetas activas con persona activa
            $query->where(function($q) {
                $q->whereHas('persona', function($subq) {
                    $subq->where('estado', true);
                })
                ->orWhereNull('id_persona'); // Incluir tarjetas sin persona para detectar errores
            });
        }

        $tarjetas = $query->orderBy('fecha_creacion', 'desc')
                          ->paginate(30);

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
