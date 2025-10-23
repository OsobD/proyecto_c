<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * @class GestionProductos
 * @package App\Livewire
 * @brief Componente para la gestión integral de productos.
 *
 * Este componente permite buscar, crear, editar y cambiar el estado de los
 * productos. Incluye una funcionalidad para visualizar el historial de costos
 * de un producto y permite la creación de nuevas categorías a través de un
 * sub-modal sin salir del formulario principal del producto.
 */
class GestionProductos extends Component
{
    // --- PROPIEDADES PÚBLICAS ---

    /** @var array Lista de todos los productos. */
    public $productos = [];
    /** @var array Lista de todas las categorías disponibles. */
    public $categorias = [];
    /** @var string Término de búsqueda para filtrar productos. */
    public $searchProducto = '';
    /** @var bool Controla la visibilidad del modal principal (crear/editar producto). */
    public $showModal = false;
    /** @var bool Controla la visibilidad del sub-modal para crear categorías. */
    public $showSubModalCategoria = false;
    /** @var int|null ID del producto cuyo historial de costos se está mostrando. */
    public $showHistorial = null;
    /** @var int|null ID del producto que se está editando. Null si se crea uno nuevo. */
    public $editingId = null;

    // --- CAMPOS DEL FORMULARIO DE PRODUCTO ---

    /** @var string Código del producto. */
    public $codigo = '';
    /** @var string Descripción del producto. */
    public $descripcion = '';
    /** @var int|string ID de la categoría seleccionada. */
    public $categoriaId = '';

    // --- CAMPO PARA CREAR CATEGORÍA ---

    /** @var string Nombre de la nueva categoría a crear en el sub-modal. */
    public $nuevaCategoriaNombre = '';

    // --- MÉTODOS DE CICLO DE VIDA ---

    /**
     * @brief Método que se ejecuta al inicializar el componente.
     * Carga datos de ejemplo para productos y categorías.
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

        $this->productos = [
            ['id' => 1, 'codigo' => 'PROD-001', 'descripcion' => 'Tornillos', 'categoria_id' => 1, 'activo' => true, 'historial' => [['fecha' => '2024-01-15', 'proveedor' => 'Ferretería A', 'costo' => 15.50, 'factura' => 'F-001']]],
            ['id' => 2, 'codigo' => 'PROD-002', 'descripcion' => 'Abrazaderas', 'categoria_id' => 2, 'activo' => true, 'historial' => []],
            ['id' => 3, 'codigo' => 'PROD-003', 'descripcion' => 'Cinta aislante', 'categoria_id' => 2, 'activo' => false, 'historial' => []],
        ];
    }

    // --- PROPIEDADES COMPUTADAS ---

    /**
     * @brief Filtra los productos según el término de búsqueda.
     * La búsqueda se aplica sobre el código, descripción y nombre de la categoría.
     * @return array
     */
    public function getProductosFiltradosProperty()
    {
        if (empty($this->searchProducto)) return $this->productos;
        $search = strtolower(trim($this->searchProducto));
        return array_filter($this->productos, function($producto) use ($search) {
            return str_contains(strtolower($producto['codigo']), $search) ||
                   str_contains(strtolower($producto['descripcion']), $search) ||
                   str_contains(strtolower($this->getNombreCategoria($producto['categoria_id'])), $search);
        });
    }

    /**
     * @brief Obtiene una lista de las categorías que están activas.
     * @return array
     */
    public function getCategoriasActivasProperty()
    {
        return array_filter($this->categorias, fn($cat) => $cat['activo']);
    }

    // --- MÉTODOS AUXILIARES ---

    /**
     * @brief Obtiene el nombre de una categoría a partir de su ID.
     * @param int $categoriaId ID de la categoría.
     * @return string Nombre de la categoría o 'Sin categoría' si no se encuentra.
     */
    public function getNombreCategoria($categoriaId)
    {
        $categoria = collect($this->categorias)->firstWhere('id', $categoriaId);
        return $categoria ? $categoria['nombre'] : 'Sin categoría';
    }

    // --- MÉTODOS DE MANEJO DEL MODAL PRINCIPAL ---

    /**
     * @brief Abre el modal en modo de creación de producto.
     * @return void
     */
    public function abrirModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * @brief Abre el modal en modo de edición con los datos de un producto.
     * @param int $id ID del producto a editar.
     * @return void
     */
    public function editarProducto($id)
    {
        $producto = collect($this->productos)->firstWhere('id', $id);
        if ($producto) {
            $this->editingId = $id;
            $this->codigo = $producto['codigo'];
            $this->descripcion = $producto['descripcion'];
            $this->categoriaId = $producto['categoria_id'];
            $this->showModal = true;
        }
    }

    /**
     * @brief Cierra el modal principal y reinicia el formulario.
     * @return void
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    // --- MÉTODOS DE LÓGICA DE NEGOCIO ---

    /**
     * @brief Guarda un producto nuevo o actualiza uno existente.
     * @return void
     */
    public function guardarProducto()
    {
        $this->validate([
            'codigo' => 'required|min:3|max:50',
            'descripcion' => 'required|min:3|max:255',
            'categoriaId' => 'required',
        ]);

        if ($this->editingId) {
            $this->productos = array_map(function($p) {
                if ($p['id'] === $this->editingId) {
                    $p['codigo'] = $this->codigo;
                    $p['descripcion'] = $this->descripcion;
                    $p['categoria_id'] = (int)$this->categoriaId;
                }
                return $p;
            }, $this->productos);
        } else {
            $newId = count($this->productos) > 0 ? max(array_column($this->productos, 'id')) + 1 : 1;
            $this->productos[] = [
                'id' => $newId, 'codigo' => $this->codigo,
                'descripcion' => $this->descripcion, 'categoria_id' => (int)$this->categoriaId,
                'activo' => true, 'historial' => [],
            ];
        }
        $this->closeModal();
        session()->flash('message', $this->editingId ? 'Producto actualizado.' : 'Producto creado.');
    }

    /**
     * @brief Cambia el estado (activo/inactivo) de un producto.
     * @param int $id ID del producto a modificar.
     * @return void
     */
    public function toggleEstado($id)
    {
        $this->productos = array_map(function($p) use ($id) {
            if ($p['id'] === $id) $p['activo'] = !$p['activo'];
            return $p;
        }, $this->productos);
        session()->flash('message', 'Estado del producto actualizado.');
    }

    /**
     * @brief Muestra u oculta el historial de costos de un producto.
     * @param int $id ID del producto.
     * @return void
     */
    public function toggleHistorial($id)
    {
        $this->showHistorial = $this->showHistorial === $id ? null : $id;
    }

    // --- MÉTODOS DEL SUB-MODAL DE CATEGORÍA ---

    /**
     * @brief Abre el sub-modal para crear una nueva categoría.
     * @return void
     */
    public function abrirSubModalCategoria()
    {
        $this->nuevaCategoriaNombre = '';
        $this->showSubModalCategoria = true;
    }

    /**
     * @brief Guarda la nueva categoría y la selecciona en el formulario de producto.
     * @return void
     */
    public function guardarNuevaCategoria()
    {
        $this->validate(['nuevaCategoriaNombre' => 'required|min:3|max:100']);

        $newId = count($this->categorias) > 0 ? max(array_column($this->categorias, 'id')) + 1 : 1;
        $this->categorias[] = ['id' => $newId, 'nombre' => $this->nuevaCategoriaNombre, 'activo' => true];

        $this->categoriaId = $newId;
        $this->closeSubModalCategoria();
    }

    /**
     * @brief Cierra el sub-modal de creación de categoría.
     * @return void
     */
    public function closeSubModalCategoria()
    {
        $this->showSubModalCategoria = false;
        $this->nuevaCategoriaNombre = '';
    }

    /**
     * @brief Reinicia las propiedades del formulario del modal principal.
     * @return void
     */
    private function resetForm()
    {
        $this->editingId = null;
        $this->codigo = '';
        $this->descripcion = '';
        $this->categoriaId = '';
        $this->resetErrorBag();
    }

    /**
     * @brief Renderiza la vista del componente.
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.gestion-productos');
    }
}
