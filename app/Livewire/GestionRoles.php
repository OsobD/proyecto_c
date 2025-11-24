<?php

namespace App\Livewire;

use App\Models\Rol;
use App\Models\Permiso;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

/**
 * Componente GestionRoles
 *
 * Gestiona el CRUD de roles y la asignación de permisos.
 */
class GestionRoles extends Component
{
    use WithPagination;

    // Propiedades de búsqueda
    public $search = '';
    public $sortField = 'nombre';
    public $sortDirection = 'asc';

    // Propiedades del modal
    public $showModal = false;
    public $editMode = false;
    public $rolId = null;

    // Datos del Rol
    public $nombre = '';
    public $selectedPermisos = [];

    // Cache de permisos para el modal
    public $allPermisos = [];

    // Filtrado y organización de permisos en el modal
    public $searchPermisos = '';
    public $modulosAbiertos = [];

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'nombre' => 'required|string|max:255|unique:rol,nombre',
        'selectedPermisos' => 'array',
        'selectedPermisos.*' => 'exists:permiso,id',
    ];

    public function mount()
    {
        $this->allPermisos = Permiso::orderBy('modulo')->orderBy('nombre')->get();
    }

    /**
     * Obtener nombre amigable del módulo
     */
    public function getNombreModuloAmigable($modulo)
    {
        $nombres = [
            'almacenes' => 'Almacenes',
            'aprobaciones' => 'Aprobaciones',
            'bitacora' => 'Bitácora',
            'bodegas' => 'Bodegas',
            'categorias' => 'Categorías',
            'compras' => 'Compras',
            'configuracion' => 'Configuración',
            'lotes' => 'Lotes',
            'movimientos' => 'Movimientos',
            'permisos' => 'Permisos',
            'productos' => 'Productos',
            'proveedores' => 'Proveedores',
            'reportes' => 'Reportes',
            'requisiciones' => 'Requisiciones',
            'roles' => 'Roles',
            'tarjetas' => 'Tarjetas',
            'traslados' => 'Traslados',
            'ubicaciones' => 'Ubicaciones',
            'usuarios' => 'Usuarios',
        ];

        return $nombres[$modulo] ?? ucfirst($modulo);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField !== $field) {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        } else {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        }
    }

    public function abrirModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
        // Recargar permisos por si hubo cambios
        $this->allPermisos = Permiso::orderBy('nombre')->get();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->nombre = '';
        $this->selectedPermisos = [];
        $this->rolId = null;
        $this->searchPermisos = '';
        $this->modulosAbiertos = [];
    }

    public function guardarRol()
    {
        if ($this->editMode) {
            $this->rules['nombre'] = 'required|string|max:255|unique:rol,nombre,' . $this->rolId;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            if ($this->editMode) {
                $rol = Rol::findOrFail($this->rolId);
                $rol->update(['nombre' => $this->nombre]);
                $rol->permisos()->sync($this->selectedPermisos);
                session()->flash('message', 'Rol actualizado exitosamente.');
            } else {
                $rol = Rol::create(['nombre' => $this->nombre]);
                $rol->permisos()->sync($this->selectedPermisos);
                session()->flash('message', 'Rol creado exitosamente.');
            }

            DB::commit();
            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar el rol: ' . $e->getMessage());
        }
    }

    public function editarRol($id)
    {
        $this->resetValidation();
        $this->editMode = true;
        $this->rolId = $id;

        $rol = Rol::with('permisos')->findOrFail($id);
        $this->nombre = $rol->nombre;
        $this->selectedPermisos = $rol->permisos->pluck('id')->toArray();

        // Recargar permisos
        $this->allPermisos = Permiso::orderBy('nombre')->get();
        
        $this->showModal = true;
    }

    public function eliminarRol($id)
    {
        try {
            $rol = Rol::findOrFail($id);

            // Validar si tiene usuarios asignados
            if ($rol->usuarios()->exists()) {
                session()->flash('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
                return;
            }

            $rol->permisos()->detach();
            $rol->delete();
            session()->flash('message', 'Rol eliminado exitosamente.');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el rol: ' . $e->getMessage());
        }
    }

    /**
     * Obtener permisos agrupados por módulo y filtrados
     */
    public function getPermisosAgrupadosProperty()
    {
        $permisos = $this->allPermisos;

        // Filtrar si hay búsqueda
        if (!empty($this->searchPermisos)) {
            $search = strtolower($this->searchPermisos);
            $permisos = $permisos->filter(function($permiso) use ($search) {
                return str_contains(strtolower($permiso->nombre), $search) ||
                       str_contains(strtolower($permiso->modulo), $search) ||
                       str_contains(strtolower($permiso->descripcion ?? ''), $search);
            });
        }

        // Agrupar por módulo
        return $permisos->groupBy('modulo')->sortKeys();
    }

    /**
     * Toggle para abrir/cerrar módulos en el accordion
     */
    public function toggleModulo($modulo)
    {
        if (in_array($modulo, $this->modulosAbiertos)) {
            $this->modulosAbiertos = array_diff($this->modulosAbiertos, [$modulo]);
        } else {
            $this->modulosAbiertos[] = $modulo;
        }
    }

    /**
     * Seleccionar todos los permisos de un módulo
     */
    public function seleccionarTodoModulo($modulo)
    {
        $permisosModulo = $this->allPermisos->where('modulo', $modulo)->pluck('id')->toArray();
        $this->selectedPermisos = array_unique(array_merge($this->selectedPermisos, $permisosModulo));
    }

    /**
     * Deseleccionar todos los permisos de un módulo
     */
    public function deseleccionarTodoModulo($modulo)
    {
        $permisosModulo = $this->allPermisos->where('modulo', $modulo)->pluck('id')->toArray();
        $this->selectedPermisos = array_diff($this->selectedPermisos, $permisosModulo);
    }

    /**
     * Verificar si todos los permisos de un módulo están seleccionados
     */
    public function todoModuloSeleccionado($modulo)
    {
        $permisosModulo = $this->allPermisos->where('modulo', $modulo)->pluck('id')->toArray();
        return count(array_intersect($this->selectedPermisos, $permisosModulo)) === count($permisosModulo);
    }

    public function render()
    {
        $roles = Rol::withCount('permisos')
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.gestion-roles', [
            'roles' => $roles
        ]);
    }
}
