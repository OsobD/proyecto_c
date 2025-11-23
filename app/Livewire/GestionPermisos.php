<?php

namespace App\Livewire;

use App\Models\Permiso;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente GestionPermisos
 *
 * Gestiona el CRUD de permisos.
 */
class GestionPermisos extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'nombre';
    public $sortDirection = 'asc';

    public $showModal = false;
    public $editMode = false;
    public $permisoId = null;

    public $nombre = '';

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'nombre' => 'required|string|max:255|unique:permiso,nombre',
    ];

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
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->nombre = '';
        $this->permisoId = null;
    }

    public function guardarPermiso()
    {
        if ($this->editMode) {
            $this->rules['nombre'] = 'required|string|max:255|unique:permiso,nombre,' . $this->permisoId;
        }

        $this->validate();

        try {
            if ($this->editMode) {
                $permiso = Permiso::findOrFail($this->permisoId);
                $permiso->update(['nombre' => $this->nombre]);
                session()->flash('message', 'Permiso actualizado exitosamente.');
            } else {
                Permiso::create(['nombre' => $this->nombre]);
                session()->flash('message', 'Permiso creado exitosamente.');
            }

            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar el permiso: ' . $e->getMessage());
        }
    }

    public function editarPermiso($id)
    {
        $this->resetValidation();
        $this->editMode = true;
        $this->permisoId = $id;

        $permiso = Permiso::findOrFail($id);
        $this->nombre = $permiso->nombre;
        
        $this->showModal = true;
    }

    public function eliminarPermiso($id)
    {
        try {
            $permiso = Permiso::findOrFail($id);
            
            // Validar si está asignado a algún rol
            if ($permiso->roles()->exists()) {
                session()->flash('error', 'No se puede eliminar el permiso porque está asignado a uno o más roles.');
                return;
            }

            $permiso->delete();
            session()->flash('message', 'Permiso eliminado exitosamente.');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el permiso: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $permisos = Permiso::where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.gestion-permisos', [
            'permisos' => $permisos
        ]);
    }
}
