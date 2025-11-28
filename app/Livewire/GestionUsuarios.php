<?php

namespace App\Livewire;

use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Puesto;
use App\Models\Rol;
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
    public $sortField = 'nombre_usuario';  // 'nombre_usuario' o 'nombre_completo'
    public $sortDirection = 'asc';  // 'asc' o 'desc'

    // Propiedades del modal de filtros
    public $showFilterModal = false;
    public $showInactive = false;
    public $filterRol = '';

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
    public $rolId = ''; // Campo Rol
    public $contrasena = '';
    public $estado = true;

    // Password temporal generada
    public $passwordGenerada = '';
    public $mostrarPassword = false;

    // Propiedades para selección de persona existente
    public $searchPersona = '';
    public $showPersonaDropdown = false;
    public $selectedPersona = null;
    public $personaId = null;

    // Propiedades para selección de rol en modal de crear (searchable)
    public $searchRolModal = '';
    public $selectedRolModal = null;

    // Propiedades para selección de rol en modal de editar (searchable)
    public $searchRolEdit = '';
    public $selectedRolEdit = null;

    // Key para forzar recreación de componentes al abrir modal
    public $modalKey = 0;

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
                'rolId' => 'required|exists:rol,id',
            ];
        }

        return [
            'personaId' => 'required|exists:persona,id',
            'nombre_usuario' => 'required|string|max:255|' . $usuarioIdRule,
            'rolId' => 'required|exists:rol,id',
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
        'rolId.required' => 'Debe seleccionar un rol.',
        'rolId.exists' => 'El rol seleccionado no es válido.',
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
     * Actualiza cuando cambia la búsqueda de persona
     */
    public function updatedSearchPersona()
    {
        $this->showPersonaDropdown = true;
    }

    /**
     * Obtiene personas filtradas para el autocompletado
     * Solo muestra personas activas que NO tienen usuario asignado
     * Límite dinámico: 7 sin búsqueda, 25 con búsqueda activa
     */
    public function getPersonaResultsProperty()
    {
        $query = Persona::where('estado', true)
            ->whereDoesntHave('usuario'); // Solo personas sin usuario

        $hasSearch = !empty($this->searchPersona);

        if ($hasSearch) {
            $search = $this->searchPersona;
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'like', '%' . $search . '%')
                    ->orWhere('apellidos', 'like', '%' . $search . '%')
                    ->orWhere('dpi', 'like', '%' . $search . '%');
            });
        }

        // Límite dinámico: 7 sin búsqueda (para no abrumar), 25 con búsqueda (ya filtrado)
        $limit = $hasSearch ? 25 : 7;

        return $query->orderBy('nombres')->limit($limit)->get()->map(function ($persona) {
            return [
                'id' => $persona->id,
                'label' => "{$persona->nombres} {$persona->apellidos}",
                'sublabel' => "DPI: {$persona->dpi}",
                'nombre' => "{$persona->nombres} {$persona->apellidos}", // Fallback compatibility
            ];
        });
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

            // Generar nombre de usuario automáticamente
            $this->generarNombreUsuario($persona);
        }
    }

    /**
     * Genera un nombre de usuario único basado en la persona
     * Formato: [Inicial Nombre][Apellido][Secuencia] (ej. jalvarado1)
     */
    public function generarNombreUsuario($persona)
    {
        if (!$persona)
            return;

        $primerNombre = Str::slug(explode(' ', trim($persona->nombres))[0]);
        $primerApellido = Str::slug(explode(' ', trim($persona->apellidos))[0]);

        $base = substr($primerNombre, 0, 1) . $primerApellido;
        $contador = 1;
        $usuarioGenerado = $base . $contador;

        // Buscar el siguiente disponible
        while (Usuario::where('nombre_usuario', $usuarioGenerado)->exists()) {
            $contador++;
            $usuarioGenerado = $base . $contador;
        }

        $this->nombre_usuario = $usuarioGenerado;
    }

    /**
     * Obtiene roles filtrados para el modal de crear usuario
     */
    public function getRolModalResultsProperty()
    {
        $roles = $this->roles->toArray();

        if (empty($this->searchRolModal)) {
            return array_slice($roles, 0, 10);
        }

        $search = strtolower(trim($this->searchRolModal));

        return array_filter($roles, function ($rol) use ($search) {
            return str_contains(strtolower($rol['nombre']), $search);
        });
    }

    /**
     * Selecciona un rol en el modal de crear
     */
    public function selectRolModal($id)
    {
        $rol = $this->roles->firstWhere('id', $id);
        if ($rol) {
            $this->selectedRolModal = [
                'id' => $rol->id,
                'nombre' => $rol->nombre,
            ];
            $this->rolId = $rol->id;
        }
    }

    /**
     * Limpia la selección de rol en modal
     */
    public function clearRolModal()
    {
        $this->selectedRolModal = null;
        $this->rolId = '';
    }

    /**
     * Limpia la selección de persona
     */
    public function clearPersona()
    {
        $this->selectedPersona = null;
        $this->personaId = null;
        $this->nombre_usuario = ''; // Limpiar usuario generado
    }

    /**
     * Obtiene roles filtrados para el modal de editar usuario
     */
    public function getRolEditResultsProperty()
    {
        $roles = $this->roles->toArray();

        if (empty($this->searchRolEdit)) {
            return array_slice($roles, 0, 10);
        }

        $search = strtolower(trim($this->searchRolEdit));

        return array_filter($roles, function ($rol) use ($search) {
            return str_contains(strtolower($rol['nombre']), $search);
        });
    }

    /**
     * Selecciona un rol en el modal de editar
     */
    public function selectRolEdit($id)
    {
        $rol = $this->roles->firstWhere('id', $id);
        if ($rol) {
            $this->selectedRolEdit = [
                'id' => $rol->id,
                'nombre' => $rol->nombre,
            ];
            $this->rolId = $rol->id;
        }
    }

    /**
     * Limpia la selección de rol en modal de editar
     */
    public function clearRolEdit()
    {
        $this->selectedRolEdit = null;
        $this->rolId = '';
    }

    /**
     * Maneja el evento cuando se crea una persona
     */
    public function handlePersonaCreada($personaData, $mensaje)
    {
        // Seleccionar automáticamente la persona recién creada
        $persona = Persona::find($personaData['id']);

        if ($persona) {
            $this->selectedPersona = [
                'id' => $persona->id,
                'nombre_completo' => "{$persona->nombres} {$persona->apellidos}",
                'dpi' => $persona->dpi,
            ];
            $this->personaId = $persona->id;
            $this->showPersonaDropdown = false;

            // Generar usuario para la nueva persona
            $this->generarNombreUsuario($persona);
        }

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
        $this->modalKey++; // Incrementar key para forzar recreación de componentes
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
                'id_rol' => $this->rolId, // Guardar Rol
                'estado' => true,
                'debe_cambiar_contrasena' => true, // Forzar cambio de contraseña en primer login
            ]);

            // 5. Registrar en bitácora
            Bitacora::create([
                'accion' => 'Crear',
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
        $this->rolId = $usuario->id_rol; // Cargar Rol
        $this->estado = $usuario->estado;

        // Cargar rol seleccionado para el dropdown
        $rol = $this->roles->firstWhere('id', $usuario->id_rol);
        if ($rol) {
            $this->selectedRolEdit = [
                'id' => $rol->id,
                'nombre' => $rol->nombre,
            ];
            $this->rolId = $rol->id;
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
                'id_rol' => $this->rolId, // Actualizar Rol
                'estado' => $this->estado,
            ]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Actualizar',
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
        $this->rolId = ""; // Resetear Rol
        $this->contrasena = '';
        $this->estado = true;
        $this->usuarioId = null;
        $this->passwordGenerada = '';
        $this->mostrarPassword = false;

        // Resetear campos de persona
        $this->searchPersona = '';
        $this->showPersonaDropdown = false;
        $this->selectedPersona = null;
        $this->personaId = null;

        // Resetear campos de rol modal
        $this->searchRolModal = '';
        $this->selectedRolModal = null;

        // Resetear campos de rol edit
        $this->searchRolEdit = '';
        $this->selectedRolEdit = null;
    }

    /**
     * Resetea (genera nueva) contraseña de un usuario
     */
    public function resetearPassword($id)
    {
        $usuario = Usuario::with('persona')->findOrFail($id);

        if (!$usuario->persona) {
            session()->flash('error', 'Error: El usuario no tiene una persona asignada.');
            return;
        }

        $this->usuarioId = $id;
        $this->nombre_usuario = $usuario->nombre_usuario;

        // Generar nueva contraseña
        $this->generarPassword();

        try {
            DB::beginTransaction();

            // Actualizar contraseña y marcar que debe cambiarla
            $usuario->update([
                'contrasena' => Hash::make($this->passwordGenerada),
                'debe_cambiar_contrasena' => true, // Forzar cambio de contraseña
            ]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Resetear Password',
                'modelo' => 'Usuario',
                'modelo_id' => $usuario->id,
                'descripcion' => "Contraseña reseteada para usuario: {$this->nombre_usuario} ({$usuario->persona->nombres} {$usuario->persona->apellidos})",
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
            $accion = $nuevoEstado ? 'Activar' : 'Desactivar';
            Bitacora::create([
                'accion' => $accion,
                'modelo' => 'Usuario',
                'modelo_id' => $usuario->id,
                'descripcion' => "Usuario " . ($nuevoEstado ? 'activado' : 'desactivado') . ": {$usuario->nombre_usuario}",
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
            ->whereHas('persona'); // Solo usuarios que tienen persona asignada

        // Lógica para mostrar/ocultar inactivos
        if (!$this->showInactive) {
            $query->where('usuario.estado', true);
        }

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
        if (!empty($this->filterRol)) {
            $query->where('id_rol', $this->filterRol);
        }

        // Aplicar ordenamiento
        switch ($this->sortField) {
            case 'nombre_completo':
                $query->join('persona', 'usuario.id_persona', '=', 'persona.id')
                    ->orderBy('persona.nombres', $this->sortDirection)
                    ->orderBy('persona.apellidos', $this->sortDirection)
                    ->select('usuario.*'); // Evitar conflictos de ID
                break;

            case 'rol':
                $query->join('rol', 'usuario.id_rol', '=', 'rol.id')
                    ->orderBy('rol.nombre', $this->sortDirection)
                    ->select('usuario.*');
                break;

            case 'estado':
                $query->orderBy('usuario.estado', $this->sortDirection);
                break;

            case 'nombre_usuario':
            default:
                $query->orderBy('nombre_usuario', $this->sortDirection ?? 'asc');
                break;
        }

        return $query->paginate(30);
    }

    public function openFilterModal()
    {
        $this->showFilterModal = true;
    }

    public function closeFilterModal()
    {
        $this->showFilterModal = false;
    }

    public function clearFilters()
    {
        $this->filterRol = '';
        $this->showInactive = false;
        $this->resetPage();
    }

    /**
     * Obtiene la lista de roles
     */
    public function getRolesProperty()
    {
        return Rol::orderBy('nombre')->get();
    }

    /**
     * Renderiza la vista del componente
     */
    public function render()
    {
        return view('livewire.gestion-usuarios', [
            'usuarios' => $this->usuarios,
            'roles' => $this->roles,
        ]);
    }
}
