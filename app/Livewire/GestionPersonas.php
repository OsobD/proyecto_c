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
    
    // Propiedades del modal de filtros
    public $showFilterModal = false;
    public $showInactive = false; // Renombrado de showAllPersonas para consistencia

    // Propiedades de ordenamiento
    public $sortField = 'nombres';
    public $sortDirection = 'asc';

    // Para el acordeón de productos consumibles
    public $personaIdConsumiblesExpandida = null;

    // Campos del formulario
    public $personaId;
    public $nombres;
    public $apellidos;
    public $dpi;
    public $telefono;
    public $correo;

    // Key para forzar recreación de componentes al abrir modal
    public $modalKey = 0;

    protected $paginationTheme = 'tailwind';

    protected $listeners = ['personaCreada' => 'handlePersonaCreada'];

    protected $rules = [
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'dpi' => 'required|digits:13',
        'telefono' => 'nullable|digits:8',
        'correo' => 'nullable|email:rfc,dns|max:255',
    ];

    protected $messages = [
        'nombres.required' => 'Los nombres son obligatorios.',
        'apellidos.required' => 'Los apellidos son obligatorios.',
        'dpi.required' => 'El DPI es obligatorio.',
        'dpi.digits' => 'El DPI debe tener exactamente 13 dígitos numéricos.',
        'dpi.unique' => 'Ya existe una persona registrada con este DPI.',
        'telefono.digits' => 'El teléfono debe tener exactamente 8 dígitos numéricos.',
        'telefono.unique' => 'Ya existe una persona registrada con este teléfono.',
        'correo.email' => 'El correo debe ser una dirección de email válida (ej: usuario@dominio.com).',
        'correo.unique' => 'Ya existe una persona registrada con este correo.',
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

        // Filtrar por estado si no queremos ver todas (inactivas)
        if (!$this->showInactive) {
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

        $personas = $query->paginate(10);

        return view('livewire.gestion-personas', [
            'personas' => $personas
        ]);
    }

    public function openModal()
    {
        // Validar permiso para crear personas
        if (!auth()->user()->tienePermiso('personas.crear')) {
            session()->flash('error', 'No tienes permiso para crear personas.');
            return;
        }

        $this->resetForm();
        $this->editMode = false;
        $this->modalKey++;
        $this->showModal = true;
    }

    public function edit($id)
    {
        // Validar permiso para editar personas
        if (!auth()->user()->puedeEditar('personas')) {
            session()->flash('error', 'No tienes permiso para editar personas.');
            return;
        }

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
        // Validar permisos según el modo
        if ($this->editMode) {
            if (!auth()->user()->puedeEditar('personas')) {
                session()->flash('error', 'No tienes permiso para editar personas.');
                return;
            }
        } else {
            if (!auth()->user()->puedeCrear('personas')) {
                session()->flash('error', 'No tienes permiso para crear personas.');
                return;
            }
        }

        // Validar campos únicos (DPI, teléfono, correo) solo si es nueva persona o si cambió el valor
        $validationRules = $this->rules;
        if ($this->editMode) {
            $validationRules['dpi'] = 'required|digits:13|unique:persona,dpi,' . $this->personaId;

            // Validar teléfono único solo si no está vacío
            if (!empty($this->telefono)) {
                $validationRules['telefono'] = 'nullable|digits:8|unique:persona,telefono,' . $this->personaId;
            } else {
                $validationRules['telefono'] = 'nullable';
            }

            // Validar correo único solo si no está vacío
            if (!empty($this->correo)) {
                $validationRules['correo'] = 'nullable|email:rfc,dns|max:255|unique:persona,correo,' . $this->personaId;
            } else {
                $validationRules['correo'] = 'nullable';
            }
        } else {
            $validationRules['dpi'] = 'required|digits:13|unique:persona,dpi';

            // Validar teléfono único solo si no está vacío
            if (!empty($this->telefono)) {
                $validationRules['telefono'] = 'nullable|digits:8|unique:persona,telefono';
            } else {
                $validationRules['telefono'] = 'nullable';
            }

            // Validar correo único solo si no está vacío
            if (!empty($this->correo)) {
                $validationRules['correo'] = 'nullable|email:rfc,dns|max:255|unique:persona,correo';
            } else {
                $validationRules['correo'] = 'nullable';
            }
        }

        $this->validate($validationRules);

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

    /**
     * Alterna el estado de una persona (activar/desactivar)
     * Si se desactiva, también desactiva su tarjeta de responsabilidad
     */
    public function toggleEstado($id)
    {
        // Validar permiso para eliminar/desactivar personas
        if (!auth()->user()->puedeEliminar('personas')) {
            session()->flash('error', 'No tienes permiso para activar/desactivar personas.');
            return;
        }

        try {
            $persona = Persona::with('tarjetasResponsabilidad')->findOrFail($id);
            $nuevoEstado = !$persona->estado;

            // Actualizar estado de la persona
            $persona->update(['estado' => $nuevoEstado]);

            // Sincronizar estado de la tarjeta de responsabilidad
            foreach ($persona->tarjetasResponsabilidad as $tarjeta) {
                $tarjeta->update(['activo' => $nuevoEstado]);
            }

            // Registrar en bitácora
            $accion = $nuevoEstado ? 'activar' : 'desactivar';
            $descripcion = "Persona {$accion}da: {$persona->nombres} {$persona->apellidos}";

            if (!$nuevoEstado && $persona->tarjetasResponsabilidad->count() > 0) {
                $descripcion .= " (tarjeta de responsabilidad desactivada)";
            }

            Bitacora::create([
                'accion' => $accion,
                'modelo' => 'Persona',
                'modelo_id' => $persona->id,
                'descripcion' => $descripcion,
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            $mensaje = $nuevoEstado ? 'Persona activada exitosamente.' : 'Persona desactivada exitosamente.';
            if (!$nuevoEstado && $persona->tarjetasResponsabilidad->count() > 0) {
                $mensaje .= ' Su tarjeta de responsabilidad también fue desactivada.';
            }

            session()->flash('message', $mensaje);

        } catch (\Exception $e) {
            session()->flash('error', 'Error al cambiar el estado: ' . $e->getMessage());
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

    /**
     * Toggle para expandir/contraer la lista de productos consumibles solicitados por una persona
     */
    public function toggleConsumibles($personaId)
    {
        if ($this->personaIdConsumiblesExpandida === $personaId) {
            $this->personaIdConsumiblesExpandida = null;
        } else {
            $this->personaIdConsumiblesExpandida = $personaId;
        }
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
        $this->sortField = 'nombres';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }
}
