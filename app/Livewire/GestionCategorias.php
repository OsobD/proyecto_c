<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * @class GestionCategorias
 * @package App\Livewire
 * @brief Componente para la gestión de categorías de productos.
 *
 * Este componente permite a los usuarios buscar, crear, editar y cambiar el
 * estado (activo/inactivo) de las categorías de productos. Las operaciones
 * de creación y edición se realizan a través de un modal.
 */
class GestionCategorias extends Component
{
    // --- PROPIEDADES PÚBLICAS ---

    /** @var array Lista de todas las categorías. */
    public $categorias = [];
    /** @var string Término de búsqueda para filtrar categorías. */
    public $searchCategoria = '';
    /** @var bool Controla la visibilidad del modal de creación/edición. */
    public $showModal = false;
    /** @var int|null ID de la categoría que se está editando. Null si se crea una nueva. */
    public $editingId = null;

    // --- CAMPOS DEL FORMULARIO DEL MODAL ---

    /** @var string Nombre de la categoría en el formulario. */
    public $nombre = '';

    // --- MÉTODOS DE CICLO DE VIDA ---

    /**
     * @brief Método que se ejecuta al inicializar el componente.
     * Carga datos de ejemplo para la lista de categorías.
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

    // --- PROPIEDADES COMPUTADAS ---

    /**
     * @brief Filtra las categorías según el término de búsqueda.
     * @return array
     */
    public function getCategoriasFiltradasProperty()
    {
        if (empty($this->searchCategoria)) {
            return $this->categorias;
        }
        $search = strtolower(trim($this->searchCategoria));
        return array_filter($this->categorias, fn($c) => str_contains(strtolower($c['nombre']), $search));
    }

    // --- MÉTODOS DE MANEJO DEL MODAL ---

    /**
     * @brief Abre el modal en modo de creación.
     * @return void
     */
    public function abrirModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * @brief Abre el modal en modo de edición con los datos de una categoría existente.
     * @param int $id ID de la categoría a editar.
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
     * @brief Cierra el modal y reinicia el formulario.
     * @return void
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    // --- MÉTODOS DE LÓGICA DE NEGOCIO ---

    /**
     * @brief Guarda una categoría nueva o actualiza una existente.
     * Simula la lógica de validación y persistencia de datos.
     * @return void
     */
    public function guardarCategoria()
    {
        $this->validate(['nombre' => 'required|min:3|max:100']);

        if ($this->editingId) {
            // Actualizar categoría
            $this->categorias = array_map(function($cat) {
                if ($cat['id'] === $this->editingId) {
                    $cat['nombre'] = $this->nombre;
                }
                return $cat;
            }, $this->categorias);
        } else {
            // Crear nueva categoría
            $newId = count($this->categorias) > 0 ? max(array_column($this->categorias, 'id')) + 1 : 1;
            $this->categorias[] = ['id' => $newId, 'nombre' => $this->nombre, 'activo' => true];
        }

        $this->closeModal();
        session()->flash('message', $this->editingId ? 'Categoría actualizada.' : 'Categoría creada.');
    }

    /**
     * @brief Cambia el estado (activo/inactivo) de una categoría.
     * @param int $id ID de la categoría a modificar.
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
     * @brief Reinicia las propiedades del formulario del modal.
     * @return void
     */
    private function resetForm()
    {
        $this->editingId = null;
        $this->nombre = '';
        $this->resetErrorBag();
    }

    /**
     * @brief Renderiza la vista del componente.
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.gestion-categorias');
    }
}
