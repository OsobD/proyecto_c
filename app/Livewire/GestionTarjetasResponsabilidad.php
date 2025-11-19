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
    public $showModal = false;
    public $editMode = false;

    // Campos del formulario
    public $tarjetaId;
    public $id_persona;

    // Para mostrar la persona seleccionada en el modal
    public $personaSeleccionada = null;

    // Para búsqueda de personas
    public $searchPersona = '';
    public $personasDisponibles = [];

    // Para el acordeón de productos (similar a bodegas)
    public $tarjetaIdExpandida = null;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['personaCreada' => 'handlePersonaCreada'];

    protected $rules = [
        'id_persona' => 'required|exists:persona,id',
    ];

    protected $messages = [
        'id_persona.required' => 'Debe seleccionar una persona.',
        'id_persona.exists' => 'La persona seleccionada no existe.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSearchPersona()
    {
        if (strlen($this->searchPersona) >= 2) {
            // Buscar personas que no tengan tarjeta de responsabilidad activa
            $this->personasDisponibles = Persona::where('estado', true)
                ->whereDoesntHave('tarjetasResponsabilidad', function($query) {
                    $query->where('activo', true);
                })
                ->where(function($query) {
                    $query->where('nombres', 'like', '%' . $this->searchPersona . '%')
                          ->orWhere('apellidos', 'like', '%' . $this->searchPersona . '%')
                          ->orWhere('correo', 'like', '%' . $this->searchPersona . '%');
                })
                ->limit(10)
                ->get();
        } else {
            $this->personasDisponibles = [];
        }
    }


    public function render()
    {
        $query = TarjetaResponsabilidad::with('persona')
            ->where('activo', true);

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

    public function openModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $tarjeta = TarjetaResponsabilidad::with('persona')->findOrFail($id);

        if (!$tarjeta->persona) {
            session()->flash('error', 'Error: La tarjeta no tiene una persona asignada.');
            return;
        }

        $this->tarjetaId = $tarjeta->id;
        $this->id_persona = $tarjeta->id_persona;

        // Establecer la persona seleccionada para mostrar en el modal
        $this->personaSeleccionada = $tarjeta->persona;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        try {
            // Validar que haya una persona seleccionada
            if (!$this->personaSeleccionada) {
                session()->flash('error', 'Debe seleccionar una persona o crear una nueva.');
                return;
            }

            // Validar id_persona
            $this->validate([
                'id_persona' => 'required|exists:persona,id',
            ]);

            // Verificar si la persona ya tiene una tarjeta activa
            $tarjetaExistente = TarjetaResponsabilidad::where('id_persona', $this->personaSeleccionada->id)
                ->where('activo', true)
                ->first();

            if ($tarjetaExistente) {
                session()->flash('error', 'Esta persona ya tiene una tarjeta de responsabilidad activa.');
                return;
            }

            // Crear tarjeta para la persona seleccionada
            $tarjeta = TarjetaResponsabilidad::create([
                'nombre' => "{$this->personaSeleccionada->nombres} {$this->personaSeleccionada->apellidos}",
                'id_persona' => $this->personaSeleccionada->id,
                'fecha_creacion' => now(),
                'total' => 0,
                'activo' => true,
                'created_by' => Auth::id(),
            ]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Crear',
                'modelo' => 'TarjetaResponsabilidad',
                'modelo_id' => $tarjeta->id,
                'descripcion' => "Tarjeta de responsabilidad creada para: {$this->personaSeleccionada->nombres} {$this->personaSeleccionada->apellidos}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            $this->closeModal();
            $this->dispatch('tarjeta-saved');
            session()->flash('message', 'Tarjeta de responsabilidad creada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar la tarjeta: ' . $e->getMessage());
        }
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
                'descripcion' => "Tarjeta de responsabilidad desactivada para: {$personaDescripcion}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Tarjeta de responsabilidad desactivada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al desactivar la tarjeta: ' . $e->getMessage());
        }
    }

    public function selectPersona($personaId)
    {
        $this->personaSeleccionada = Persona::find($personaId);
        if ($this->personaSeleccionada) {
            $this->id_persona = $this->personaSeleccionada->id;
            $this->personasDisponibles = [];
            $this->searchPersona = '';
        }
    }

    public function clearPersona()
    {
        $this->personaSeleccionada = null;
        $this->id_persona = null;
        $this->searchPersona = '';
        $this->personasDisponibles = [];
    }

    public function handlePersonaCreada($personaData, $mensaje)
    {
        // Cuando se crea una persona desde ModalPersona, auto-seleccionarla
        $persona = Persona::find($personaData['id']);
        if ($persona) {
            $this->personaSeleccionada = $persona;
            $this->id_persona = $persona->id;
            $this->searchPersona = '';
            $this->personasDisponibles = [];
            session()->flash('message', $mensaje);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->tarjetaId = null;
        $this->id_persona = null;
        $this->personaSeleccionada = null;
        $this->searchPersona = '';
        $this->personasDisponibles = [];
        $this->resetErrorBag();
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
