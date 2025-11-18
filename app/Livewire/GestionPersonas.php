<?php

namespace App\Livewire;

use App\Models\Persona;
use App\Models\Bitacora;
use App\Models\TarjetaResponsabilidad;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class GestionPersonas extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $showAllPersonas = false; // Para mostrar inactivas también

    // Propiedades de ordenamiento
    public $sortField = null;  // 'id', 'nombres', 'apellidos', 'dpi'
    public $sortDirection = null;  // 'asc' o 'desc'

    // Campos del formulario
    public $personaId;
    public $nombres;
    public $apellidos;
    public $dpi;
    public $telefono;
    public $correo;

    protected $paginationTheme = 'tailwind';

    protected $listeners = ['personaCreada' => 'handlePersonaCreada'];

    protected $rules = [
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'dpi' => 'required|string|size:13',
        'telefono' => 'nullable|string|max:20',
        'correo' => 'nullable|email|max:255',
    ];

    protected $messages = [
        'nombres.required' => 'Los nombres son obligatorios.',
        'apellidos.required' => 'Los apellidos son obligatorios.',
        'dpi.required' => 'El DPI es obligatorio.',
        'dpi.size' => 'El DPI debe tener exactamente 13 dígitos.',
        'dpi.unique' => 'Ya existe una persona registrada con este DPI. El DPI debe ser único.',
        'correo.email' => 'El correo debe ser una dirección válida.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Alterna el ordenamiento por campo
     * Triple-click: alfabético → inverso → sin filtro
     */
    public function sortBy($field)
    {
        // Si es un campo diferente, empezar con orden ascendente
        if ($this->sortField !== $field) {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        // Si es el mismo campo, alternar: asc → desc → null
        else {
            if ($this->sortDirection === 'asc') {
                $this->sortDirection = 'desc';
            } elseif ($this->sortDirection === 'desc') {
                $this->sortField = null;
                $this->sortDirection = null;
            }
        }

        $this->resetPage();
    }

    public function render()
    {
        $query = Persona::query();

        // Filtrar por estado si no queremos ver todas
        if (!$this->showAllPersonas) {
            $query->where('estado', true);
        }

        // Aplicar búsqueda
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('nombres', 'like', '%' . $this->search . '%')
                  ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                  ->orWhere('dpi', 'like', '%' . $this->search . '%')
                  ->orWhere('correo', 'like', '%' . $this->search . '%')
                  ->orWhere('telefono', 'like', '%' . $this->search . '%');
            });
        }

        // Aplicar ordenamiento
        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            // Orden por defecto: por apellidos y nombres
            $query->orderBy('apellidos', 'asc')
                  ->orderBy('nombres', 'asc');
        }

        $personas = $query->paginate(30);

        return view('livewire.gestion-personas', [
            'personas' => $personas
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
        $persona = Persona::findOrFail($id);

        $this->personaId = $persona->id;
        $this->nombres = $persona->nombres;
        $this->apellidos = $persona->apellidos;
        $this->dpi = $persona->dpi;
        $this->telefono = $persona->telefono;
        $this->correo = $persona->correo;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        // Validar DPI único solo si es nueva persona o si cambió el DPI
        $dpiRules = $this->rules;
        if ($this->editMode) {
            $dpiRules['dpi'] = 'required|string|size:13|unique:persona,dpi,' . $this->personaId;
        } else {
            $dpiRules['dpi'] = 'required|string|size:13|unique:persona,dpi';
        }

        $this->validate($dpiRules);

        try {
            if ($this->editMode) {
                $persona = Persona::findOrFail($this->personaId);
                $persona->update([
                    'nombres' => $this->nombres,
                    'apellidos' => $this->apellidos,
                    'dpi' => $this->dpi,
                    'telefono' => $this->telefono,
                    'correo' => $this->correo,
                ]);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Actualizar',
                    'modelo' => 'Persona',
                    'modelo_id' => $persona->id,
                    'descripcion' => "Persona actualizada: {$persona->nombres} {$persona->apellidos}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                $mensaje = "Persona '{$persona->nombres} {$persona->apellidos}' actualizada exitosamente.";
            } else {
                // Crear persona
                $persona = Persona::create([
                    'nombres' => $this->nombres,
                    'apellidos' => $this->apellidos,
                    'dpi' => $this->dpi,
                    'telefono' => $this->telefono,
                    'correo' => $this->correo,
                    'estado' => true,
                ]);

                // Crear tarjeta de responsabilidad automáticamente
                // IMPORTANTE: created_by y updated_by deben ser NULL ya que
                // la foreign key apunta a 'users' pero usamos la tabla 'usuario'
                TarjetaResponsabilidad::create([
                    'nombre' => "{$this->nombres} {$this->apellidos}",
                    'fecha_creacion' => now(),
                    'total' => 0,
                    'id_persona' => $persona->id,
                    'activo' => true,
                    'created_by' => null,
                    'updated_by' => null,
                ]);

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Crear',
                    'modelo' => 'Persona',
                    'modelo_id' => $persona->id,
                    'descripcion' => "Persona creada: {$persona->nombres} {$persona->apellidos} (con tarjeta de responsabilidad)",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);

                $mensaje = "Persona '{$persona->nombres} {$persona->apellidos}' creada exitosamente con tarjeta de responsabilidad.";
            }

            $this->closeModal();
            $this->reset(['search']);
            session()->flash('message', $mensaje);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar la persona: ' . $e->getMessage());
        }
    }

    public function toggleEstado($id)
    {
        try {
            $persona = Persona::with(['tarjetasResponsabilidad', 'usuario'])->findOrFail($id);

            // Si está activa y queremos desactivarla, verificar relaciones
            if ($persona->estado) {
                $tieneTarjetas = $persona->tarjetasResponsabilidad()->where('activo', true)->exists();
                $tieneUsuario = $persona->usuario()->exists();

                if ($tieneTarjetas) {
                    session()->flash('error', 'No se puede desactivar la persona porque tiene tarjetas de responsabilidad activas.');
                    return;
                }

                // Verificar salidas solo si la columna id_persona existe
                try {
                    $tieneSalidas = $persona->salidas()->exists();
                    if ($tieneSalidas) {
                        session()->flash('error', 'No se puede desactivar la persona porque tiene salidas registradas.');
                        return;
                    }
                } catch (\Exception $e) {
                    // Columna no existe aún
                }

                if ($tieneUsuario) {
                    session()->flash('error', 'No se puede desactivar la persona porque tiene un usuario asociado.');
                    return;
                }
            }

            // Cambiar estado
            $nuevoEstado = !$persona->estado;
            $persona->update(['estado' => $nuevoEstado]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => $nuevoEstado ? 'Activar' : 'Desactivar',
                'modelo' => 'Persona',
                'modelo_id' => $persona->id,
                'descripcion' => ($nuevoEstado ? 'Persona activada: ' : 'Persona desactivada: ') . "{$persona->nombres} {$persona->apellidos}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', $nuevoEstado ? 'Persona activada correctamente.' : 'Persona desactivada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->personaId = null;
        $this->nombres = '';
        $this->apellidos = '';
        $this->dpi = '';
        $this->telefono = '';
        $this->correo = '';
        $this->resetErrorBag();
    }

    /**
     * Maneja el evento cuando se crea una persona desde el modal reutilizable
     */
    public function handlePersonaCreada($personaData, $mensaje)
    {
        // Establecer el mensaje flash
        session()->flash('message', $mensaje);
    }
}
