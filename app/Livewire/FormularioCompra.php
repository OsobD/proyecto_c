<?php

namespace App\Livewire;

use App\Models\Bodega;
use App\Models\Categoria;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\RegimenTributario;
use App\Models\Transaccion;
use App\Models\TipoTransaccion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

/**
 * Componente FormularioCompra
 *
 * Formulario completo para registrar compras con sistema de lotes.
 * Permite seleccionar bodega destino, proveedor, agregar múltiples productos
 * con cantidades, costos y observaciones. Crea automáticamente lotes por cada
 * producto comprado.
 *
 * **Funcionalidades:**
 * - Selección de bodega destino (warehouse)
 * - Búsqueda autocompletable de proveedores y productos
 * - Tabla dinámica de productos seleccionados con cálculo automático de totales
 * - Campo de observaciones por producto para información del lote
 * - Modal de confirmación pre-guardado con resumen
 * - Modal para crear nuevo proveedor durante el registro
 * - Modal para crear nuevo producto durante el registro
 * - Sub-modal para crear categoría al crear producto
 * - Validación de factura y datos de compra
 * - Transacción DB completa: Compra → DetalleCompra → Lote → Transaccion
 *
 * @package App\Livewire
 * @see resources/views/livewire/formulario-compra.blade.php
 */
class FormularioCompra extends Component
{
    // Catálogos de datos
    /** @var array Listado de bodegas disponibles */
    public $bodegas = [];

    /** @var array Listado de proveedores disponibles */
    public $proveedores = [];

    /** @var array Listado de productos disponibles */
    public $productos = [];

    /** @var array Listado de categorías para productos */
    public $categorias = [];

    /** @var array Tipos de régimen tributario */
    public $regimenes = [];

    // Propiedades de selección de bodega
    /** @var string Término de búsqueda de bodega */
    public $searchBodega = '';

    /** @var bool Controla visibilidad del dropdown de bodegas */
    public $showBodegaDropdown = false;

    /** @var array|null Bodega seleccionada actual */
    public $selectedBodega = null;

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

    /** @var array Productos agregados a la compra con cantidad, costo y observaciones */
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

    // Modal de confirmación
    /** @var bool Controla visibilidad del modal de confirmación */
    public $showModalConfirmacion = false;

    /**
     * Inicializa el componente cargando datos reales de la BD
     *
     * @return void
     */
    public function mount()
    {
        // Cargar regímenes tributarios
        $this->regimenes = RegimenTributario::all()->pluck('nombre')->toArray();

        // Cargar bodegas activas
        $this->bodegas = Bodega::where('activo', true)
            ->get()
            ->map(fn($bodega) => [
                'id' => $bodega->id,
                'nombre' => $bodega->nombre,
            ])
            ->toArray();

        // Cargar proveedores activos
        $this->proveedores = Proveedor::with('regimenTributario')
            ->where('activo', true)
            ->get()
            ->map(fn($proveedor) => [
                'id' => $proveedor->id,
                'nit' => $proveedor->nit,
                'regimen' => $proveedor->regimenTributario->nombre ?? 'N/A',
                'nombre' => $proveedor->nombre,
                'activo' => $proveedor->activo,
            ])
            ->toArray();

        // Cargar categorías activas
        $this->categorias = Categoria::where('activo', true)
            ->get()
            ->map(fn($categoria) => [
                'id' => $categoria->id,
                'nombre' => $categoria->nombre,
                'activo' => $categoria->activo,
            ])
            ->toArray();

        // Cargar productos activos
        $this->productos = Producto::with('categoria')
            ->where('activo', true)
            ->get()
            ->map(fn($producto) => [
                'id' => $producto->id,
                'codigo' => $producto->id, // El ID es el código
                'descripcion' => $producto->descripcion,
                'categoria_id' => $producto->id_categoria,
            ])
            ->toArray();

        $this->productosSeleccionados = [];
    }

    public function updatedSearchBodega()
    {
        $this->showBodegaDropdown = true;
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

    public function getBodegaResultsProperty()
    {
        if (empty($this->searchBodega)) {
            return $this->bodegas;
        }

        $search = strtolower(trim($this->searchBodega));

        return array_filter($this->bodegas, function($bodega) use ($search) {
            return str_contains(strtolower($bodega['nombre']), $search);
        });
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

    public function selectBodega($id)
    {
        $this->selectedBodega = collect($this->bodegas)->firstWhere('id', $id);
        $this->showBodegaDropdown = false;
        $this->searchBodega = '';
    }

    public function clearBodega()
    {
        $this->selectedBodega = null;
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
        $producto = collect($this->productos)->firstWhere('id', $productoId);
        if ($producto && !collect($this->productosSeleccionados)->contains('id', $producto['id'])) {
            $this->productosSeleccionados[] = [
                'id' => $producto['id'],
                'codigo' => $producto['codigo'],
                'descripcion' => $producto['descripcion'],
                'cantidad' => '',
                'costo' => '',
                'observaciones' => ''
            ];
        }
        $this->searchProducto = '';
        $this->showProductoDropdown = false;
    }

    public function eliminarProducto($productoId)
    {
        $this->productosSeleccionados = array_filter($this->productosSeleccionados, function($item) use ($productoId) {
            return $item['id'] !== $productoId;
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

    /**
     * Abre el modal de confirmación pre-guardado
     */
    public function abrirModalConfirmacion()
    {
        $this->validate([
            'selectedBodega' => 'required',
            'selectedProveedor' => 'required',
            'numeroFactura' => 'required|min:3',
            'numeroSerie' => 'required|min:3',
            'productosSeleccionados' => 'required|array|min:1',
        ], [
            'selectedBodega.required' => 'Debe seleccionar una bodega destino.',
            'selectedProveedor.required' => 'Debe seleccionar un proveedor.',
            'numeroFactura.required' => 'El número de factura es obligatorio.',
            'numeroSerie.required' => 'El número de serie es obligatorio.',
            'productosSeleccionados.required' => 'Debe agregar al menos un producto a la compra.',
            'productosSeleccionados.min' => 'Debe agregar al menos un producto a la compra.',
        ]);

        // Validar que todos los productos tengan cantidad y costo
        foreach ($this->productosSeleccionados as $index => $producto) {
            $this->validate([
                "productosSeleccionados.{$index}.cantidad" => 'required|integer|min:1',
                "productosSeleccionados.{$index}.costo" => 'required|numeric|min:0',
            ], [
                "productosSeleccionados.{$index}.cantidad.required" => "Debe ingresar la cantidad para el producto {$producto['codigo']}.",
                "productosSeleccionados.{$index}.costo.required" => "Debe ingresar el costo para el producto {$producto['codigo']}.",
            ]);
        }

        $this->showModalConfirmacion = true;
    }

    public function closeModalConfirmacion()
    {
        $this->showModalConfirmacion = false;
    }

    /**
     * Guarda la compra en la base de datos con sistema de lotes
     *
     * Flujo completo:
     * 1. Crea registro Compra
     * 2. Crea registro Transaccion
     * 3. Para cada producto:
     *    - Crea Producto si es nuevo
     *    - Crea DetalleCompra
     *    - Crea Lote con toda la información
     *
     * @return void
     */
    public function guardarCompra()
    {
        try {
            DB::beginTransaction();

            // 1. Obtener o crear tipo de transacción "Compra"
            $tipoTransaccion = TipoTransaccion::firstOrCreate(
                ['nombre' => 'Compra'],
                ['nombre' => 'Compra']
            );

            // 2. Crear registro de Compra
            $compra = Compra::create([
                'fecha' => now(),
                'no_serie' => $this->numeroSerie,
                'no_factura' => $this->numeroFactura,
                'correltivo' => null, // Se puede auto-incrementar con trigger o lógica adicional
                'total' => $this->total,
                'id_proveedor' => $this->selectedProveedor['id'],
                'id_bodega' => $this->selectedBodega['id'],
                'id_usuario' => auth()->id() ?? 1, // Usuario autenticado o default
            ]);

            // 3. Crear registro de Transacción
            $transaccion = Transaccion::create([
                'id_tipo' => $tipoTransaccion->id,
                'id_compra' => $compra->id,
                'id_entrada' => null,
                'id_devolucion' => null,
                'id_traslado' => null,
                'id_salida' => null,
            ]);

            // 4. Procesar cada producto
            foreach ($this->productosSeleccionados as $productoData) {
                $cantidad = (int)$productoData['cantidad'];
                $costoConIva = (float)$productoData['costo'];
                $costoSinIva = $costoConIva * 0.88; // Precio sin IVA
                $observaciones = $productoData['observaciones'] ?? '';

                // 5. Crear DetalleCompra
                $detalleCompra = DetalleCompra::create([
                    'id_compra' => $compra->id,
                    'id_producto' => $productoData['id'],
                    'precio_ingreso' => $costoSinIva,
                    'cantidad' => $cantidad,
                ]);

                // 6. Crear Lote (KEY PART!)
                Lote::create([
                    'cantidad' => $cantidad,
                    'cantidad_inicial' => $cantidad,
                    'fecha_ingreso' => now(),
                    'precio_ingreso' => $costoSinIva,
                    'observaciones' => $observaciones,
                    'id_producto' => $productoData['id'],
                    'id_bodega' => $this->selectedBodega['id'], // Hereda de compra
                    'estado' => true, // Activo
                    'id_transaccion' => $transaccion->id,
                ]);
            }

            DB::commit();

            // Limpiar formulario
            $this->reset([
                'selectedBodega',
                'selectedProveedor',
                'numeroFactura',
                'numeroSerie',
                'productosSeleccionados',
            ]);

            $this->closeModalConfirmacion();

            session()->flash('message', 'Compra registrada exitosamente con ' . count($this->productosSeleccionados) . ' lote(s) creado(s).');

            // Redireccionar a lista de compras
            return redirect()->route('compras.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar compra: ' . $e->getMessage());
            session()->flash('error', 'Error al registrar la compra: ' . $e->getMessage());
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
            'codigo' => 'required|min:3|max:255|unique:producto,id',
            'descripcion' => 'required|min:3|max:255',
            'categoriaId' => 'required|exists:categoria,id',
        ], [
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.min' => 'El código debe tener al menos 3 caracteres.',
            'codigo.unique' => 'Este código de producto ya existe.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 3 caracteres.',
            'categoriaId.required' => 'Debe seleccionar una categoría.',
        ]);

        // Crear nuevo producto
        $nuevoProducto = Producto::create([
            'id' => $this->codigo, // El código ES el ID
            'descripcion' => $this->descripcion,
            'id_categoria' => (int)$this->categoriaId,
        ]);

        // Agregar a la lista local
        $this->productos[] = [
            'id' => $nuevoProducto->id,
            'codigo' => $nuevoProducto->id,
            'descripcion' => $nuevoProducto->descripcion,
            'categoria_id' => $nuevoProducto->id_categoria,
        ];

        // Automáticamente agregar a la compra
        $this->selectProducto($nuevoProducto->id);

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
        $this->selectedCategoria = null;
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

        $nuevaCategoria = Categoria::create([
            'nombre' => $this->nuevaCategoriaNombre,
            'activo' => true,
        ]);

        // Agregar a la lista local
        $this->categorias[] = [
            'id' => $nuevaCategoria->id,
            'nombre' => $nuevaCategoria->nombre,
            'activo' => true,
        ];

        $this->categoriaId = $nuevaCategoria->id;
        $this->selectedCategoria = [
            'id' => $nuevaCategoria->id,
            'nombre' => $nuevaCategoria->nombre,
        ];

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

        // Buscar régimen tributario
        $regimen = RegimenTributario::where('nombre', $this->nuevoProveedorRegimen)->first();

        if (!$regimen) {
            session()->flash('error', 'Régimen tributario no encontrado.');
            return;
        }

        $nuevoProveedor = Proveedor::create([
            'nit' => $this->nuevoProveedorNit,
            'id_regimen' => $regimen->id,
            'nombre' => $this->nuevoProveedorNombre,
            'activo' => true,
        ]);

        // Agregar a lista local
        $proveedorData = [
            'id' => $nuevoProveedor->id,
            'nit' => $nuevoProveedor->nit,
            'regimen' => $this->nuevoProveedorRegimen,
            'nombre' => $nuevoProveedor->nombre,
            'activo' => true,
        ];

        $this->proveedores[] = $proveedorData;

        // Seleccionar automáticamente el nuevo proveedor
        $this->selectedProveedor = $proveedorData;

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
