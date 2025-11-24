<?php

namespace App\Livewire;

use App\Models\RegimenTributario;
use Livewire\Component;
use Livewire\WithPagination;

class GestionRegimenes extends Component
{
    use WithPagination;

    public $search = '';
    public $nombre = '';
    public $editingId = null;
    public $showModal = false;
    public $editMode = false;

    // Delete confirmation
    public $showDeleteModal = false;
    public $regimenToDeleteId = null;
    public $regimenToDeleteName = '';

    protected $rules = [
        'nombre' => 'required|min:3|max:255|unique:regimen_tributario,nombre',
    ];

    public function render()
    {
        $regimenes = RegimenTributario::where('nombre', 'like', '%' . $this->search . '%')
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.gestion-regimenes', [
            'regimenes' => $regimenes,
        ]);
    }

    public function openModal()
    {
        $this->reset(['nombre', 'editingId']);
        $this->editMode = false;
        $this->showModal = true;
    }

    public function create()
    {
        $this->openModal();
    }

    public function edit($id)
    {
        $regimen = RegimenTributario::findOrFail($id);
        $this->editingId = $id;
        $this->nombre = $regimen->nombre;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->editMode) {
            $this->update();
        } else {
            $this->store();
        }
    }

    public function store()
    {
        $this->validate();

        RegimenTributario::create([
            'nombre' => $this->nombre,
        ]);

        $this->closeModal();
        session()->flash('message', 'Régimen creado exitosamente.');
    }

    public function update()
    {
        $this->validate([
            'nombre' => 'required|min:3|max:255|unique:regimen_tributario,nombre,' . $this->editingId,
        ]);

        $regimen = RegimenTributario::findOrFail($this->editingId);
        $regimen->update([
            'nombre' => $this->nombre,
        ]);

        $this->closeModal();
        session()->flash('message', 'Régimen actualizado exitosamente.');
    }

    public function confirmDelete($id)
    {
        $regimen = RegimenTributario::findOrFail($id);

        // Check if it has related providers
        if ($regimen->proveedores()->exists()) {
            session()->flash('error', 'No se puede eliminar el régimen porque tiene proveedores asociados.');
            return;
        }

        $this->regimenToDeleteId = $id;
        $this->regimenToDeleteName = $regimen->nombre;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->regimenToDeleteId) {
            $regimen = RegimenTributario::findOrFail($this->regimenToDeleteId);
            $regimen->delete();
            session()->flash('message', 'Régimen eliminado exitosamente.');
        }

        $this->closeDeleteModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editMode = false;
        $this->reset(['nombre', 'editingId']);
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->regimenToDeleteId = null;
        $this->regimenToDeleteName = '';
    }
}
