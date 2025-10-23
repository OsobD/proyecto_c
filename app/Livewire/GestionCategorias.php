<?php

namespace App\Livewire;

use Livewire\Component;

class GestionCategorias extends Component
{
    public $categorias = [];
    public $searchCategoria = '';
    public $showModal = false;
    public $editingId = null;

    // Campos del formulario
    public $nombre = '';

    public function mount()
    {
        $this->categorias = [
            ['id' => 1, 'nombre' => 'Herramientas', 'activo' => true],
            ['id' => 2, 'nombre' => 'Materiales Eléctricos', 'activo' => true],
            ['id' => 3, 'nombre' => 'Equipos de Seguridad', 'activo' => true],
            ['id' => 4, 'nombre' => 'Suministros de Oficina', 'activo' => true],
        ];
    }

    public function getCategoriasFiltradasProperty()
    {
        if (empty($this->searchCategoria)) {
            return $this->categorias;
        }

        $search = strtolower(trim($this->searchCategoria));

        return array_filter($this->categorias, function($categoria) use ($search) {
            return str_contains(strtolower($categoria['nombre']), $search);
        });
    }

    public function abrirModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editarCategoria($id)
    {
        $categoria = collect($this->categorias)->firstWhere('id', $id);

        if ($categoria) {
            $this->editingId = $id;
            $this->nombre = $categoria['nombre'];
            $this->showModal = true;
        }
    }

    public function guardarCategoria()
    {
        $this->validate([
            'nombre' => 'required|min:3|max:100',
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
        ]);

        if ($this->editingId) {
            // Actualizar categoría existente
            $this->categorias = array_map(function($cat) {
                if ($cat['id'] === $this->editingId) {
                    $cat['nombre'] = $this->nombre;
                }
                return $cat;
            }, $this->categorias);
        } else {
            // Crear nueva categoría
            $newId = max(array_column($this->categorias, 'id')) + 1;
            $this->categorias[] = [
                'id' => $newId,
                'nombre' => $this->nombre,
                'activo' => true,
            ];
        }

        $this->closeModal();
        session()->flash('message', $this->editingId ? 'Categoría actualizada exitosamente.' : 'Categoría creada exitosamente.');
    }

    public function toggleEstado($id)
    {
        $this->categorias = array_map(function($cat) use ($id) {
            if ($cat['id'] === $id) {
                $cat['activo'] = !$cat['activo'];
            }
            return $cat;
        }, $this->categorias);

        session()->flash('message', 'Estado de la categoría actualizado.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->nombre = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.gestion-categorias');
    }
}
