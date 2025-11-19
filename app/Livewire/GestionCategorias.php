<?php

namespace App\Livewire;

use App\Models\Categoria;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Componente GestionCategorias
 *
 * CRUD completo para gestionar categorías de productos. Permite crear, editar,
 * buscar y activar/desactivar categorías con conexión a base de datos real.
 *
 * @package App\Livewire
 * @see resources/views/livewire/gestion-categorias.blade.php
 */
class GestionCategorias extends Component
{
    use WithPagination;

    /** @var string Término de búsqueda */
    public $searchCategoria = '';

    /** @var bool Controla visibilidad del modal */
    public $showModal = false;

    /** @var int|null ID de categoría en edición */
    public $editingId = null;

    /** @var string Nombre de la categoría */
    public $nombre = '';

    /**
     * Resetea la paginación cuando cambia la búsqueda
     *
     * @return void
     */
    public function updatingSearchCategoria()
    {
        $this->resetPage();
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
        $categoria = Categoria::find($id);

        if ($categoria) {
            $this->editingId = $id;
            $this->nombre = $categoria->nombre;
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
            $categoria = Categoria::find($this->editingId);
            if ($categoria) {
                $categoria->update([
                    'nombre' => $this->nombre,
                ]);
                $mensaje = 'Categoría actualizada exitosamente.';
            }
        } else {
            // Crear nueva categoría
            Categoria::create([
                'nombre' => $this->nombre,
                'activo' => true,
            ]);
            $mensaje = 'Categoría creada exitosamente.';
        }

        $this->closeModal();
        session()->flash('message', $mensaje ?? 'Operación completada.');
    }

    /**
     * Activa/desactiva una categoría (soft delete)
     *
     * @param int $id ID de la categoría
     * @return void
     */
    public function toggleEstado($id)
    {
        $categoria = Categoria::find($id);

        if ($categoria) {
            $categoria->update([
                'activo' => !$categoria->activo,
            ]);
            session()->flash('message', 'Estado de la categoría actualizado.');
        }
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
        $categorias = Categoria::query()
            ->when($this->searchCategoria, function($query) {
                $search = trim($this->searchCategoria);
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(30);

        return view('livewire.gestion-categorias', [
            'categorias' => $categorias
        ]);
    }
}
