<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * @class FormularioCompra
 * @package App\Livewire
 * @brief Componente para la gestión del formulario de registro de compras.
 *
 * Este componente maneja la lógica para crear una nueva compra, incluyendo la
 * búsqueda y selección de proveedores y productos. También permite la creación
 * de nuevos productos, categorías y proveedores a través de modales sin
 * abandonar el formulario principal.
 */
class FormularioCompra extends Component
{
    // --- PROPIEDADES PÚBLICAS ---

    /** @var array Datos de ejemplo para proveedores, productos, etc. */
    public $proveedores = [];
    public $productos = [];
    public $categorias = [];
    public $regimenes = [];

    // Búsqueda de proveedor
    /** @var string Término de búsqueda para proveedores. */
    public $searchProveedor = '';
    /** @var bool Controla la visibilidad del dropdown de resultados de proveedores. */
    public $showProveedorDropdown = false;
    /** @var array|null Proveedor seleccionado actualmente. */
    public $selectedProveedor = null;

    // Búsqueda de producto
    /** @var string Término de búsqueda para productos. */
    public $searchProducto = '';
    /** @var bool Controla la visibilidad del dropdown de resultados de productos. */
    public $showProductoDropdown = false;
    /** @var array Lista de productos agregados a la compra. */
    public $productosSeleccionados = [];

    // Búsqueda de categoría (para modal)
    /** @var string Término de búsqueda para categorías en el modal de producto. */
    public $searchCategoria = '';
    /** @var bool Controla la visibilidad del dropdown de resultados de categorías. */
    public $showCategoriaDropdown = false;
    /** @var array|null Categoría seleccionada en el modal. */
    public $selectedCategoria = null;

    /** @var string Número de factura de la compra. */
    public $numeroFactura = '';
    /** @var string Número de serie de la factura. */
    public $numeroSerie = '';

    // --- PROPIEDADES PARA MODALES ---

    // Modal para crear producto
    /** @var bool Controla la visibilidad del modal para crear un nuevo producto. */
    public $showModalProducto = false;
    /** @var bool Controla la visibilidad del sub-modal para crear una nueva categoría. */
    public $showSubModalCategoria = false;
    /** @var string Código del nuevo producto. */
    public $codigo = '';
    /** @var string Descripción del nuevo producto. */
    public $descripcion = '';
    /** @var int|string ID de la categoría seleccionada para el nuevo producto. */
    public $categoriaId = '';
    /** @var string Nombre de la nueva categoría a crear. */
    public $nuevaCategoriaNombre = '';

    // Modal para crear proveedor
    /** @var bool Controla la visibilidad del modal para crear un nuevo proveedor. */
    public $showModalProveedor = false;
    /** @var string NIT del nuevo proveedor. */
    public $nuevoProveedorNit = '';
    /** @var string Régimen fiscal del nuevo proveedor. */
    public $nuevoProveedorRegimen = '';
    /** @var string Nombre del nuevo proveedor. */
    public $nuevoProveedorNombre = '';

    // Dropdown de régimen fiscal (en modal de proveedor)
    /** @var bool Controla la visibilidad del dropdown de regímenes fiscales. */
    public $showRegimenDropdown = false;
    /** @var string|null Régimen fiscal seleccionado. */
    public $selectedRegimen = null;

    // --- MÉTODOS DE CICLO DE VIDA ---

    /**
     * @brief Método que se ejecuta al inicializar el componente.
     * Carga datos de ejemplo para la simulación del formulario.
     * @return void
     */
    public function mount()
    {
        $this->regimenes = [
            'Pequeño Contribuyente',
            'Régimen General',
            'Régimen Especial',
            'Exento',
        ];

        $this->proveedores = [
            ['id' => 1, 'nit' => '12345678-9', 'regimen' => 'Régimen General', 'nombre' => 'Ferretería El Martillo Feliz', 'activo' => true],
            ['id' => 2, 'nit' => '98765432-1', 'regimen' => 'Pequeño Contribuyente', 'nombre' => 'Suministros Industriales S.A.', 'activo' => true],
        ];

        $this->categorias = [
            ['id' => 1, 'nombre' => 'Herramientas', 'activo' => true],
            ['id' => 2, 'nombre' => 'Materiales Eléctricos', 'activo' => true],
            ['id' => 3, 'nombre' => 'Equipos de Seguridad', 'activo' => true],
            ['id' => 4, 'nombre' => 'Suministros de Oficina', 'activo' => true],
        ];

        $this->productos = [
            ['id' => 1, 'codigo' => 'PROD-001', 'descripcion' => 'Tornillos de acero inoxidable'],
            ['id' => 2, 'codigo' => 'PROD-002', 'descripcion' => 'Abrazaderas de metal'],
            ['id' => 3, 'codigo' => 'PROD-003', 'descripcion' => 'Cinta aislante'],
            ['id' => 4, 'codigo' => 'PROD-004', 'descripcion' => 'Guantes de seguridad'],
            ['id' => 5, 'codigo' => 'PROD-005', 'descripcion' => 'Fusibles de 15A'],
        ];

        $this->productosSeleccionados = [];
    }

    /**
     * @brief Hook que se ejecuta cuando una propiedad pública es actualizada.
     * Se utiliza para mostrar los dropdowns de búsqueda y para asegurar que
     * los valores de cantidad y costo de los productos sean numéricos.
     * @param string $propertyName Nombre de la propiedad actualizada.
     * @return void
     */
    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'search')) {
            $dropdown = 'show' . ucfirst(str_replace('search', '', $propertyName)) . 'Dropdown';
            if (property_exists($this, $dropdown)) {
                $this->$dropdown = true;
            }
        }

        if (str_starts_with($propertyName, 'productosSeleccionados.')) {
            $parts = explode('.', $propertyName);
            if (count($parts) === 3) {
                $index = $parts[1];
                $field = $parts[2];

                if ($field === 'cantidad') {
                    $valor = $this->productosSeleccionados[$index]['cantidad'];
                    $this->productosSeleccionados[$index]['cantidad'] = empty($valor) ? 1 : max(1, (int)$valor);
                } elseif ($field === 'costo') {
                    $valor = $this->productosSeleccionados[$index]['costo'];
                    $this->productosSeleccionados[$index]['costo'] = ($valor === '' || $valor === null) ? 0 : max(0, (float)$valor);
                }
            }
        }
    }


    // --- PROPIEDADES COMPUTADAS ---

    /**
     * @brief Filtra los proveedores basados en el término de búsqueda.
     * @return array
     */
    public function getProveedorResultsProperty()
    {
        if (empty($this->searchProveedor)) {
            return $this->proveedores;
        }
        $search = strtolower(trim($this->searchProveedor));
        return array_filter($this->proveedores, fn($p) => str_contains(strtolower($p['nombre']), $search));
    }

    /**
     * @brief Filtra los productos basados en el término de búsqueda.
     * @return array
     */
    public function getProductoResultsProperty()
    {
        if (empty($this->searchProducto)) {
            return $this->productos;
        }
        $search = strtolower(trim($this->searchProducto));
        return array_filter($this->productos, fn($p) => str_contains(strtolower($p['descripcion']), $search) || str_contains(strtolower($p['codigo']), $search));
    }

    /**
     * @brief Filtra las categorías activas basadas en el término de búsqueda.
     * @return array
     */
    public function getCategoriaResultsProperty()
    {
        $categorias = $this->categoriasActivas;
        if (empty($this->searchCategoria)) {
            return $categorias;
        }
        $search = strtolower(trim($this->searchCategoria));
        return array_filter($categorias, fn($c) => str_contains(strtolower($c['nombre']), $search));
    }

    /**
     * @brief Obtiene solo las categorías que están marcadas como activas.
     * @return array
     */
    public function getCategoriasActivasProperty()
    {
        return array_filter($this->categorias, fn($cat) => $cat['activo']);
    }

    /**
     * @brief Calcula el subtotal de la compra antes de impuestos.
     * @return float
     */
    public function getSubtotalProperty()
    {
        return collect($this->productosSeleccionados)->sum(function($producto) {
            $cantidad = (float)($producto['cantidad'] ?? 0);
            $costoConIva = (float)($producto['costo'] ?? 0);
            $costoSinIva = $costoConIva / 1.12; // Asume IVA del 12%
            return $cantidad * $costoSinIva;
        });
    }

    /**
     * @brief Calcula el total de la compra (actualmente igual al subtotal).
     * @return float
     */
    public function getTotalProperty()
    {
        return collect($this->productosSeleccionados)->sum(function($producto) {
            return (float)($producto['cantidad'] ?? 0) * (float)($producto['costo'] ?? 0);
        });
    }

    // --- MÉTODOS DE MANEJO DE SELECCIÓN ---

    /**
     * @brief Selecciona un proveedor de la lista de resultados.
     * @param int $id ID del proveedor a seleccionar.
     * @return void
     */
    public function selectProveedor($id)
    {
        $this->selectedProveedor = collect($this->proveedores)->firstWhere('id', $id);
        $this->showProveedorDropdown = false;
        $this->searchProveedor = '';
    }

    /**
     * @brief Limpia la selección del proveedor actual.
     * @return void
     */
    public function clearProveedor()
    {
        $this->selectedProveedor = null;
    }

    /**
     * @brief Selecciona un producto de la lista de resultados y lo agrega a la compra.
     * @param int $productoId ID del producto a agregar.
     * @return void
     */
    public function selectProducto($productoId)
    {
        $producto = collect($this->productos)->firstWhere('id', (int)$productoId);
        if ($producto && !collect($this->productosSeleccionados)->contains('id', $producto['id'])) {
            $this->productosSeleccionados[] = [
                'id' => $producto['id'],
                'codigo' => $producto['codigo'],
                'descripcion' => $producto['descripcion'],
                'cantidad' => 1,
                'costo' => ''
            ];
        }
        $this->searchProducto = '';
        $this->showProductoDropdown = false;
    }

    /**
     * @brief Selecciona el primer producto de la lista de resultados.
     * Útil para agilizar la selección con la tecla Enter.
     * @return void
     */
    public function seleccionarPrimerResultado()
    {
        $resultados = $this->productoResults;
        if (!empty($resultados)) {
            $this->selectProducto(array_values($resultados)[0]['id']);
        }
    }

    /**
     * @brief Elimina un producto de la lista de la compra.
     * @param int $productoId ID del producto a eliminar.
     * @return void
     */
    public function eliminarProducto($productoId)
    {
        $this->productosSeleccionados = array_values(array_filter(
            $this->productosSeleccionados,
            fn($item) => $item['id'] !== (int)$productoId
        ));
    }

    /**
     * @brief Selecciona una categoría en el modal de creación de producto.
     * @param int $id ID de la categoría a seleccionar.
     * @return void
     */
    public function selectCategoria($id)
    {
        $this->selectedCategoria = collect($this->categoriasActivas)->firstWhere('id', $id);
        $this->categoriaId = $id;
        $this->showCategoriaDropdown = false;
        $this->searchCategoria = '';
    }

    /**
     * @brief Limpia la selección de la categoría actual en el modal.
     * @return void
     */
    public function clearCategoria()
    {
        $this->selectedCategoria = null;
        $this->categoriaId = null;
    }

    /**
     * @brief Selecciona un régimen fiscal en el modal de creación de proveedor.
     * @param string $regimen El régimen a seleccionar.
     * @return void
     */
    public function selectRegimen($regimen)
    {
        $this->selectedRegimen = $regimen;
        $this->nuevoProveedorRegimen = $regimen;
        $this->showRegimenDropdown = false;
    }

    /**
     * @brief Limpia la selección del régimen fiscal actual en el modal.
     * @return void
     */
    public function clearRegimen()
    {
        $this->selectedRegimen = null;
        $this->nuevoProveedorRegimen = '';
    }

    // --- MÉTODOS PARA MODAL DE PRODUCTO ---

    /**
     * @brief Abre el modal para crear un nuevo producto.
     * @return void
     */
    public function abrirModalProducto()
    {
        $this->resetFormProducto();
        $this->showModalProducto = true;
    }

    /**
     * @brief Guarda el nuevo producto y lo agrega a la compra.
     * @return void
     */
    public function guardarNuevoProducto()
    {
        $this->validate([
            'codigo' => 'required|min:3|max:50',
            'descripcion' => 'required|min:3|max:255',
            'categoriaId' => 'required',
        ]);

        $newId = count($this->productos) > 0 ? max(array_column($this->productos, 'id')) + 1 : 1;
        $this->productos[] = [
            'id' => $newId, 'codigo' => $this->codigo,
            'descripcion' => $this->descripcion, 'categoria_id' => (int)$this->categoriaId,
        ];

        $this->selectProducto($newId);
        $this->closeModalProducto();
        session()->flash('message', 'Producto creado y agregado a la compra exitosamente.');
    }

    /**
     * @brief Cierra el modal de creación de producto.
     * @return void
     */
    public function closeModalProducto()
    {
        $this->showModalProducto = false;
        $this->resetFormProducto();
    }

    /**
     * @brief Reinicia el formulario del modal de producto.
     * @return void
     */
    private function resetFormProducto()
    {
        $this->codigo = '';
        $this->descripcion = '';
        $this->categoriaId = '';
        $this->selectedCategoria = null;
        $this->resetErrorBag();
    }

    // --- MÉTODOS PARA SUB-MODAL DE CATEGORÍA ---

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
     * @brief Guarda la nueva categoría y la selecciona.
     * @return void
     */
    public function guardarNuevaCategoria()
    {
        $this->validate(['nuevaCategoriaNombre' => 'required|min:3|max:100']);

        $newId = count($this->categorias) > 0 ? max(array_column($this->categorias, 'id')) + 1 : 1;
        $this->categorias[] = ['id' => $newId, 'nombre' => $this->nuevaCategoriaNombre, 'activo' => true];

        $this->selectCategoria($newId);
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

    // --- MÉTODOS PARA MODAL DE PROVEEDOR ---

    /**
     * @brief Abre el modal para crear un nuevo proveedor.
     * @return void
     */
    public function abrirModalProveedor()
    {
        $this->resetFormProveedor();
        $this->showModalProveedor = true;
        $this->showProveedorDropdown = false;
    }

    /**
     * @brief Guarda el nuevo proveedor y lo selecciona para la compra.
     * @return void
     */
    public function guardarNuevoProveedor()
    {
        $this->validate([
            'nuevoProveedorNit' => 'required|min:5|max:20',
            'nuevoProveedorRegimen' => 'required',
            'nuevoProveedorNombre' => 'required|min:3|max:255',
        ]);

        $newId = count($this->proveedores) > 0 ? max(array_column($this->proveedores, 'id')) + 1 : 1;
        $nuevoProveedor = [
            'id' => $newId, 'nit' => $this->nuevoProveedorNit,
            'regimen' => $this->nuevoProveedorRegimen, 'nombre' => $this->nuevoProveedorNombre,
            'activo' => true,
        ];

        $this->proveedores[] = $nuevoProveedor;
        $this->selectProveedor($newId);
        $this->closeModalProveedor();
        session()->flash('message', 'Proveedor creado y seleccionado exitosamente.');
    }

    /**
     * @brief Cierra el modal de creación de proveedor.
     * @return void
     */
    public function closeModalProveedor()
    {
        $this->showModalProveedor = false;
        $this->resetFormProveedor();
    }

    /**
     * @brief Reinicia el formulario del modal de proveedor.
     * @return void
     */
    private function resetFormProveedor()
    {
        $this->nuevoProveedorNit = '';
        $this->nuevoProveedorRegimen = '';
        $this->nuevoProveedorNombre = '';
        $this->selectedRegimen = null;
        $this->showRegimenDropdown = false;
        $this->resetErrorBag();
    }

    /**
     * @brief Renderiza la vista del componente.
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.formulario-compra');
    }
}
