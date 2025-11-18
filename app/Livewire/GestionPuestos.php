<?php

namespace App\Livewire;

use App\Models\Puesto;
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

    protected $paginationTheme = 'tailwind';

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
        $puestos = Puesto::where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy('nombre', 'asc')
            ->paginate(15);

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
            session()->flash('message', 'Puesto actualizado exitosamente.');
        } else {
            Puesto::create([
                'nombre' => $this->nombre,
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
            session()->flash('message', 'Puesto eliminado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el puesto: ' . $e->getMessage());
        }
    }
}
