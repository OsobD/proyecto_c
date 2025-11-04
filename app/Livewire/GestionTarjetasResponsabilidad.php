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
    public $fecha_creacion;
    public $total = 0;

    // Para búsqueda de personas
    public $searchPersona = '';
    public $personasDisponibles = [];
    public $personaSeleccionada = null;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'id_persona' => 'required|exists:persona,id',
        'fecha_creacion' => 'required|date',
        'total' => 'numeric|min:0',
    ];

    protected $messages = [
        'id_persona.required' => 'Debe seleccionar una persona.',
        'id_persona.exists' => 'La persona seleccionada no existe.',
        'fecha_creacion.required' => 'La fecha de creación es obligatoria.',
        'fecha_creacion.date' => 'La fecha de creación debe ser válida.',
        'total.numeric' => 'El total debe ser un número.',
        'total.min' => 'El total no puede ser negativo.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSearchPersona()
    {
        if (strlen($this->searchPersona) >= 2) {
            $this->personasDisponibles = Persona::where('estado', true)
                ->where(function($query) {
                    $query->where('nombres', 'like', '%' . $this->searchPersona . '%')
                          ->orWhere('apellidos', 'like', '%' . $this->searchPersona . '%')
                          ->orWhere('correo', 'like', '%' . $this->searchPersona . '%');
                })
                ->whereDoesntHave('tarjetasResponsabilidad', function($query) {
                    $query->where('activo', true);
                })
                ->limit(10)
                ->get();
        } else {
            $this->personasDisponibles = [];
        }
    }

    public function selectPersona($personaId)
    {
        $persona = Persona::findOrFail($personaId);
        $this->personaSeleccionada = $persona;
        $this->id_persona = $persona->id;
        $this->searchPersona = "{$persona->nombres} {$persona->apellidos}";
        $this->personasDisponibles = [];
    }

    public function clearPersona()
    {
        $this->personaSeleccionada = null;
        $this->id_persona = null;
        $this->searchPersona = '';
        $this->personasDisponibles = [];
    }

    public function render()
    {
        $tarjetas = TarjetaResponsabilidad::with('persona')
            ->where('activo', true)
            ->whereHas('persona', function($query) {
                $query->where('estado', true)
                      ->where(function($q) {
                          $q->where('nombres', 'like', '%' . $this->search . '%')
                            ->orWhere('apellidos', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('fecha_creacion', 'desc')
            ->paginate(10);

        return view('livewire.gestion-tarjetas-responsabilidad', [
            'tarjetas' => $tarjetas
        ]);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->fecha_creacion = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function edit($id)
    {
        $tarjeta = TarjetaResponsabilidad::with('persona')->findOrFail($id);

        $this->tarjetaId = $tarjeta->id;
        $this->id_persona = $tarjeta->id_persona;
        $this->fecha_creacion = $tarjeta->fecha_creacion;
        $this->total = $tarjeta->total;

        $this->personaSeleccionada = $tarjeta->persona;
        $this->searchPersona = "{$tarjeta->persona->nombres} {$tarjeta->persona->apellidos}";

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->editMode) {
                $tarjeta = TarjetaResponsabilidad::findOrFail($this->tarjetaId);

                $tarjeta->update([
                    'fecha_creacion' => $this->fecha_creacion,
                    'total' => $this->total,
                    'updated_by' => Auth::id(),
                ]);

                $persona = Persona::find($tarjeta->id_persona);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Actualizar',
                    'descripcion' => "Tarjeta de responsabilidad actualizada para: {$persona->nombres} {$persona->apellidos}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                $mensaje = 'Tarjeta de responsabilidad actualizada correctamente.';
            } else {
                // Verificar que la persona no tenga ya una tarjeta activa
                $tarjetaExistente = TarjetaResponsabilidad::where('id_persona', $this->id_persona)
                    ->where('activo', true)
                    ->exists();

                if ($tarjetaExistente) {
                    session()->flash('error', 'Esta persona ya tiene una tarjeta de responsabilidad activa.');
                    return;
                }

                $tarjeta = TarjetaResponsabilidad::create([
                    'id_persona' => $this->id_persona,
                    'fecha_creacion' => $this->fecha_creacion,
                    'total' => $this->total,
                    'activo' => true,
                    'created_by' => Auth::id(),
                ]);

                $persona = Persona::find($this->id_persona);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Crear',
                    'descripcion' => "Tarjeta de responsabilidad creada para: {$persona->nombres} {$persona->apellidos}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                $mensaje = 'Tarjeta de responsabilidad creada correctamente.';
            }

            $this->closeModal();
            $this->dispatch('tarjeta-saved');
            session()->flash('message', $mensaje);
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

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Desactivar',
                'descripcion' => "Tarjeta de responsabilidad desactivada para: {$tarjeta->persona->nombres} {$tarjeta->persona->apellidos}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Tarjeta de responsabilidad desactivada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al desactivar la tarjeta: ' . $e->getMessage());
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
        $this->fecha_creacion = '';
        $this->total = 0;
        $this->searchPersona = '';
        $this->personasDisponibles = [];
        $this->personaSeleccionada = null;
        $this->resetErrorBag();
    }
}
