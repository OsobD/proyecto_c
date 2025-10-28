<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Componente FormularioCompra
 *
 * Formulario completo para registrar compras al sistema. Permite seleccionar proveedor,
 * agregar múltiples productos con cantidades y costos, y crear proveedores/productos
 * sobre la marcha mediante modales auxiliares.
 *
 * **Funcionalidades:**
 * - Búsqueda autocompletable de proveedores y productos
 * - Tabla dinámica de productos seleccionados con cálculo automático de totales
 * - Modal para crear nuevo proveedor durante el registro
 * - Modal para crear nuevo producto durante el registro
 * - Sub-modal para crear categoría al crear producto
 * - Validación de factura y datos de compra
 *
 * @package App\Livewire
 * @see resources/views/livewire/formulario-compra.blade.php
 */
class FormularioCompra extends Component
{
    // Catálogos de datos
    /** @var array Listado de proveedores disponibles */
    public $proveedores = [];

    /** @var array Listado de productos disponibles */
    public $productos = [];

    /** @var array Listado de categorías para productos */
    public $categorias = [];

    /** @var array Tipos de régimen tributario */
    public $regimenes = [];

    // Propiedades de selección de proveedor
    /** @var string Término de búsqueda de proveedor */
    public $searchProveedor = '';

    /** @var bool Controla visibilidad del dropdown de proveedores */
    public $showProveedorDropdown = false;

    /** @var array|null Proveedor seleccionado actual */
    public $selectedProveedor = null;

    // Propiedades de selección de productos
    /** @var string Término de búsqueda de producto */
    public $searchProducto = '';

    /** @var bool Controla visibilidad del dropdown de productos */
    public $showProductoDropdown = false;

    /** @var array Productos agregados a la compra con cantidad y costo */
    public $productosSeleccionados = [];

    // Propiedades de búsqueda de categoría (para modal de producto)
    /** @var string Término de búsqueda de categoría */
    public $searchCategoria = '';

    /** @var bool Controla visibilidad del dropdown de categorías */
    public $showCategoriaDropdown = false;

    /** @var array|null Categoría seleccionada al crear producto */
    public $selectedCategoria = null;

    // Datos de factura
    /** @var string Número de factura de la compra */
    public $numeroFactura = '';

    /** @var string Serie de la factura */
    public $numeroSerie = '';

    // Propiedades del modal de creación de producto
    /** @var bool Controla visibilidad del modal de producto */
    public $showModalProducto = false;

    /** @var bool Controla visibilidad del sub-modal de categoría */
    public $showSubModalCategoria = false;

    /** @var string Código del nuevo producto */
    public $codigo = '';

    /** @var string Descripción del nuevo producto */
    public $descripcion = '';

    /** @var string|int ID de categoría del nuevo producto */
    public $categoriaId = '';

    /** @var string Nombre de nueva categoría a crear */
    public $nuevaCategoriaNombre = '';

    // Propiedades del modal de creación de proveedor
    /** @var bool Controla visibilidad del modal de proveedor */
    public $showModalProveedor = false;

    /** @var string NIT del nuevo proveedor */
    public $nuevoProveedorNit = '';

    /** @var string Régimen tributario del nuevo proveedor */
    public $nuevoProveedorRegimen = '';

    /** @var string Nombre del nuevo proveedor */
    public $nuevoProveedorNombre = '';

    // Propiedades del dropdown de régimen
    /** @var bool Controla visibilidad del dropdown de régimen */
    public $showRegimenDropdown = false;

    /** @var string|null Régimen seleccionado */
    public $selectedRegimen = null;

    /**
     * Inicializa el componente con datos mock de prueba
     *
     * @todo Reemplazar con consultas a BD: Proveedor::all(), Producto::all(), Categoria::all()
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
            ['id' => 1, 'codigo' => 'PROD-001', 'descripcion' => 'Tornillos de acero inoxidable'],
            ['id' => 2, 'codigo' => 'PROD-002', 'descripcion' => 'Abrazaderas de metal'],
            ['id' => 3, 'codigo' => 'PROD-003', 'descripcion' => 'Cinta aislante'],
            ['id' => 4, 'codigo' => 'PROD-004', 'descripcion' => 'Guantes de seguridad'],
            ['id' => 5, 'codigo' => 'PROD-005', 'descripcion' => 'Fusibles de 15A'],
            ['id' => 1, 'codigo' => 'PROD-001', 'descripcion' => 'Tornillos de acero inoxidable'],
            ['id' => 2, 'codigo' => 'PROD-002', 'descripcion' => 'Abrazaderas de metal'],
            ['id' => 3, 'codigo' => 'PROD-003', 'descripcion' => 'Cinta aislante'],
            ['id' => 4, 'codigo' => 'PROD-004', 'descripcion' => 'Guantes de seguridad'],
            ['id' => 5, 'codigo' => 'PROD-005', 'descripcion' => 'Fusibles de 15A'],
            ['id' => 1, 'codigo' => 'PROD-001', 'descripcion' => 'Tornillos de acero inoxidable'],
            ['id' => 2, 'codigo' => 'PROD-002', 'descripcion' => 'Abrazaderas de metal'],
            ['id' => 3, 'codigo' => 'PROD-003', 'descripcion' => 'Cinta aislante'],
            ['id' => 4, 'codigo' => 'PROD-004', 'descripcion' => 'Guantes de seguridad'],
            ['id' => 5, 'codigo' => 'PROD-005', 'descripcion' => 'Fusibles de 15A'],
        ];

        $this->productosSeleccionados = [];
    }

    public function updatedSearchProveedor()
    {
        $this->showProveedorDropdown = true;
    }

    public function updatedSearchProducto()
    {
        $this->showProductoDropdown = true;
    }

    public function updatedSearchCategoria()
    {
        $this->showCategoriaDropdown = true;
    }

    public function getProveedorResultsProperty()
    {
        if (empty($this->searchProveedor)) {
            return $this->proveedores;
        }

        $search = strtolower(trim($this->searchProveedor));

        return array_filter($this->proveedores, function($proveedor) use ($search) {
            return str_contains(strtolower($proveedor['nombre']), $search);
        });
    }

    public function getProductoResultsProperty()
    {
        if (empty($this->searchProducto)) {
            return $this->productos;
        }

        $search = strtolower(trim($this->searchProducto));

        return array_filter($this->productos, function($producto) use ($search) {
            return str_contains(strtolower($producto['descripcion']), $search) ||
                   str_contains(strtolower($producto['codigo']), $search);
        });
    }

    public function getCategoriaResultsProperty()
    {
        $categorias = $this->categoriasActivas;

        if (empty($this->searchCategoria)) {
            return $categorias;
        }

        $search = strtolower(trim($this->searchCategoria));

        return array_filter($categorias, function($categoria) use ($search) {
            return str_contains(strtolower($categoria['nombre']), $search);
        });
    }

    public function selectProveedor($id)
    {
        $this->selectedProveedor = collect($this->proveedores)->firstWhere('id', $id);
        $this->showProveedorDropdown = false;
        $this->searchProveedor = '';
    }

    public function clearProveedor()
    {
        $this->selectedProveedor = null;
    }

    public function seleccionarPrimerResultado()
    {
        $resultados = $this->productoResults;
        if (!empty($resultados)) {
            $primerProducto = array_values($resultados)[0];
            $this->selectProducto($primerProducto['id']);
        }
    }

    public function selectCategoria($id)
    {
        $this->selectedCategoria = collect($this->categoriasActivas)->firstWhere('id', $id);
        $this->categoriaId = $id;
        $this->showCategoriaDropdown = false;
        $this->searchCategoria = '';
    }

    public function clearCategoria()
    {
        $this->selectedCategoria = null;
        $this->categoriaId = null;
    }

    public function selectProducto($productoId)
    {
        $producto = collect($this->productos)->firstWhere('id', (int)$productoId);
        if ($producto && !collect($this->productosSeleccionados)->contains('id', $producto['id'])) {
            $this->productosSeleccionados[] = [
                'id' => $producto['id'],
                'codigo' => $producto['codigo'],
                'descripcion' => $producto['descripcion'],
                'cantidad' => '',
                'costo' => ''
            ];
        }
        $this->searchProducto = '';
        $this->showProductoDropdown = false;
    }

    public function eliminarProducto($productoId)
    {
        $this->productosSeleccionados = array_filter($this->productosSeleccionados, function($item) use ($productoId) {
            return $item['id'] !== (int)$productoId;
        });
        // Re-index the array
        $this->productosSeleccionados = array_values($this->productosSeleccionados);
    }

    public function getSubtotalProperty()
    {
        return collect($this->productosSeleccionados)->sum(function($producto) {
            $cantidad = (float)($producto['cantidad'] ?? 0);
            $costoConIva = (float)($producto['costo'] ?? 0);
            $costoSinIva = $costoConIva * 0.88; // Resta el 12% de IVA
            return $cantidad * $costoSinIva;
        });
    }

    public function getTotalProperty()
    {
        return $this->subtotal;
    }

    // Asegurar que los valores sean numéricos cuando se actualizan
    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'productosSeleccionados.')) {
            $parts = explode('.', $propertyName);
            if (count($parts) === 3) {
                $index = $parts[1];
                $field = $parts[2];

                if ($field === 'cantidad') {
                    $valor = $this->productosSeleccionados[$index]['cantidad'];
                    // Si está vacío o es null, establecer 1 como mínimo
                    $this->productosSeleccionados[$index]['cantidad'] = empty($valor) ? 1 : max(1, (int)$valor);
                } elseif ($field === 'costo') {
                    $valor = $this->productosSeleccionados[$index]['costo'];
                    // Si está vacío o es null, establecer 0
                    $this->productosSeleccionados[$index]['costo'] = ($valor === '' || $valor === null) ? 0 : max(0, (float)$valor);
                }
            }
        }
    }

    // Modal de creación de producto
    public function abrirModalProducto()
    {
        $this->resetFormProducto();
        $this->showModalProducto = true;
    }

    public function getCategoriasActivasProperty()
    {
        return array_filter($this->categorias, fn($cat) => $cat['activo']);
    }

    public function guardarNuevoProducto()
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

        // Crear nuevo producto
        $newId = max(array_column($this->productos, 'id')) + 1;
        $nuevoProducto = [
            'id' => $newId,
            'codigo' => $this->codigo,
            'descripcion' => $this->descripcion,
            'categoria_id' => (int)$this->categoriaId,
        ];

        $this->productos[] = $nuevoProducto;

        // Automáticamente agregar a la compra
        $this->selectProducto($newId);

        $this->closeModalProducto();
        session()->flash('message', 'Producto creado y agregado a la compra exitosamente.');
    }

    public function closeModalProducto()
    {
        $this->showModalProducto = false;
        $this->resetFormProducto();
    }

    private function resetFormProducto()
    {
        $this->codigo = '';
        $this->descripcion = '';
        $this->categoriaId = '';
        $this->resetErrorBag();
    }

    // Sub-modal de categorías
    public function abrirSubModalCategoria()
    {
        $this->nuevaCategoriaNombre = '';
        $this->showSubModalCategoria = true;
    }

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

    public function closeSubModalCategoria()
    {
        $this->showSubModalCategoria = false;
        $this->nuevaCategoriaNombre = '';
    }

    // Modal de creación de proveedor
    public function abrirModalProveedor()
    {
        $this->nuevoProveedorNit = '';
        $this->nuevoProveedorRegimen = '';
        $this->nuevoProveedorNombre = '';
        $this->selectedRegimen = null;
        $this->showRegimenDropdown = false;
        $this->showModalProveedor = true;
        $this->showProveedorDropdown = false;
    }

    public function selectRegimen($regimen)
    {
        $this->selectedRegimen = $regimen;
        $this->nuevoProveedorRegimen = $regimen;
        $this->showRegimenDropdown = false;
    }

    public function clearRegimen()
    {
        $this->selectedRegimen = null;
        $this->nuevoProveedorRegimen = '';
    }

    public function guardarNuevoProveedor()
    {
        $this->validate([
            'nuevoProveedorNit' => 'required|min:5|max:20',
            'nuevoProveedorRegimen' => 'required',
            'nuevoProveedorNombre' => 'required|min:3|max:255',
        ], [
            'nuevoProveedorNit.required' => 'El NIT es obligatorio.',
            'nuevoProveedorNit.min' => 'El NIT debe tener al menos 5 caracteres.',
            'nuevoProveedorRegimen.required' => 'Debe seleccionar un régimen.',
            'nuevoProveedorNombre.required' => 'El nombre del proveedor es obligatorio.',
            'nuevoProveedorNombre.min' => 'El nombre debe tener al menos 3 caracteres.',
        ]);

        $newId = max(array_column($this->proveedores, 'id')) + 1;
        $nuevoProveedor = [
            'id' => $newId,
            'nit' => $this->nuevoProveedorNit,
            'regimen' => $this->nuevoProveedorRegimen,
            'nombre' => $this->nuevoProveedorNombre,
            'activo' => true,
        ];

        $this->proveedores[] = $nuevoProveedor;

        // Seleccionar automáticamente el nuevo proveedor
        $this->selectedProveedor = $nuevoProveedor;

        $this->closeModalProveedor();
        session()->flash('message', 'Proveedor creado y seleccionado exitosamente.');
    }

    public function closeModalProveedor()
    {
        $this->showModalProveedor = false;
        $this->nuevoProveedorNit = '';
        $this->nuevoProveedorRegimen = '';
        $this->nuevoProveedorNombre = '';
        $this->selectedRegimen = null;
        $this->showRegimenDropdown = false;
        $this->resetErrorBag();
    }

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.formulario-compra');
    }
}
