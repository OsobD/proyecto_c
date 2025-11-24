<?php

namespace App\Livewire;

use App\Models\Puesto;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class GestionPuestos extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editMode = false;
    public $puestoId;
    public $nombre = '';
    public $search = '';

    // Modal de filtros
    public $showFilterModal = false;

    // Ordenamiento
    public $sortField = 'nombre';
    public $sortDirection = 'asc';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField !== $field) {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        } else {
            if ($this->sortDirection === 'asc') {
                $this->sortDirection = 'desc';
            } elseif ($this->sortDirection === 'desc') {
                $this->sortField = null;
                $this->sortDirection = null;
            }
        }
        $this->resetPage();
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
        $this->sortField = 'nombre';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                'unique:puesto,nombre,' . ($this->editMode ? $this->puestoId : 'NULL')
            ],
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre del puesto es obligatorio.',
        'nombre.unique' => 'Ya existe un puesto con este nombre.',
        'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
    ];

    public function render()
    {
        $query = Puesto::query();

        // Aplicar búsqueda
        if (!empty($this->search)) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        }

        // Aplicar ordenamiento
        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('nombre', 'asc');
        }

        $puestos = $query->paginate(10);

        return view('livewire.gestion-puestos', [
            'puestos' => $puestos
        ]);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->puestoId = null;
        $this->nombre = '';
        $this->editMode = false;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $puesto = Puesto::findOrFail($this->puestoId);
            $puesto->update([
                'nombre' => $this->nombre,
            ]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Actualizar',
                'modelo' => 'Puesto',
                'modelo_id' => $puesto->id,
                'descripcion' => "Puesto actualizado: {$this->nombre}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Puesto actualizado exitosamente.');
        } else {
            $puesto = Puesto::create([
                'nombre' => $this->nombre,
            ]);

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Crear',
                'modelo' => 'Puesto',
                'modelo_id' => $puesto->id,
                'descripcion' => "Puesto creado: {$this->nombre}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Puesto creado exitosamente.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $puesto = Puesto::findOrFail($id);
        $this->puestoId = $puesto->id;
        $this->nombre = $puesto->nombre;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function confirmDelete($id)
    {
        $this->puestoId = $id;
        $this->dispatch('confirm-delete');
    }

    public function delete()
    {
        try {
            $puesto = Puesto::findOrFail($this->puestoId);

            // Verificar si hay usuarios con este puesto
            if ($puesto->usuarios()->count() > 0) {
                session()->flash('error', 'No se puede eliminar el puesto porque hay usuarios asociados.');
                return;
            }

            $puesto->delete();

            // Registrar en bitácora
            Bitacora::create([
                'accion' => 'Eliminar',
                'modelo' => 'Puesto',
                'modelo_id' => $puesto->id,
                'descripcion' => "Puesto eliminado: {$puesto->nombre}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            session()->flash('message', 'Puesto eliminado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el puesto: ' . $e->getMessage());
        }
    }
}
