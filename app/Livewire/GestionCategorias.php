<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Componente GestionCategorias
 *
 * CRUD simple para gestionar categorías de productos. Permite crear, editar,
 * buscar y activar/desactivar categorías.
 *
 * @package App\Livewire
 * @see resources/views/livewire/gestion-categorias.blade.php
 */
class GestionCategorias extends Component
{
    /** @var array Listado de categorías */
    public $categorias = [];

    /** @var string Término de búsqueda */
    public $searchCategoria = '';

    /** @var bool Controla visibilidad del modal */
    public $showModal = false;

    /** @var int|null ID de categoría en edición */
    public $editingId = null;

    /** @var string Nombre de la categoría */
    public $nombre = '';

    /**
     * Inicializa el componente con datos mock de prueba
     *
     * @todo Reemplazar con consultas a BD: Categoria::all()
     * @return void
     */
    public function mount()
    {
        $this->categorias = [
            ['id' => 1, 'nombre' => 'Herramientas', 'activo' => true],
            ['id' => 2, 'nombre' => 'Materiales Eléctricos', 'activo' => true],
            ['id' => 3, 'nombre' => 'Equipos de Seguridad', 'activo' => true],
            ['id' => 4, 'nombre' => 'Suministros de Oficina', 'activo' => true],
        ];
    }

    /**
     * Computed property: Retorna categorías filtradas por búsqueda
     *
     * @return array Categorías que coinciden con el término de búsqueda
     */
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

    /**
     * Abre el modal en modo creación
     *
     * @return void
     */
    public function abrirModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Abre el modal en modo edición con datos de la categoría seleccionada
     *
     * @param int $id ID de la categoría a editar
     * @return void
     */
    public function editarCategoria($id)
    {
        $categoria = collect($this->categorias)->firstWhere('id', $id);

        if ($categoria) {
            $this->editingId = $id;
            $this->nombre = $categoria['nombre'];
            $this->showModal = true;
        }
    }

    /**
     * Guarda una categoría (crear o actualizar según editingId)
     *
     * @return void
     */
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

    /**
     * Activa/desactiva una categoría (soft delete)
     *
     * @param int $id ID de la categoría
     * @return void
     */
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

    /**
     * Cierra el modal
     *
     * @return void
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Limpia el formulario y errores de validación
     *
     * @return void
     */
    private function resetForm()
    {
        $this->editingId = null;
        $this->nombre = '';
        $this->resetErrorBag();
    }

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.gestion-categorias');
    }
}
