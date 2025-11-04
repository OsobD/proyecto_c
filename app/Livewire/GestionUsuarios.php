<?php

namespace App\Livewire;

use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Rol;
use App\Models\Bitacora;
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
    public $filterRol = '';
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
    public $telefono = '';
    public $correo = '';
    public $fecha_nacimiento = '';
    public $genero = '';

    // Datos de Usuario
    public $nombre_usuario = '';
    public $rolId = '';
    public $contrasena = '';
    public $estado = true;

    // Password temporal generada
    public $passwordGenerada = '';
    public $mostrarPassword = false;

    // Propiedades para autocompletado de rol
    public $searchRol = '';
    public $showRolDropdown = false;
    public $selectedRol = null;

    protected $paginationTheme = 'tailwind';

    /**
     * Reglas de validación para crear usuario
     */
    protected function rules()
    {
        $usuarioIdRule = $this->editMode ? 'unique:usuario,nombre_usuario,' . $this->usuarioId : 'unique:usuario,nombre_usuario';

        return [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|max:255',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F',
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
        'telefono.required' => 'El teléfono es obligatorio.',
        'correo.required' => 'El correo electrónico es obligatorio.',
        'correo.email' => 'El correo debe ser una dirección válida.',
        'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
        'genero.required' => 'El género es obligatorio.',
        'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
        'nombre_usuario.unique' => 'Este nombre de usuario ya está en uso.',
        'rolId.required' => 'Debe seleccionar un rol.',
        'rolId.exists' => 'El rol seleccionado no es válido.',
    ];

    /**
     * Resetea la paginación cuando cambia la búsqueda
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Resetea la paginación cuando cambia el filtro de rol
     */
    public function updatingFilterRol()
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
     * Actualiza cuando cambia la búsqueda de rol
     */
    public function updatedSearchRol()
    {
        $this->showRolDropdown = true;
    }

    /**
     * Obtiene roles filtrados para el autocompletado
     */
    public function getRolResultsProperty()
    {
        $roles = $this->roles->toArray();

        if (empty($this->searchRol)) {
            return $roles;
        }

        $search = strtolower(trim($this->searchRol));

        return array_filter($roles, function($rol) use ($search) {
            return str_contains(strtolower($rol['nombre']), $search);
        });
    }

    /**
     * Selecciona un rol del autocompletado
     */
    public function selectRol($id)
    {
        $rol = $this->roles->firstWhere('id', $id);
        if ($rol) {
            $this->selectedRol = [
                'id' => $rol->id,
                'nombre' => $rol->nombre,
            ];
            $this->rolId = $rol->id;
            $this->showRolDropdown = false;
            $this->searchRol = '';
        }
    }

    /**
     * Limpia la selección de rol
     */
    public function clearRol()
    {
        $this->selectedRol = null;
        $this->rolId = '';
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
     * Guarda un nuevo usuario con sincronización en ambas tablas
     */
    public function guardarUsuario()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // 1. Crear la Persona
            $persona = Persona::create([
                'nombres' => $this->nombres,
                'apellidos' => $this->apellidos,
                'telefono' => $this->telefono,
                'correo' => $this->correo,
                'fecha_nacimiento' => $this->fecha_nacimiento,
                'genero' => $this->genero,
                'estado' => true,
            ]);

            // 2. Generar contraseña si no existe
            if (empty($this->passwordGenerada)) {
                $this->generarPassword();
            }

            // 3. Crear registro de usuario
            $usuario = Usuario::create([
                'nombre_usuario' => $this->nombre_usuario,
                'contrasena' => Hash::make($this->passwordGenerada),
                'id_persona' => $persona->id,
                'id_rol' => $this->rolId,
                'estado' => true,
            ]);

            // 4. Registrar en bitácora
            Bitacora::create([
                'accion' => 'crear',
                'modelo' => 'Usuario',
                'modelo_id' => $usuario->id,
                'descripcion' => "Usuario creado: {$this->nombre_usuario} ({$this->nombres} {$this->apellidos})",
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
        $this->telefono = $usuario->persona->telefono;
        $this->correo = $usuario->persona->correo;
        $this->fecha_nacimiento = $usuario->persona->fecha_nacimiento?->format('Y-m-d');
        $this->genero = $usuario->persona->genero;

        // Cargar datos de usuario
        $this->nombre_usuario = $usuario->nombre_usuario;
        $this->rolId = $usuario->id_rol;
        $this->estado = $usuario->estado;

        // Cargar rol seleccionado para el dropdown
        $rol = $this->roles->firstWhere('id', $usuario->id_rol);
        if ($rol) {
            $this->selectedRol = [
                'id' => $rol->id,
                'nombre' => $rol->nombre,
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
                'telefono' => $this->telefono,
                'correo' => $this->correo,
                'fecha_nacimiento' => $this->fecha_nacimiento,
                'genero' => $this->genero,
            ]);

            // Actualizar Usuario
            $usuario->update([
                'id_rol' => $this->rolId,
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
        $this->telefono = '';
        $this->correo = '';
        $this->fecha_nacimiento = '';
        $this->genero = '';
        $this->nombre_usuario = '';
        $this->rolId = '';
        $this->contrasena = '';
        $this->estado = true;
        $this->usuarioId = null;
        $this->passwordGenerada = '';
        $this->mostrarPassword = false;
        $this->selectedRol = null;
        $this->searchRol = '';
        $this->showRolDropdown = false;
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
            ->whereNotNull('id_rol'); // Solo usuarios con rol (usuarios reales)

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

        return $query->paginate(10);
    }

    /**
     * Obtiene la lista de roles para el filtro
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
