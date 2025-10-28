<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Componente GestionProductos
 *
 * Gestiona el CRUD completo de productos del sistema de inventario.
 * Permite crear, editar, buscar, activar/desactivar productos y visualizar
 * su historial de compras.
 *
 * **Funcionalidades principales:**
 * - Listado de productos con búsqueda en tiempo real
 * - Creación y edición de productos mediante modal
 * - Asociación de productos con categorías
 * - Creación rápida de categorías desde el mismo formulario (sub-modal)
 * - Activación/desactivación de productos (soft delete)
 * - Visualización de historial de compras por producto
 *
 * **Estado de desarrollo:**
 * Actualmente utiliza datos mock. Pendiente integración con modelos Eloquent
 * (Producto, Categoria) y base de datos real.
 *
 * @package App\Livewire
 * @version 1.0
 * @see resources/views/livewire/gestion-productos.blade.php Vista asociada
 */
class GestionProductos extends Component
{
    // Propiedades de datos
    /** @var array Lista de productos del sistema */
    public $productos = [];

    /** @var array Lista de categorías disponibles */
    public $categorias = [];

    // Propiedades de búsqueda y filtrado
    /** @var string Término de búsqueda para filtrar productos */
    public $searchProducto = '';

    // Propiedades de control de UI
    /** @var bool Controla visibilidad del modal de producto */
    public $showModal = false;

    /** @var bool Controla visibilidad del sub-modal de categoría */
    public $showSubModalCategoria = false;

    /** @var int|null ID del producto cuyo historial está expandido */
    public $showHistorial = null;

    /** @var int|null ID del producto en edición (null = modo creación) */
    public $editingId = null;

    // Campos del formulario de producto
    /** @var string Código único del producto */
    public $codigo = '';

    /** @var string Descripción del producto */
    public $descripcion = '';

    /** @var string|int ID de la categoría seleccionada */
    public $categoriaId = '';

    // Campo para crear categoría
    /** @var string Nombre de nueva categoría a crear */
    public $nuevaCategoriaNombre = '';

    /**
     * Inicializa el componente con datos mock de prueba
     *
     * @todo Reemplazar con consultas a BD: Producto::all() y Categoria::all()
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
            [
                'id' => 1,
                'codigo' => 'PROD-001',
                'descripcion' => 'Tornillos de acero inoxidable',
                'categoria_id' => 1,
                'activo' => true,
                'historial' => [
                    ['fecha' => '2024-01-15', 'proveedor' => 'Ferretería El Martillo', 'costo' => 15.50, 'factura' => 'F-001'],
                    ['fecha' => '2024-02-20', 'proveedor' => 'Ferretería El Martillo', 'costo' => 16.00, 'factura' => 'F-045'],
                ],
            ],
            [
                'id' => 2,
                'codigo' => 'PROD-002',
                'descripcion' => 'Abrazaderas de metal',
                'categoria_id' => 2,
                'activo' => true,
                'historial' => [
                    ['fecha' => '2024-01-10', 'proveedor' => 'Suministros Industriales', 'costo' => 5.75, 'factura' => 'F-120'],
                ],
            ],
            [
                'id' => 3,
                'codigo' => 'PROD-003',
                'descripcion' => 'Cinta aislante',
                'categoria_id' => 2,
                'activo' => true,
                'historial' => [],
            ],
            [
                'id' => 4,
                'codigo' => 'PROD-004',
                'descripcion' => 'Guantes de seguridad',
                'categoria_id' => 3,
                'activo' => true,
                'historial' => [],
            ],
            [
                'id' => 5,
                'codigo' => 'PROD-005',
                'descripcion' => 'Fusibles de 15A',
                'categoria_id' => 2,
                'activo' => false,
                'historial' => [],
            ],
        ];
    }

    /**
     * Computed property: Retorna productos filtrados por búsqueda
     *
     * Filtra por código, descripción o nombre de categoría.
     *
     * @return array Lista de productos que coinciden con el término de búsqueda
     */
    public function getProductosFiltradosProperty()
    {
        if (empty($this->searchProducto)) {
            return $this->productos;
        }

        $search = strtolower(trim($this->searchProducto));

        return array_filter($this->productos, function($producto) use ($search) {
            $categoriaNombre = $this->getNombreCategoria($producto['categoria_id']);
            return str_contains(strtolower($producto['codigo']), $search) ||
                   str_contains(strtolower($producto['descripcion']), $search) ||
                   str_contains(strtolower($categoriaNombre), $search);
        });
    }

    /**
     * Computed property: Retorna solo categorías activas
     *
     * @return array Categorías con estado activo = true
     */
    public function getCategoriasActivasProperty()
    {
        return array_filter($this->categorias, fn($cat) => $cat['activo']);
    }

    /**
     * Obtiene el nombre de una categoría por su ID
     *
     * @param int $categoriaId ID de la categoría a buscar
     * @return string Nombre de la categoría o 'Sin categoría' si no existe
     */
    public function getNombreCategoria($categoriaId)
    {
        $categoria = collect($this->categorias)->firstWhere('id', $categoriaId);
        return $categoria ? $categoria['nombre'] : 'Sin categoría';
    }

    /**
     * Abre el modal de producto en modo creación
     *
     * @return void
     */
    public function abrirModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Abre el modal de producto en modo edición
     *
     * Carga los datos del producto seleccionado en el formulario.
     *
     * @param int $id ID del producto a editar
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
     * Guarda un producto (crear o actualizar según editingId)
     *
     * Valida los campos del formulario y persiste los cambios.
     * Muestra mensaje de éxito mediante flash session.
     *
     * @return void
     */
    public function guardarProducto()
    {
        $this->validate([
            'codigo' => 'required|min:3|max:50',
            'descripcion' => 'required|min:3|max:255',
            'categoriaId' => 'required',
        ], [
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.min' => 'El código debe tener al menos 3 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 3 caracteres.',
            'categoriaId.required' => 'Debe seleccionar una categoría.',
        ]);

        if ($this->editingId) {
            // Actualizar producto existente
            $this->productos = array_map(function($prod) {
                if ($prod['id'] === $this->editingId) {
                    $prod['codigo'] = $this->codigo;
                    $prod['descripcion'] = $this->descripcion;
                    $prod['categoria_id'] = (int)$this->categoriaId;
                }
                return $prod;
            }, $this->productos);
        } else {
            // Crear nuevo producto
            $newId = max(array_column($this->productos, 'id')) + 1;
            $this->productos[] = [
                'id' => $newId,
                'codigo' => $this->codigo,
                'descripcion' => $this->descripcion,
                'categoria_id' => (int)$this->categoriaId,
                'activo' => true,
                'historial' => [],
            ];
        }

        $this->closeModal();
        session()->flash('message', $this->editingId ? 'Producto actualizado exitosamente.' : 'Producto creado exitosamente.');
    }

    /**
     * Cambia el estado activo/inactivo de un producto (soft delete)
     *
     * @param int $id ID del producto a activar/desactivar
     * @return void
     */
    public function toggleEstado($id)
    {
        $this->productos = array_map(function($prod) use ($id) {
            if ($prod['id'] === $id) {
                $prod['activo'] = !$prod['activo'];
            }
            return $prod;
        }, $this->productos);

        session()->flash('message', 'Estado del producto actualizado.');
    }

    /**
     * Expande/colapsa el historial de compras de un producto
     *
     * @param int $id ID del producto cuyo historial se desea ver
     * @return void
     */
    public function toggleHistorial($id)
    {
        $this->showHistorial = $this->showHistorial === $id ? null : $id;
    }

    /**
     * Abre el sub-modal para crear nueva categoría
     *
     * @return void
     */
    public function abrirSubModalCategoria()
    {
        $this->nuevaCategoriaNombre = '';
        $this->showSubModalCategoria = true;
    }

    /**
     * Guarda una nueva categoría desde el sub-modal
     *
     * Al crear exitosamente, selecciona automáticamente la nueva categoría
     * en el formulario principal de producto.
     *
     * @return void
     */
    public function guardarNuevaCategoria()
    {
        $this->validate([
            'nuevaCategoriaNombre' => 'required|min:3|max:100',
        ], [
            'nuevaCategoriaNombre.required' => 'El nombre de la categoría es obligatorio.',
            'nuevaCategoriaNombre.min' => 'El nombre debe tener al menos 3 caracteres.',
        ]);

        $newId = max(array_column($this->categorias, 'id')) + 1;
        $this->categorias[] = [
            'id' => $newId,
            'nombre' => $this->nuevaCategoriaNombre,
            'activo' => true,
        ];

        $this->categoriaId = $newId;
        $this->showSubModalCategoria = false;
        $this->nuevaCategoriaNombre = '';
    }

    /**
     * Cierra el sub-modal de categoría
     *
     * @return void
     */
    public function closeSubModalCategoria()
    {
        $this->showSubModalCategoria = false;
        $this->nuevaCategoriaNombre = '';
    }

    /**
     * Cierra el modal principal de producto
     *
     * @return void
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Limpia los campos del formulario y errores de validación
     *
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
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.gestion-productos');
    }
}
