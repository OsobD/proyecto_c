<?php

namespace App\Livewire;

use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Puesto;
use App\Models\Bitacora;
use App\Models\TarjetaResponsabilidad;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente GestionUsuarios
 *
 * Gestiona el CRUD completo de usuarios del sistema.
 * Incluye búsqueda, filtros, ordenamiento y registro en bitácora.
 *
 * @package App\Livewire
 * @see resources/views/livewire/gestion-usuarios.blade.php
 */
class GestionUsuarios extends Component
{
    use WithPagination;

    // Propiedades de búsqueda y filtrado
    public $search = '';
    public $filterPuesto = '';
    public $sortField = null;  // 'nombre_usuario' o 'nombre_completo'
    public $sortDirection = null;  // 'asc' o 'desc'

    // Propiedades del modal de creación/edición
    public $showModal = false;
    public $showModalEditar = false;
    public $showModalPassword = false;
    public $editMode = false;
    public $usuarioId = null;

    // Datos de Persona
    public $nombres = '';
    public $apellidos = '';
    public $dpi = '';
    public $telefono = '';
    public $correo = '';

    // Datos de Usuario
    public $nombre_usuario = '';
    public $puestoId = '';
    public $contrasena = '';
    public $estado = true;

    // Password temporal generada
    public $passwordGenerada = '';
    public $mostrarPassword = false;

    // Propiedades para autocompletado de puesto
    public $searchPuesto = '';
    public $showPuestoDropdown = false;
    public $selectedPuesto = null;

    // Propiedades para filtro de puesto mejorado
    public $searchFilterPuesto = '';
    public $showFilterPuestoDropdown = false;
    public $selectedFilterPuesto = null;

    // Propiedades para selección de persona existente
    public $searchPersona = '';
    public $showPersonaDropdown = false;
    public $selectedPersona = null;
    public $personaId = null;

    protected $paginationTheme = 'tailwind';

    protected $listeners = ['personaCreada' => 'handlePersonaCreada'];

    /**
     * Reglas de validación para crear usuario
     */
    protected function rules()
    {
        $usuarioIdRule = $this->editMode ? 'unique:usuario,nombre_usuario,' . $this->usuarioId : 'unique:usuario,nombre_usuario';

        // En modo creación, siempre requerimos una persona seleccionada
        // En modo edición, validamos los datos de la persona
        if ($this->editMode) {
            $dpiRule = 'required|string|size:13|unique:persona,dpi,' . Usuario::find($this->usuarioId)?->id_persona;

            return [
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'dpi' => $dpiRule,
                'telefono' => 'nullable|string|max:20',
                'correo' => 'nullable|email|max:255',
                'nombre_usuario' => 'required|string|max:255|' . $usuarioIdRule,
                'puestoId' => 'required|exists:puesto,id',
            ];
        }

        return [
            'personaId' => 'required|exists:persona,id',
            'nombre_usuario' => 'required|string|max:255|' . $usuarioIdRule,
            'puestoId' => 'required|exists:puesto,id',
        ];
    }

    /**
     * Mensajes de validación en español
     */
    protected $messages = [
        'nombres.required' => 'Los nombres son obligatorios.',
        'apellidos.required' => 'Los apellidos son obligatorios.',
        'dpi.required' => 'El DPI es obligatorio.',
        'dpi.size' => 'El DPI debe tener exactamente 13 dígitos.',
        'dpi.unique' => 'Este DPI ya está registrado.',
        'telefono.string' => 'El teléfono debe ser un texto válido.',
        'correo.email' => 'El correo debe ser una dirección válida.',
        'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
        'nombre_usuario.unique' => 'Este nombre de usuario ya está en uso.',
        'puestoId.required' => 'Debe seleccionar un puesto.',
        'puestoId.exists' => 'El puesto seleccionado no es válido.',
        'personaId.required' => 'Debe seleccionar una persona.',
        'personaId.exists' => 'La persona seleccionada no es válida.',
    ];

    /**
     * Resetea la paginación cuando cambia la búsqueda
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Resetea la paginación cuando cambia el filtro de puesto
     */
    public function updatingFilterPuesto()
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

    /**
     * Actualiza cuando cambia la búsqueda de puesto
     */
    public function updatedSearchPuesto()
    {
        $this->showPuestoDropdown = true;
    }

    /**
     * Obtiene puestos filtrados para el autocompletado
     */
    public function getPuestoResultsProperty()
    {
        $puestos = $this->puestos->toArray();

        if (empty($this->searchPuesto)) {
            return $puestos;
        }

        $search = strtolower(trim($this->searchPuesto));

        return array_filter($puestos, function($puesto) use ($search) {
            return str_contains(strtolower($puesto['nombre']), $search);
        });
    }

    /**
     * Selecciona un puesto del autocompletado
     */
    public function selectPuesto($id)
    {
        $puesto = $this->puestos->firstWhere('id', $id);
        if ($puesto) {
            $this->selectedPuesto = [
                'id' => $puesto->id,
                'nombre' => $puesto->nombre,
            ];
            $this->puestoId = $puesto->id;
            $this->showPuestoDropdown = false;
            $this->searchPuesto = '';
        }
    }

    /**
     * Limpia la selección de puesto
     */
    public function clearPuesto()
    {
        $this->selectedPuesto = null;
        $this->puestoId = "";
    }

    /**
     * Obtiene los puestos filtrados para el filtro de búsqueda
     */
    public function getFilterPuestoResultsProperty()
    {
        $puestos = $this->puestos->toArray();

        if (empty($this->searchFilterPuesto)) {
            return $puestos;
        }

        $search = strtolower(trim($this->searchFilterPuesto));

        return array_filter($puestos, function($puesto) use ($search) {
            return str_contains(strtolower($puesto['nombre']), $search);
        });
    }

    /**
     * Selecciona un puesto del filtro de búsqueda
     */
    public function selectFilterPuesto($id)
    {
        $puesto = $this->puestos->firstWhere('id', $id);
        if ($puesto) {
            $this->selectedFilterPuesto = [
                'id' => $puesto->id,
                'nombre' => $puesto->nombre,
            ];
            $this->filterPuesto = $puesto->id;
            $this->showFilterPuestoDropdown = false;
            $this->searchFilterPuesto = '';
            $this->resetPage(); // Resetear paginación al filtrar
        }
    }

    /**
     * Limpia la selección del filtro de puesto
     */
    public function clearFilterPuesto()
    {
        $this->selectedFilterPuesto = null;
        $this->filterPuesto = '';
        $this->searchFilterPuesto = '';
        $this->resetPage(); // Resetear paginación al limpiar filtro
    }

    /**
     * Actualiza cuando cambia la búsqueda de persona
     */
    public function updatedSearchPersona()
    {
        $this->showPersonaDropdown = true;
    }

    /**
     * Obtiene personas filtradas para el autocompletado
     * Solo muestra personas activas que NO tienen usuario asignado
     */
    public function getPersonaResultsProperty()
    {
        $query = Persona::where('estado', true)
            ->whereDoesntHave('usuario'); // Solo personas sin usuario

        if (!empty($this->searchPersona)) {
            $search = $this->searchPersona;
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'like', '%' . $search . '%')
                  ->orWhere('apellidos', 'like', '%' . $search . '%')
                  ->orWhere('dpi', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('nombres')->limit(10)->get();
    }

    /**
     * Selecciona una persona del autocompletado
     */
    public function selectPersona($id)
    {
        $persona = Persona::find($id);
        if ($persona) {
            $this->selectedPersona = [
                'id' => $persona->id,
                'nombre_completo' => "{$persona->nombres} {$persona->apellidos}",
                'dpi' => $persona->dpi,
            ];
            $this->personaId = $persona->id;
            $this->showPersonaDropdown = false;
            $this->searchPersona = '';
        }
    }

    /**
     * Limpia la selección de persona
     */
    public function clearPersona()
    {
        $this->selectedPersona = null;
        $this->personaId = null;
    }

    /**
     * Maneja el evento cuando se crea una persona
     */
    public function handlePersonaCreada($personaData, $mensaje)
    {
        // Seleccionar automáticamente la persona recién creada
        $this->selectedPersona = $personaData;
        $this->personaId = $personaData['id'];
        $this->showPersonaDropdown = false;

        // Establecer el mensaje flash
        session()->flash('message', $mensaje);
    }

    /**
     * Abre el modal para crear nuevo usuario
     */
    public function abrirModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    /**
     * Cierra el modal de creación
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    /**
     * Genera una contraseña aleatoria segura
     */
    public function generarPassword()
    {
        // Generar password de 12 caracteres con mayúsculas, minúsculas, números y símbolos
        $this->passwordGenerada = Str::password(12, true, true, true, false);
        $this->mostrarPassword = true;
    }

    /**
     * Guarda un nuevo usuario con persona seleccionada
     */
    public function guardarUsuario()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // 1. Usar persona seleccionada
            $persona = Persona::findOrFail($this->personaId);

            // Validar que la persona no tenga ya un usuario asignado
            if ($persona->usuario) {
                session()->flash('error', 'Esta persona ya tiene un usuario asignado.');
                DB::rollBack();
                return;
            }

            // Verificar si la persona ya tiene tarjeta de responsabilidad
            if (!$persona->tarjetasResponsabilidad()->exists()) {
                // 2. Crear tarjeta de responsabilidad si no existe
                // IMPORTANTE: created_by y updated_by deben ser NULL ya que
                // la foreign key apunta a 'users' pero usamos la tabla 'usuario'
                TarjetaResponsabilidad::create([
                    'nombre' => "{$persona->nombres} {$persona->apellidos}",
                    'fecha_creacion' => now(),
                    'total' => 0,
                    'id_persona' => $persona->id,
                    'activo' => true,
                    'created_by' => null,
                    'updated_by' => null,
                ]);
            }

            // 3. Generar contraseña si no existe
            if (empty($this->passwordGenerada)) {
                $this->generarPassword();
            }

            // 4. Crear registro de usuario
            $usuario = Usuario::create([
                'nombre_usuario' => $this->nombre_usuario,
                'contrasena' => Hash::make($this->passwordGenerada),
                'id_persona' => $persona->id,
                'id_puesto' => $this->puestoId,
                'estado' => true,
            ]);

            // 5. Registrar en bitácora
            Bitacora::create([
                'accion' => 'crear',
                'modelo' => 'Usuario',
                'modelo_id' => $usuario->id,
                'descripcion' => "Usuario creado: {$this->nombre_usuario} para {$persona->nombres} {$persona->apellidos}",
                'id_usuario' => auth()->id() ?? 1,
                'created_at' => now(),
            ]);

            DB::commit();

            session()->flash('message', 'Usuario creado exitosamente. Contraseña temporal generada.');

            // Mantener el modal abierto para mostrar la contraseña
            $this->showModal = false;
            $this->showModalPassword = true;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Cierra el modal de contraseña y resetea
     */
    public function closeModalPassword()
    {
        $this->showModalPassword = false;
        $this->resetForm();
    }

    /**
     * Abre el modal para editar usuario
     */
    public function editarUsuario($id)
    {
        $this->resetValidation();
        $this->editMode = true;
        $this->usuarioId = $id;

        $usuario = Usuario::with('persona')->findOrFail($id);

        // Cargar datos de persona
        $this->nombres = $usuario->persona->nombres;
        $this->apellidos = $usuario->persona->apellidos;
        $this->dpi = $usuario->persona->dpi;
        $this->telefono = $usuario->persona->telefono;
        $this->correo = $usuario->persona->correo;

        // Cargar datos de usuario
        $this->nombre_usuario = $usuario->nombre_usuario;
        $this->puestoId = "";
        $this->estado = $usuario->estado;

        // Cargar rol seleccionado para el dropdown
        $puesto = $this->puestos->firstWhere('id', $usuario->id_puesto);
        if ($puesto) {
            $this->selectedPuesto = [
                'id' => $puesto->id,
                'nombre' => $puesto->nombre,
            ];
        }

        $this->showModalEditar = true;
    }

    /**
     * Actualiza un usuario existente
     */
    public function actualizarUsuario()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $usuario = Usuario::with('persona')->findOrFail($this->usuarioId);
            $persona = $usuario->persona;

            // Actualizar Persona
            $persona->update([
                'nombres' => $this->nombres,
                'apellidos' => $this->apellidos,
                'dpi' => $this->dpi,
                'telefono' => $this->telefono,
                'correo' => $this->correo,
            ]);

            // Actualizar Usuario
            $usuario->update([
                'id_puesto' => $this->puestoId,
                'estado' => $this->estado,
            ]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'actualizar',
                'modelo' => 'Usuario',
                'modelo_id' => $usuario->id,
                'descripcion' => "Usuario actualizado: {$this->nombre_usuario}",
                'id_usuario' => auth()->id() ?? 1,
                'created_at' => now(),
            ]);

            DB::commit();

            session()->flash('message', 'Usuario actualizado exitosamente.');
            $this->closeModalEditar();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Cierra el modal de edición
     */
    public function closeModalEditar()
    {
        $this->showModalEditar = false;
        $this->resetForm();
        $this->resetValidation();
    }

    /**
     * Resetea el formulario
     */
    private function resetForm()
    {
        $this->nombres = '';
        $this->apellidos = '';
        $this->dpi = '';
        $this->telefono = '';
        $this->correo = '';
        $this->nombre_usuario = '';
        $this->puestoId = "";
        $this->contrasena = '';
        $this->estado = true;
        $this->usuarioId = null;
        $this->passwordGenerada = '';
        $this->mostrarPassword = false;
        $this->selectedPuesto = null;
        $this->searchPuesto = '';
        $this->showPuestoDropdown = false;

        // Resetear campos de persona
        $this->searchPersona = '';
        $this->showPersonaDropdown = false;
        $this->selectedPersona = null;
        $this->personaId = null;
    }

    /**
     * Resetea (genera nueva) contraseña de un usuario
     */
    public function resetearPassword($id)
    {
        $this->usuarioId = $id;
        $usuario = Usuario::findOrFail($id);
        $this->nombre_usuario = $usuario->nombre_usuario;

        // Generar nueva contraseña
        $this->generarPassword();

        try {
            DB::beginTransaction();

            // Actualizar contraseña
            $usuario->update([
                'contrasena' => Hash::make($this->passwordGenerada),
            ]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'resetear_password',
                'modelo' => 'Usuario',
                'modelo_id' => $usuario->id,
                'descripcion' => "Contraseña reseteada para usuario: {$this->nombre_usuario}",
                'id_usuario' => auth()->id() ?? 1,
                'created_at' => now(),
            ]);

            DB::commit();

            session()->flash('message', 'Contraseña reseteada exitosamente.');
            $this->showModalPassword = true;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al resetear la contraseña: ' . $e->getMessage());
        }
    }

    /**
     * Alterna el estado de un usuario (activar/desactivar)
     */
    public function toggleEstado($id)
    {
        try {
            DB::beginTransaction();

            $usuario = Usuario::findOrFail($id);
            $nuevoEstado = !$usuario->estado;

            // Actualizar estado
            $usuario->update(['estado' => $nuevoEstado]);

            // Registrar en bitácora
            $accion = $nuevoEstado ? 'activar' : 'desactivar';
            Bitacora::create([
                'accion' => $accion,
                'modelo' => 'Usuario',
                'modelo_id' => $usuario->id,
                'descripcion' => "Usuario {$accion}do: {$usuario->nombre_usuario}",
                'id_usuario' => auth()->id() ?? 1,
                'created_at' => now(),
            ]);

            DB::commit();

            $mensaje = $nuevoEstado ? 'Usuario activado exitosamente.' : 'Usuario desactivado exitosamente.';
            session()->flash('message', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene la lista de usuarios con filtros, búsqueda y ordenamiento
     */
    public function getUsuariosProperty()
    {
        $query = Usuario::with(['persona', 'rol'])
            ->whereHas('persona') // Solo usuarios que tienen persona asignada
            ->whereNotNull('id_puesto'); // Solo usuarios con rol (usuarios reales)

        // Aplicar búsqueda
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nombre_usuario', 'like', '%' . $this->search . '%')
                  ->orWhereHas('persona', function ($subQ) {
                      $subQ->where('nombres', 'like', '%' . $this->search . '%')
                           ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                           ->orWhere('correo', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Aplicar filtro por rol
        if (!empty($this->filterPuesto)) {
            $query->where('id_puesto', $this->filterPuesto);
        }

        // Aplicar ordenamiento
        if ($this->sortField === 'nombre_usuario') {
            $query->orderBy('nombre_usuario', $this->sortDirection);
        } elseif ($this->sortField === 'nombre_completo') {
            $query->join('persona', 'usuario.id_persona', '=', 'persona.id')
                  ->orderBy('persona.nombres', $this->sortDirection)
                  ->orderBy('persona.apellidos', $this->sortDirection)
                  ->select('usuario.*');
        } else {
            // Orden por defecto
            $query->orderBy('nombre_usuario', 'asc');
        }

        return $query->paginate(30);
    }

    /**
     * Obtiene la lista de puestos para el filtro
     */
    public function getPuestosProperty()
    {
        return Puesto::orderBy('nombre')->get();
    }

    /**
     * Renderiza la vista del componente
     */
    public function render()
    {
        return view('livewire.gestion-usuarios', [
            'usuarios' => $this->usuarios,
            'puestos' => $this->puestos,
        ]);
    }
}
