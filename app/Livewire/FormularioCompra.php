<?php

namespace App\Livewire;

use App\Models\Bitacora;
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
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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

    /** @var string Número de serie de la factura */
    public $numeroSerie = '';

    /** @var string Correlativo de la compra */
    public $correlativo = '';

    /** @var string Precio total según factura física */
    public $precioFactura = '';

    // Arrays temporales para datos creados durante el registro
    /** @var array Productos nuevos creados pero no guardados en DB */
    public $nuevosProductos = [];

    /** @var array Proveedores nuevos creados pero no guardados en DB */
    public $nuevosProveedores = [];

    /** @var array Categorías nuevas creadas pero no guardadas en DB */
    public $nuevasCategorias = [];

    /** @var int Contador para IDs temporales negativos - debe ser público para persistir en Livewire */
    public $tempIdCounter = -1;

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

    /** @var bool Indica si el nuevo producto es consumible */
    public $esConsumible = false;

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
                'es_consumible' => $producto->es_consumible,
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

        return array_filter($this->bodegas, function ($bodega) use ($search) {
            return str_contains(strtolower($bodega['nombre']), $search);
        });
    }

    public function getProveedorResultsProperty()
    {
        if (empty($this->searchProveedor)) {
            return $this->proveedores;
        }

        $search = strtolower(trim($this->searchProveedor));

        return array_filter($this->proveedores, function ($proveedor) use ($search) {
            return str_contains(strtolower($proveedor['nombre']), $search);
        });
    }

    public function getProductoResultsProperty()
    {
        if (empty($this->searchProducto)) {
            return $this->productos;
        }

        $search = strtolower(trim($this->searchProducto));

        return array_filter($this->productos, function ($producto) use ($search) {
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

        return array_filter($categorias, function ($categoria) use ($search) {
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
                'es_consumible' => $producto['es_consumible'] ?? false,
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
        // Si el ID es negativo, es un producto temporal nuevo
        if ($productoId < 0) {
            $this->nuevosProductos = array_values(
                array_filter($this->nuevosProductos, function ($item) use ($productoId) {
                    return $item['id'] !== $productoId;
                })
            );
        }

        // Filtrar y reindexar en una sola operación para forzar reactividad
        $this->productosSeleccionados = array_values(
            array_filter($this->productosSeleccionados, function ($item) use ($productoId) {
                return $item['id'] !== $productoId;
            })
        );
    }

    /**
     * Calcula el subtotal (precio sin IVA)
     * En Guatemala: Precio con IVA / 1.12 = Precio sin IVA
     */
    public function getSubtotalProperty()
    {
        return collect($this->productosSeleccionados)->sum(function ($producto) {
            $cantidad = (float) ($producto['cantidad'] ?? 0);
            $costoConIva = (float) ($producto['costo'] ?? 0);
            // Dividir entre 1.12 para obtener el precio sin IVA
            $costoSinIva = $costoConIva / 1.12;
            return $cantidad * $costoSinIva;
        });
    }

    /**
     * Calcula el IVA (12% en Guatemala)
     * IVA = Subtotal * 0.12
     */
    public function getIvaProperty()
    {
        return $this->subtotal * 0.12;
    }

    /**
     * Calcula el total (Subtotal + IVA)
     */
    public function getTotalProperty()
    {
        return $this->subtotal + $this->iva;
    }

    /**
     * Verifica si el total calculado coincide con el precio de factura
     */
    public function getDiferenciaFacturaProperty()
    {
        if (empty($this->precioFactura)) {
            return 0;
        }
        $precioFacturaNum = (float) $this->precioFactura;
        return $precioFacturaNum - $this->total;
    }

    // Asegurar que los valores sean numéricos cuando se actualizan
    // Validaciones en tiempo real
    public function updatedCorrelativo()
    {
        $this->validateOnly('correlativo', [
            'correlativo' => 'required|min:3|unique:compra,correlativo'
        ], [
            'correlativo.unique' => 'Este correlativo ya existe en el sistema.',
            'correlativo.required' => 'El correlativo es obligatorio.',
            'correlativo.min' => 'El correlativo debe tener al menos 3 caracteres.'
        ]);
    }

    public function updatedNumeroFactura()
    {
        $this->validarFacturaUnica();
    }

    public function updatedNumeroSerie()
    {
        $this->validarFacturaUnica();
    }

    public function updatedSelectedProveedor()
    {
        // Si se selecciona un proveedor, validar si la factura ya existe
        if ($this->selectedProveedor) {
            $this->validarFacturaUnica();
        }
    }

    protected function validarFacturaUnica()
    {
        // Solo validar si tenemos factura y proveedor
        if (!empty($this->numeroFactura) && !empty($this->selectedProveedor)) {
             $this->validate([
                'numeroFactura' => [
                    Rule::unique('compra', 'no_factura')->where(function ($query) {
                        return $query->where('id_proveedor', $this->selectedProveedor['id'])
                                     ->where('no_serie', $this->numeroSerie);
                    }),
                ],
            ], [
                'numeroFactura.unique' => 'Esta factura ya ha sido registrada.'
            ]);
        }
    }

    // Asegurar que los valores sean numéricos cuando se actualizan
    // public function updated($propertyName)
    // {
    //     // Lógica eliminada para evitar conflictos con la escritura rápida del usuario
    //     // La validación se encarga de asegurar los tipos de datos
    // }

    /**
     * Abre el modal de confirmación pre-guardado
     */
    public function abrirModalConfirmacion()
    {
        $this->validate([
            'selectedBodega' => 'required',
            'selectedProveedor' => 'required',
            'numeroFactura' => [
                'required',
                'min:3',
                Rule::unique('compra', 'no_factura')->where(function ($query) {
                    return $query->where('id_proveedor', $this->selectedProveedor['id'] ?? null)
                                 ->where('no_serie', $this->numeroSerie);
                }),
            ],
            'numeroSerie' => 'nullable|min:1',
            'correlativo' => 'required|min:3|unique:compra,correlativo',
            'precioFactura' => 'nullable|numeric|min:0',
            'productosSeleccionados' => 'required|array|min:1',
        ], [
            'selectedBodega.required' => 'Debe seleccionar una bodega destino.',
            'selectedProveedor.required' => 'Debe seleccionar un proveedor.',
            'numeroFactura.required' => 'El número de factura es obligatorio.',
            'numeroFactura.unique' => 'Esta factura ya ha sido registrada para este proveedor.',
            'numeroSerie.min' => 'El número de serie debe tener al menos 1 carácter.',
            'correlativo.required' => 'El correlativo es obligatorio.',
            'correlativo.unique' => 'Este correlativo ya existe en el sistema.',
            'precioFactura.numeric' => 'El precio de factura debe ser un número válido.',
            'precioFactura.min' => 'El precio de factura debe ser mayor o igual a 0.',
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

            // 0. Mapeo de IDs temporales a IDs reales
            $mapeoIds = [
                'categorias' => [],
                'productos' => [],
                'proveedores' => [],
            ];

            // 1. Crear categorías temporales primero
            foreach ($this->nuevasCategorias as $categoriaTemp) {
                $nuevaCategoria = Categoria::create([
                    'nombre' => $categoriaTemp['nombre'],
                    'activo' => true,
                ]);
                $mapeoIds['categorias'][$categoriaTemp['id']] = $nuevaCategoria->id;

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Crear',
                    'modelo' => 'Categoria',
                    'modelo_id' => $nuevaCategoria->id,
                    'descripcion' => "Categoría creada desde compra: {$nuevaCategoria->nombre}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);
            }

            // 2. Crear proveedores temporales
            foreach ($this->nuevosProveedores as $proveedorTemp) {
                $regimen = RegimenTributario::where('nombre', $proveedorTemp['regimen'])->first();
                $nuevoProveedor = Proveedor::create([
                    'nit' => $proveedorTemp['nit'],
                    'id_regimen' => $regimen->id,
                    'nombre' => $proveedorTemp['nombre'],
                    'activo' => true,
                ]);
                $mapeoIds['proveedores'][$proveedorTemp['id']] = $nuevoProveedor->id;

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Crear',
                    'modelo' => 'Proveedor',
                    'modelo_id' => $nuevoProveedor->id,
                    'descripcion' => "Proveedor creado desde compra: {$nuevoProveedor->nombre}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);
            }

            // 3. Crear productos temporales (con categorías ya mapeadas)
            foreach ($this->nuevosProductos as $productoTemp) {
                $categoriaIdReal = $productoTemp['categoria_id'];
                // Si la categoría es temporal, usar el ID real mapeado
                if ($categoriaIdReal < 0 && isset($mapeoIds['categorias'][$categoriaIdReal])) {
                    $categoriaIdReal = $mapeoIds['categorias'][$categoriaIdReal];
                }

                $nuevoProducto = Producto::create([
                    'id' => $productoTemp['codigo'],
                    'descripcion' => $productoTemp['descripcion'],
                    'id_categoria' => $categoriaIdReal,
                    'es_consumible' => $productoTemp['es_consumible'] ?? false,
                    'activo' => true,
                ]);
                $mapeoIds['productos'][$productoTemp['id']] = $nuevoProducto->id;

                // Registrar en bitácora
                Bitacora::create([
                    'accion' => 'Crear',
                    'modelo' => 'Producto',
                    'modelo_id' => $nuevoProducto->id,
                    'descripcion' => "Producto creado desde compra: {$nuevoProducto->descripcion}",
                    'id_usuario' => Auth::id(),
                    'created_at' => now(),
                ]);
            }

            // 4. Mapear proveedor si es temporal
            $proveedorIdReal = $this->selectedProveedor['id'];
            if ($proveedorIdReal < 0 && isset($mapeoIds['proveedores'][$proveedorIdReal])) {
                $proveedorIdReal = $mapeoIds['proveedores'][$proveedorIdReal];
            }

            // 5. Obtener o crear tipo de transacción "Compra"
            $tipoTransaccion = TipoTransaccion::firstOrCreate(
                ['nombre' => 'Compra'],
                ['nombre' => 'Compra']
            );

            // 6. Obtener usuario para la compra
            $idUsuario = null;

            Log::info('Intentando obtener usuario', [
                'session_usuario_id' => session('usuario_id'),
                'auth_check' => auth()->check(),
                'auth_id' => auth()->id(),
                'session_all' => session()->all()
            ]);

            // Intentar obtener de sesión (si hay autenticación custom)
            if (session()->has('usuario_id')) {
                $idUsuario = session('usuario_id');
                Log::info('Usuario obtenido de sesión custom', ['id_usuario' => $idUsuario]);
            }
            // Intentar obtener de auth() de Laravel
            elseif (auth()->check()) {
                $idUsuario = auth()->id();
                Log::info('Usuario obtenido de auth() Laravel', ['id_usuario' => $idUsuario]);
            }
            // Si no hay usuario, usar el primero disponible (para desarrollo/testing)
            else {
                $primerUsuario = \App\Models\Usuario::first();
                if (!$primerUsuario) {
                    throw new \Exception('No hay usuarios disponibles en el sistema. Por favor, cree un usuario primero.');
                }
                $idUsuario = $primerUsuario->id;
                Log::info('Usando primer usuario disponible para compra (desarrollo)', ['id_usuario' => $idUsuario, 'nombre' => $primerUsuario->nombre]);
            }

            // Crear registro de Compra
            // TEMPORAL: Permitir null en id_usuario si el usuario no existe en la tabla
            Log::info('Validando usuario antes de crear compra', ['id_usuario_a_validar' => $idUsuario]);

            $usuarioValido = $idUsuario ? \App\Models\Usuario::find($idUsuario) : null;

            Log::info('Resultado de validación de usuario', [
                'id_usuario_original' => $idUsuario,
                'usuario_valido' => $usuarioValido ? 'sí' : 'no',
                'id_a_usar' => $usuarioValido ? $usuarioValido->id : 'null'
            ]);

            $compra = Compra::create([
                'fecha' => now(),
                'no_factura' => $this->numeroFactura,
                'no_serie' => $this->numeroSerie,
                'correlativo' => $this->correlativo,
                'total' => $this->total,
                'precio_factura' => !empty($this->precioFactura) ? (float) $this->precioFactura : null,
                'id_proveedor' => $proveedorIdReal,
                'id_bodega' => $this->selectedBodega['id'],
                'id_usuario' => $usuarioValido ? $usuarioValido->id : null,
            ]);

            Log::info('Compra creada exitosamente', ['compra_id' => $compra->id, 'usuario_usado' => $usuarioValido ? $usuarioValido->id : 'null']);

            // 7. Crear registro de Transacción
            $transaccion = Transaccion::create([
                'id_tipo' => $tipoTransaccion->id,
                'id_compra' => $compra->id,
                'id_entrada' => null,
                'id_devolucion' => null,
                'id_traslado' => null,
                'id_salida' => null,
            ]);

            // 8. Procesar cada producto (mapeando IDs temporales)
            foreach ($this->productosSeleccionados as $productoData) {
                $productoIdReal = $productoData['id'];
                // Si el producto es temporal, usar el ID real mapeado
                if ($productoIdReal < 0 && isset($mapeoIds['productos'][$productoIdReal])) {
                    $productoIdReal = $mapeoIds['productos'][$productoIdReal];
                }

                $cantidad = (int) $productoData['cantidad'];
                $costoConIva = (float) $productoData['costo'];
                // Corregido: Dividir entre 1.12 para obtener precio sin IVA (12% Guatemala)
                $costoSinIva = $costoConIva / 1.12;
                $observaciones = $productoData['observaciones'] ?? '';

                // 9. Crear DetalleCompra
                DetalleCompra::create([
                    'id_compra' => $compra->id,
                    'id_producto' => $productoIdReal,
                    'precio_ingreso' => $costoSinIva,
                    'cantidad' => $cantidad,
                ]);

                // 10. Crear Lote (independiente de bodega)
                $lote = Lote::create([
                    'cantidad_disponible' => $cantidad,
                    'cantidad_inicial' => $cantidad,
                    'fecha_ingreso' => now(),
                    'precio_ingreso' => $costoSinIva,
                    'observaciones' => $observaciones,
                    'id_producto' => $productoIdReal,
                    'id_bodega' => $this->selectedBodega['id'], // Mantenido temporalmente para compatibilidad
                    'estado' => true,
                    'id_transaccion' => $transaccion->id,
                ]);

                // 11. Registrar ubicación del lote en la bodega
                $lote->incrementarEnBodega($this->selectedBodega['id'], $cantidad);
            }

            // Registrar Compra en bitácora
            Bitacora::create([
                'accion' => 'Crear',
                'modelo' => 'Compra',
                'modelo_id' => $compra->id,
                'descripcion' => "Compra registrada: Factura #{$compra->no_factura}",
                'id_usuario' => Auth::id(),
                'created_at' => now(),
            ]);

            DB::commit();

            $cantidadLotes = count($this->productosSeleccionados);

            // Limpiar formulario
            $this->reset([
                'selectedBodega',
                'selectedProveedor',
                'numeroFactura',
                'numeroSerie',
                'correlativo',
                'precioFactura',
                'productosSeleccionados',
                'nuevosProductos',
                'nuevosProveedores',
                'nuevasCategorias',
            ]);

            $this->closeModalConfirmacion();

            session()->flash('message', "Compra registrada exitosamente con {$cantidadLotes} lote(s) creado(s).");

            // Redireccionar a lista de compras (ruta corregida)
            return redirect()->route('compras');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar compra: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Cerrar el modal incluso si hay error
            $this->closeModalConfirmacion();

            // Mostrar el error completo para depuración
            $mensajeError = 'Error al registrar la compra: ' . $e->getMessage();

            session()->flash('error', $mensajeError);
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
        // Validar (sin verificar unique en DB, solo en productos locales y temporales)
        $this->validate([
            'codigo' => 'required|min:3|max:255',
            'descripcion' => 'required|min:3|max:255',
            'categoriaId' => 'required',
        ], [
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.min' => 'El código debe tener al menos 3 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 3 caracteres.',
            'categoriaId.required' => 'Debe seleccionar una categoría.',
        ]);

        // Verificar si ya existe en DB o en temporales
        $existeEnBD = Producto::where('id', $this->codigo)->exists();
        $existeEnTemporales = collect($this->nuevosProductos)->contains('codigo', $this->codigo);

        if ($existeEnBD || $existeEnTemporales) {
            $this->addError('codigo', 'Este código de producto ya existe.');
            return;
        }

        // Generar ID temporal negativo
        $idTemporal = $this->tempIdCounter--;

        // Agregar a array temporal (NO guardar en DB)
        $productoTemp = [
            'id' => $idTemporal,
            'codigo' => $this->codigo,
            'descripcion' => $this->descripcion,
            'categoria_id' => (int) $this->categoriaId,
            'es_consumible' => $this->esConsumible,
        ];

        $this->nuevosProductos[] = $productoTemp;

        // Agregar a la lista local para que aparezca en dropdown
        $this->productos[] = $productoTemp;

        // Automáticamente agregar a la compra
        $this->selectProducto($idTemporal);

        $this->closeModalProducto();
        session()->flash('message', 'Producto agregado. Se guardará al confirmar la compra.');
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
        $this->esConsumible = false;
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

        // Generar ID temporal negativo
        $idTemporal = $this->tempIdCounter--;

        // Agregar a array temporal (NO guardar en DB)
        $categoriaTemp = [
            'id' => $idTemporal,
            'nombre' => $this->nuevaCategoriaNombre,
            'activo' => true,
        ];

        $this->nuevasCategorias[] = $categoriaTemp;

        // Agregar a la lista local para que aparezca en dropdown
        $this->categorias[] = $categoriaTemp;

        $this->categoriaId = $idTemporal;
        $this->selectedCategoria = $categoriaTemp;

        $this->showSubModalCategoria = false;
        $this->nuevaCategoriaNombre = '';
        session()->flash('message', 'Categoría agregada. Se guardará al confirmar la compra.');
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
            'nuevoProveedorNit' => 'required|numeric|digits_between:5,20|unique:proveedor,nit',
            'nuevoProveedorRegimen' => 'required',
            'nuevoProveedorNombre' => 'required|min:3|max:255',
        ], [
            'nuevoProveedorNit.required' => 'El NIT es obligatorio.',
            'nuevoProveedorNit.numeric' => 'El NIT debe contener solo números.',
            'nuevoProveedorNit.digits_between' => 'El NIT debe tener entre 5 y 20 dígitos.',
            'nuevoProveedorNit.unique' => 'Ya existe un proveedor registrado con este NIT.',
            'nuevoProveedorRegimen.required' => 'Debe seleccionar un régimen.',
            'nuevoProveedorNombre.required' => 'El nombre del proveedor es obligatorio.',
            'nuevoProveedorNombre.min' => 'El nombre debe tener al menos 3 caracteres.',
        ]);

        // Buscar régimen tributario para validar que existe
        $regimen = RegimenTributario::where('nombre', $this->nuevoProveedorRegimen)->first();

        if (!$regimen) {
            session()->flash('error', 'Régimen tributario no encontrado.');
            return;
        }

        // Generar ID temporal negativo
        $idTemporal = $this->tempIdCounter--;

        // Agregar a array temporal (NO guardar en DB)
        $proveedorTemp = [
            'id' => $idTemporal,
            'nit' => $this->nuevoProveedorNit,
            'regimen' => $this->nuevoProveedorRegimen,
            'nombre' => $this->nuevoProveedorNombre,
            'activo' => true,
        ];

        $this->nuevosProveedores[] = $proveedorTemp;

        // Agregar a lista local para que aparezca en dropdown
        $this->proveedores[] = $proveedorTemp;

        // Seleccionar automáticamente el nuevo proveedor
        $this->selectedProveedor = $proveedorTemp;

        $this->closeModalProveedor();
        session()->flash('message', 'Proveedor agregado. Se guardará al confirmar la compra.');
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
