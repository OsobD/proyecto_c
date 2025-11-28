<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\KardexService;
use App\Models\Usuario;
use App\Models\Proveedor;
use App\Models\Bodega;
use App\Models\Producto;
use App\Models\Compra;
use App\Models\Traslado;
use App\Models\Bitacora;
use App\Models\DetalleCompra;
use App\Models\DetalleTraslado;
use Illuminate\Support\Facades\DB;

/**
 * Componente GenerarReportes
 *
 * Módulo de generación de reportes del sistema con múltiples categorías:
 * compras, traslados, inventario y bitácora. Permite filtrar por fechas,
 * usuarios, proveedores y bodegas, y exportar en múltiples formatos.
 *
 * **Tipos de reportes disponibles:**
 * - Compras: por proveedor, período, análisis de costos, por categoría
 * - Traslados: por bodega, período, requisiciones, devoluciones
 * - Inventario: existencias, valorización, productos bajo stock, Kardex
 * - Bitácora: movimientos, auditoría, actividad por usuario
 *
 * @package App\Livewire
 * @see resources/views/livewire/generar-reportes.blade.php
 */
class GenerarReportes extends Component
{
    // Propiedades de interfaz
    /** @var string Tab activo actual (compras|traslados|inventario|bitacora) */
    public $tabActivo = 'compras';

    /** @var string ID del tipo de reporte seleccionado */
    public $tipoReporte = '';

    // Filtros de fecha
    /** @var string Fecha de inicio del rango */
    public $fechaInicio = '';

    /** @var string Fecha de fin del rango */
    public $fechaFin = '';

    // Filtros adicionales
    /** @var string|int ID de usuario seleccionado */
    public $usuarioSeleccionado = '';

    /** @var string|int ID de proveedor seleccionado */
    public $proveedorSeleccionado = '';

    /** @var string|int ID de bodega seleccionada */
    public $bodegaSeleccionada = '';

    /** @var string|int ID de producto seleccionado (para Kardex) */
    public $productoSeleccionado = '';

    // Propiedades de búsqueda para filtros
    public $searchProveedorFiltro = '';
    public $searchBodegaFiltro = '';
    public $searchProductoFiltro = '';
    public $searchUsuarioFiltro = '';
    public $searchTipoReporteFiltro = '';

    // Control de visibilidad de dropdowns
    public $showProveedorDropdown = false;
    public $showBodegaDropdown = false;
    public $showProductoDropdown = false;
    public $showUsuarioDropdown = false;
    public $showTipoReporteDropdown = false;

    // Filtros seleccionados
    public $selectedProveedorFiltro = null;
    public $selectedBodegaFiltro = null;
    public $selectedProductoFiltro = null;
    public $selectedUsuarioFiltro = null;
    public $selectedTipoReporteFiltro = null;

    // Catálogos para filtros
    /** @var array Lista de usuarios del sistema */
    public $usuarios = [];

    /** @var array Lista de proveedores activos */
    public $proveedores = [];

    /** @var array Lista de bodegas disponibles */
    public $bodegas = [];

    /** @var array Lista de productos disponibles */
    public $productos = [];

    // Definiciones de reportes por categoría
    /** @var array Tipos de reportes de compras */
    public $reportesCompras = [];

    /** @var array Tipos de reportes de traslados */
    public $reportesTraslados = [];

    /** @var array Tipos de reportes de inventario */
    public $reportesInventario = [];

    /** @var array Tipos de reportes de bitácora */
    public $reportesBitacora = [];

    // Datos del reporte generado
    /** @var array Datos del reporte Kardex */
    public $datosKardex = [];

    /** @var array Datos del reporte actual */
    public $datosReporte = [];

    /** @var string Título del reporte actual */
    public $tituloReporte = '';

    public function mount()
    {
        // Cargar datos reales desde la base de datos
        $this->cargarCatalogos();

        $this->reportesCompras = [
            ['id' => 'compras_proveedor', 'nombre' => 'Compras por Proveedor'],
            ['id' => 'compras_periodo', 'nombre' => 'Compras por Período'],
            ['id' => 'analisis_costos', 'nombre' => 'Análisis de Costos'],
            ['id' => 'compras_categoria', 'nombre' => 'Compras por Categoría'],
        ];

        $this->reportesTraslados = [
            ['id' => 'traslados_bodega', 'nombre' => 'Traslados por Bodega'],
            ['id' => 'traslados_periodo', 'nombre' => 'Traslados por Período'],
            ['id' => 'requisiciones_area', 'nombre' => 'Requisiciones por Área'],
            ['id' => 'devoluciones', 'nombre' => 'Reporte de Devoluciones'],
        ];

        $this->reportesInventario = [
            ['id' => 'kardex', 'nombre' => 'Kardex de Inventario'],
            ['id' => 'inventario_bodega', 'nombre' => 'Inventario por Bodega'],
            ['id' => 'tarjeta_responsabilidad', 'nombre' => 'Tarjeta de Responsabilidad'],
            ['id' => 'stock_minimo', 'nombre' => 'Productos con Stock Mínimo'],
        ];

        $this->reportesBitacora = [
            ['id' => 'actividad_usuario', 'nombre' => 'Actividad por Usuario'],
            ['id' => 'actividad_periodo', 'nombre' => 'Actividad por Período'],
            ['id' => 'cambios_inventario', 'nombre' => 'Cambios en Inventario'],
            ['id' => 'log_sistema', 'nombre' => 'Log del Sistema'],
        ];

        // Inicializar fechas por defecto (último mes)
        $this->fechaFin = date('Y-m-d');
        $this->fechaInicio = date('Y-m-d', strtotime('-1 month'));
    }

    /**
     * Carga los catálogos desde la base de datos
     */
    private function cargarCatalogos()
    {
        // Cargar usuarios
        $this->usuarios = Usuario::where('estado', true)
            ->orderBy('nombre_usuario')
            ->get()
            ->map(function ($usuario) {
                return [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre_usuario
                ];
            })
            ->toArray();

        // Cargar proveedores
        $this->proveedores = Proveedor::where('activo', true)
            ->orderBy('nombre')
            ->get()
            ->map(function ($proveedor) {
                return [
                    'id' => $proveedor->id,
                    'nombre' => $proveedor->nombre
                ];
            })
            ->toArray();

        // Cargar bodegas
        $this->bodegas = Bodega::where('activo', true)
            ->orderBy('nombre')
            ->get()
            ->map(function ($bodega) {
                return [
                    'id' => $bodega->id,
                    'nombre' => $bodega->nombre
                ];
            })
            ->toArray();

        // Cargar productos
        $this->productos = Producto::with('categoria')
            ->where('activo', true)
            ->orderBy('descripcion')
            ->get()
            ->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->descripcion,
                    'categoria' => $producto->categoria->nombre ?? 'Sin categoría'
                ];
            })
            ->toArray();
    }

    public function cambiarTab($tab)
    {
        $this->tabActivo = $tab;

        // Limpiar todos los filtros al cambiar de tab
        $this->tipoReporte = '';
        $this->datosKardex = [];
        $this->datosReporte = [];
        $this->tituloReporte = '';

        // Limpiar filtros seleccionados
        $this->selectedTipoReporteFiltro = null;
        $this->selectedProveedorFiltro = null;
        $this->selectedBodegaFiltro = null;
        $this->selectedProductoFiltro = null;
        $this->selectedUsuarioFiltro = null;

        // Limpiar búsquedas
        $this->searchTipoReporteFiltro = '';
        $this->searchProveedorFiltro = '';
        $this->searchBodegaFiltro = '';
        $this->searchProductoFiltro = '';
        $this->searchUsuarioFiltro = '';

        // Limpiar selecciones
        $this->proveedorSeleccionado = '';
        $this->bodegaSeleccionada = '';
        $this->productoSeleccionado = '';
        $this->usuarioSeleccionado = '';

        // Cerrar dropdowns
        $this->showTipoReporteDropdown = false;
        $this->showProveedorDropdown = false;
        $this->showBodegaDropdown = false;
        $this->showProductoDropdown = false;
        $this->showUsuarioDropdown = false;
    }

    public function generarReporte()
    {
        if (empty($this->tipoReporte)) {
            session()->flash('error', 'Debe seleccionar un tipo de reporte.');
            return;
        }

        // Limpiar datos previos
        $this->datosReporte = [];
        $this->datosKardex = [];

        // Generar reporte según el tipo
        switch ($this->tipoReporte) {
            // Reportes de Compras
            case 'compras_proveedor':
                $this->generarComprasPorProveedor();
                break;
            case 'compras_periodo':
                $this->generarComprasPorPeriodo();
                break;
            case 'analisis_costos':
                $this->generarAnalisisCostos();
                break;
            case 'compras_categoria':
                $this->generarComprasPorCategoria();
                break;

            // Reportes de Traslados
            case 'traslados_bodega':
                $this->generarTrasladosPorBodega();
                break;
            case 'traslados_periodo':
                $this->generarTrasladosPorPeriodo();
                break;
            case 'requisiciones_area':
                $this->generarRequisicionesPorArea();
                break;
            case 'devoluciones':
                $this->generarDevoluciones();
                break;

            // Reportes de Inventario
            case 'kardex':
                $this->generarKardex();
                break;
            case 'inventario_bodega':
                $this->generarInventarioPorBodega();
                break;
            case 'tarjeta_responsabilidad':
                $this->generarTarjetaResponsabilidad();
                break;
            case 'stock_minimo':
                $this->generarStockMinimo();
                break;

            // Reportes de Bitácora
            case 'actividad_usuario':
                $this->generarActividadPorUsuario();
                break;
            case 'actividad_periodo':
                $this->generarActividadPorPeriodo();
                break;
            case 'cambios_inventario':
                $this->generarCambiosInventario();
                break;
            case 'log_sistema':
                $this->generarLogSistema();
                break;

            default:
                session()->flash('error', 'Tipo de reporte no implementado.');
                break;
        }
    }

    /**
     * Genera el reporte Kardex de inventario
     */
    public function generarKardex()
    {
        try {
            $kardexService = new KardexService();

            // Preparar filtros
            $filtros = [];

            if (!empty($this->fechaInicio)) {
                $filtros['fecha_inicio'] = $this->fechaInicio;
            }

            if (!empty($this->fechaFin)) {
                $filtros['fecha_fin'] = $this->fechaFin;
            }

            if (!empty($this->bodegaSeleccionada)) {
                $filtros['id_bodega'] = $this->bodegaSeleccionada;
            }

            if (!empty($this->productoSeleccionado)) {
                $filtros['id_producto'] = $this->productoSeleccionado;
            }

            if (!empty($this->usuarioSeleccionado)) {
                $filtros['id_usuario'] = $this->usuarioSeleccionado;
            }

            // Generar el Kardex
            $movimientos = $kardexService->generarKardex($filtros);

            if ($movimientos->isEmpty()) {
                session()->flash('error', 'No se encontraron movimientos con los filtros seleccionados.');
                $this->datosKardex = [];
                return;
            }

            // Guardar datos para mostrar en la vista
            $this->datosKardex = $movimientos->toArray();

            session()->flash('message', 'Kardex generado exitosamente. Se encontraron ' . count($this->datosKardex) . ' movimientos.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el Kardex: ' . $e->getMessage());
            $this->datosKardex = [];
        }
    }

    public function exportarExcel()
    {
        session()->flash('message', 'Exportando a Excel...');
    }

    public function imprimir()
    {
        session()->flash('message', 'Preparando impresión...');
    }

    // Métodos de búsqueda para filtros - Proveedor
    public function getProveedorResultsProperty()
    {
        if (empty($this->searchProveedorFiltro)) {
            return array_slice($this->proveedores, 0, 10);
        }

        $filtered = array_filter($this->proveedores, function ($proveedor) {
            return stripos($proveedor['nombre'], $this->searchProveedorFiltro) !== false;
        });
        return array_slice($filtered, 0, 10);
    }

    public function selectProveedorFiltro($proveedorId)
    {
        $proveedor = collect($this->proveedores)->firstWhere('id', $proveedorId);
        if ($proveedor) {
            $this->selectedProveedorFiltro = $proveedor;
            $this->proveedorSeleccionado = $proveedorId;
            $this->searchProveedorFiltro = '';
            $this->showProveedorDropdown = false;
        }
    }

    public function clearProveedorFiltro()
    {
        $this->selectedProveedorFiltro = null;
        $this->proveedorSeleccionado = '';
        $this->searchProveedorFiltro = '';
        $this->showProveedorDropdown = false;
    }

    public function updatedSearchProveedorFiltro()
    {
        $this->showProveedorDropdown = true;
    }

    // Métodos de búsqueda para filtros - Bodega
    public function getBodegaResultsProperty()
    {
        if (empty($this->searchBodegaFiltro)) {
            return array_slice($this->bodegas, 0, 10);
        }

        $filtered = array_filter($this->bodegas, function ($bodega) {
            return stripos($bodega['nombre'], $this->searchBodegaFiltro) !== false;
        });
        return array_slice($filtered, 0, 10);
    }

    public function selectBodegaFiltro($bodegaId)
    {
        $bodega = collect($this->bodegas)->firstWhere('id', $bodegaId);
        if ($bodega) {
            $this->selectedBodegaFiltro = $bodega;
            $this->bodegaSeleccionada = $bodegaId;
            $this->searchBodegaFiltro = '';
            $this->showBodegaDropdown = false;
        }
    }

    public function clearBodegaFiltro()
    {
        $this->selectedBodegaFiltro = null;
        $this->bodegaSeleccionada = '';
        $this->searchBodegaFiltro = '';
        $this->showBodegaDropdown = false;
    }

    public function updatedSearchBodegaFiltro()
    {
        $this->showBodegaDropdown = true;
    }

    // Métodos de búsqueda para filtros - Producto
    public function getProductoResultsProperty()
    {
        if (empty($this->searchProductoFiltro)) {
            return array_slice($this->productos, 0, 10);
        }

        $filtered = array_filter($this->productos, function ($producto) {
            return stripos($producto['nombre'], $this->searchProductoFiltro) !== false;
        });
        return array_slice($filtered, 0, 10);
    }

    public function selectProductoFiltro($productoId)
    {
        $producto = collect($this->productos)->firstWhere('id', $productoId);
        if ($producto) {
            $this->selectedProductoFiltro = $producto;
            $this->productoSeleccionado = $productoId;
            $this->searchProductoFiltro = '';
            $this->showProductoDropdown = false;
        }
    }

    public function clearProductoFiltro()
    {
        $this->selectedProductoFiltro = null;
        $this->productoSeleccionado = '';
        $this->searchProductoFiltro = '';
        $this->showProductoDropdown = false;
    }

    public function updatedSearchProductoFiltro()
    {
        $this->showProductoDropdown = true;
    }

    // Métodos de búsqueda para filtros - Usuario
    public function getUsuarioResultsProperty()
    {
        if (empty($this->searchUsuarioFiltro)) {
            return array_slice($this->usuarios, 0, 10);
        }

        $filtered = array_filter($this->usuarios, function ($usuario) {
            return stripos($usuario['nombre'], $this->searchUsuarioFiltro) !== false;
        });
        return array_slice($filtered, 0, 10);
    }

    public function selectUsuarioFiltro($usuarioId)
    {
        $usuario = collect($this->usuarios)->firstWhere('id', $usuarioId);
        if ($usuario) {
            $this->selectedUsuarioFiltro = $usuario;
            $this->usuarioSeleccionado = $usuarioId;
            $this->searchUsuarioFiltro = '';
            $this->showUsuarioDropdown = false;
        }
    }

    public function clearUsuarioFiltro()
    {
        $this->selectedUsuarioFiltro = null;
        $this->usuarioSeleccionado = '';
        $this->searchUsuarioFiltro = '';
        $this->showUsuarioDropdown = false;
    }

    public function updatedSearchUsuarioFiltro()
    {
        $this->showUsuarioDropdown = true;
    }

    // Métodos de búsqueda para filtros - Tipo de Reporte
    public function getTipoReporteResultsProperty()
    {
        $reportes = [];
        
        // Obtener reportes según tab activo
        switch ($this->tabActivo) {
            case 'compras':
                $reportes = $this->reportesCompras;
                break;
            case 'traslados':
                $reportes = $this->reportesTraslados;
                break;
            case 'inventario':
                $reportes = $this->reportesInventario;
                break;
            case 'bitacora':
                $reportes = $this->reportesBitacora;
                break;
        }

        if (empty($this->searchTipoReporteFiltro)) {
            return array_slice($reportes, 0, 10);
        }

        $filtered = array_filter($reportes, function ($reporte) {
            return stripos($reporte['nombre'], $this->searchTipoReporteFiltro) !== false;
        });
        return array_slice($filtered, 0, 10);
    }

    public function selectTipoReporteFiltro($reporteId)
    {
        $reportes = $this->tipoReporteResults;
        $reporte = collect($reportes)->firstWhere('id', $reporteId);
        if ($reporte) {
            $this->selectedTipoReporteFiltro = $reporte;
            $this->tipoReporte = $reporteId;
            $this->searchTipoReporteFiltro = '';
            $this->showTipoReporteDropdown = false;
        }
    }

    public function clearTipoReporteFiltro()
    {
        $this->selectedTipoReporteFiltro = null;
        $this->tipoReporte = '';
        $this->searchTipoReporteFiltro = '';
        $this->showTipoReporteDropdown = false;
    }

    public function updatedSearchTipoReporteFiltro()
    {
        $this->showTipoReporteDropdown = true;
    }

    // ==================== REPORTES DE COMPRAS ====================

    private function generarComprasPorProveedor()
    {
        try {
            $query = Compra::with(['proveedor', 'detalles.producto.categoria'])
                ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin]);

            if ($this->proveedorSeleccionado) {
                $query->where('id_proveedor', $this->proveedorSeleccionado);
            }

            $compras = $query->get();

            if ($compras->isEmpty()) {
                session()->flash('error', 'No se encontraron compras con los filtros seleccionados.');
                return;
            }

            $this->datosReporte = $compras->map(function ($compra) {
                return [
                    'fecha' => $compra->fecha,
                    'numero_factura' => $compra->numero_factura,
                    'proveedor' => $compra->proveedor->nombre ?? 'N/A',
                    'total' => $compra->total,
                    'estado' => $compra->estado ? 'Activa' : 'Inactiva',
                ];
            })->toArray();

            $this->tituloReporte = 'Compras por Proveedor';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' compras.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    private function generarComprasPorPeriodo()
    {
        try {
            $compras = Compra::with(['proveedor'])
                ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])
                ->orderBy('fecha', 'desc')
                ->get();

            if ($compras->isEmpty()) {
                session()->flash('error', 'No se encontraron compras en el período seleccionado.');
                return;
            }

            $this->datosReporte = $compras->map(function ($compra) {
                return [
                    'fecha' => $compra->fecha,
                    'numero_factura' => $compra->numero_factura,
                    'proveedor' => $compra->proveedor->nombre ?? 'N/A',
                    'subtotal' => $compra->subtotal,
                    'total' => $compra->total,
                ];
            })->toArray();

            $this->tituloReporte = 'Compras por Período';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' compras.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    private function generarAnalisisCostos()
    {
        try {
            $compras = DetalleCompra::with(['compra.proveedor', 'producto'])
                ->whereHas('compra', function ($query) {
                    $query->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin]);
                })
                ->get()
                ->groupBy('id_producto');

            if ($compras->isEmpty()) {
                session()->flash('error', 'No se encontraron datos para análisis de costos.');
                return;
            }

            $this->datosReporte = $compras->map(function ($detalles, $productoId) {
                $producto = $detalles->first()->producto;
                $totalCantidad = $detalles->sum('cantidad');
                $totalCosto = $detalles->sum(function ($detalle) {
                    return $detalle->cantidad * $detalle->precio_unitario;
                });
                $costoPromedio = $totalCantidad > 0 ? $totalCosto / $totalCantidad : 0;

                return [
                    'producto' => $producto->descripcion ?? 'N/A',
                    'cantidad_total' => $totalCantidad,
                    'costo_total' => $totalCosto,
                    'costo_promedio' => $costoPromedio,
                ];
            })->values()->toArray();

            $this->tituloReporte = 'Análisis de Costos';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' productos.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    private function generarComprasPorCategoria()
    {
        try {
            $compras = DetalleCompra::with(['compra', 'producto.categoria'])
                ->whereHas('compra', function ($query) {
                    $query->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin]);
                })
                ->get()
                ->groupBy('producto.categoria.nombre');

            if ($compras->isEmpty()) {
                session()->flash('error', 'No se encontraron compras por categoría.');
                return;
            }

            $this->datosReporte = $compras->map(function ($detalles, $categoria) {
                $totalCantidad = $detalles->sum('cantidad');
                $totalCosto = $detalles->sum(function ($detalle) {
                    return $detalle->cantidad * $detalle->precio_unitario;
                });

                return [
                    'categoria' => $categoria ?: 'Sin categoría',
                    'cantidad_total' => $totalCantidad,
                    'costo_total' => $totalCosto,
                ];
            })->values()->toArray();

            $this->tituloReporte = 'Compras por Categoría';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' categorías.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    // ==================== REPORTES DE TRASLADOS ====================

    private function generarTrasladosPorBodega()
    {
        try {
            $query = Traslado::with(['bodegaOrigen', 'bodegaDestino'])
                ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin]);

            if ($this->bodegaSeleccionada) {
                $query->where(function ($q) {
                    $q->where('id_bodega_origen', $this->bodegaSeleccionada)
                      ->orWhere('id_bodega_destino', $this->bodegaSeleccionada);
                });
            }

            $traslados = $query->get();

            if ($traslados->isEmpty()) {
                session()->flash('error', 'No se encontraron traslados con los filtros seleccionados.');
                return;
            }

            $this->datosReporte = $traslados->map(function ($traslado) {
                return [
                    'fecha' => $traslado->fecha,
                    'numero' => $traslado->numero_traslado ?? 'N/A',
                    'bodega_origen' => $traslado->bodegaOrigen->nombre ?? 'N/A',
                    'bodega_destino' => $traslado->bodegaDestino->nombre ?? 'N/A',
                    'estado' => $traslado->estado ?? 'N/A',
                ];
            })->toArray();

            $this->tituloReporte = 'Traslados por Bodega';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' traslados.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    private function generarTrasladosPorPeriodo()
    {
        try {
            $traslados = Traslado::with(['bodegaOrigen', 'bodegaDestino'])
                ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])
                ->orderBy('fecha', 'desc')
                ->get();

            if ($traslados->isEmpty()) {
                session()->flash('error', 'No se encontraron traslados en el período seleccionado.');
                return;
            }

            $this->datosReporte = $traslados->map(function ($traslado) {
                return [
                    'fecha' => $traslado->fecha,
                    'numero' => $traslado->numero_traslado ?? 'N/A',
                    'bodega_origen' => $traslado->bodegaOrigen->nombre ?? 'N/A',
                    'bodega_destino' => $traslado->bodegaDestino->nombre ?? 'N/A',
                    'tipo' => $traslado->tipo ?? 'N/A',
                ];
            })->toArray();

            $this->tituloReporte = 'Traslados por Período';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' traslados.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    private function generarRequisicionesPorArea()
    {
        try {
            $requisiciones = Traslado::with(['bodegaOrigen', 'bodegaDestino'])
                ->where('tipo', 'requisicion')
                ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])
                ->orderBy('fecha', 'desc')
                ->get();

            if ($requisiciones->isEmpty()) {
                session()->flash('error', 'No se encontraron requisiciones en el período seleccionado.');
                return;
            }

            $this->datosReporte = $requisiciones->map(function ($requisicion) {
                return [
                    'fecha' => $requisicion->fecha,
                    'numero' => $requisicion->numero_traslado ?? 'N/A',
                    'area_solicitante' => $requisicion->bodegaDestino->nombre ?? 'N/A',
                    'estado' => $requisicion->estado ?? 'N/A',
                ];
            })->toArray();

            $this->tituloReporte = 'Requisiciones por Área';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' requisiciones.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    private function generarDevoluciones()
    {
        try {
            $devoluciones = Traslado::with(['bodegaOrigen', 'bodegaDestino'])
                ->where('tipo', 'devolucion')
                ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])
                ->orderBy('fecha', 'desc')
                ->get();

            if ($devoluciones->isEmpty()) {
                session()->flash('error', 'No se encontraron devoluciones en el período seleccionado.');
                return;
            }

            $this->datosReporte = $devoluciones->map(function ($devolucion) {
                return [
                    'fecha' => $devolucion->fecha,
                    'numero' => $devolucion->numero_traslado ?? 'N/A',
                    'bodega_origen' => $devolucion->bodegaOrigen->nombre ?? 'N/A',
                    'bodega_destino' => $devolucion->bodegaDestino->nombre ?? 'N/A',
                    'estado' => $devolucion->estado ?? 'N/A',
                ];
            })->toArray();

            $this->tituloReporte = 'Reporte de Devoluciones';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' devoluciones.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    // ==================== REPORTES DE INVENTARIO ====================

    private function generarInventarioPorBodega()
    {
        try {
            $query = DB::table('inventario as i')
                ->join('productos as p', 'i.id_producto', '=', 'p.id')
                ->join('bodegas as b', 'i.id_bodega', '=', 'b.id')
                ->leftJoin('categorias as c', 'p.id_categoria', '=', 'c.id')
                ->select(
                    'b.nombre as bodega',
                    'p.descripcion as producto',
                    'c.nombre as categoria',
                    'i.cantidad',
                    'i.costo_promedio'
                )
                ->where('i.cantidad', '>', 0);

            if ($this->bodegaSeleccionada) {
                $query->where('i.id_bodega', $this->bodegaSeleccionada);
            }

            $inventario = $query->get();

            if ($inventario->isEmpty()) {
                session()->flash('error', 'No se encontró inventario con los filtros seleccionados.');
                return;
            }

            $this->datosReporte = $inventario->map(function ($item) {
                return [
                    'bodega' => $item->bodega,
                    'producto' => $item->producto,
                    'categoria' => $item->categoria ?? 'Sin categoría',
                    'cantidad' => $item->cantidad,
                    'costo_promedio' => $item->costo_promedio,
                    'valor_total' => $item->cantidad * $item->costo_promedio,
                ];
            })->toArray();

            $this->tituloReporte = 'Inventario por Bodega';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' productos.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    private function generarTarjetaResponsabilidad()
    {
        try {
            $query = DB::table('inventario as i')
                ->join('productos as p', 'i.id_producto', '=', 'p.id')
                ->join('bodegas as b', 'i.id_bodega', '=', 'b.id')
                ->select(
                    'b.nombre as bodega',
                    'b.responsable',
                    'p.descripcion as producto',
                    'i.cantidad',
                    'i.costo_promedio'
                )
                ->where('i.cantidad', '>', 0);

            if ($this->bodegaSeleccionada) {
                $query->where('i.id_bodega', $this->bodegaSeleccionada);
            }

            $inventario = $query->get();

            if ($inventario->isEmpty()) {
                session()->flash('error', 'No se encontró inventario con los filtros seleccionados.');
                return;
            }

            $this->datosReporte = $inventario->map(function ($item) {
                return [
                    'bodega' => $item->bodega,
                    'responsable' => $item->responsable ?? 'N/A',
                    'producto' => $item->producto,
                    'cantidad' => $item->cantidad,
                    'valor_unitario' => $item->costo_promedio,
                    'valor_total' => $item->cantidad * $item->costo_promedio,
                ];
            })->toArray();

            $this->tituloReporte = 'Tarjeta de Responsabilidad';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' productos.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    private function generarStockMinimo()
    {
        try {
            $productos = DB::table('inventario as i')
                ->join('productos as p', 'i.id_producto', '=', 'p.id')
                ->join('bodegas as b', 'i.id_bodega', '=', 'b.id')
                ->select(
                    'p.descripcion as producto',
                    'b.nombre as bodega',
                    'i.cantidad',
                    'p.stock_minimo',
                    'i.costo_promedio'
                )
                ->whereRaw('i.cantidad <= p.stock_minimo')
                ->where('p.activo', true);

            if ($this->bodegaSeleccionada) {
                $productos->where('i.id_bodega', $this->bodegaSeleccionada);
            }

            $resultado = $productos->get();

            if ($resultado->isEmpty()) {
                session()->flash('error', 'No se encontraron productos con stock mínimo.');
                return;
            }

            $this->datosReporte = $resultado->map(function ($item) {
                return [
                    'producto' => $item->producto,
                    'bodega' => $item->bodega,
                    'cantidad_actual' => $item->cantidad,
                    'stock_minimo' => $item->stock_minimo,
                    'diferencia' => $item->stock_minimo - $item->cantidad,
                ];
            })->toArray();

            $this->tituloReporte = 'Productos con Stock Mínimo';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' productos.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    // ==================== REPORTES DE BITÁCORA ====================

    private function generarActividadPorUsuario()
    {
        try {
            $query = Bitacora::with(['usuario'])
                ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin]);

            if ($this->usuarioSeleccionado) {
                $query->where('id_usuario', $this->usuarioSeleccionado);
            }

            $actividades = $query->orderBy('fecha', 'desc')->get();

            if ($actividades->isEmpty()) {
                session()->flash('error', 'No se encontraron actividades con los filtros seleccionados.');
                return;
            }

            $this->datosReporte = $actividades->map(function ($actividad) {
                return [
                    'fecha' => $actividad->fecha,
                    'usuario' => $actividad->usuario->nombre_usuario ?? 'N/A',
                    'accion' => $actividad->accion,
                    'modulo' => $actividad->modulo ?? 'N/A',
                    'descripcion' => $actividad->descripcion ?? '',
                ];
            })->toArray();

            $this->tituloReporte = 'Actividad por Usuario';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' actividades.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    private function generarActividadPorPeriodo()
    {
        try {
            $actividades = Bitacora::with(['usuario'])
                ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])
                ->orderBy('fecha', 'desc')
                ->get();

            if ($actividades->isEmpty()) {
                session()->flash('error', 'No se encontraron actividades en el período seleccionado.');
                return;
            }

            $this->datosReporte = $actividades->map(function ($actividad) {
                return [
                    'fecha' => $actividad->fecha,
                    'usuario' => $actividad->usuario->nombre_usuario ?? 'N/A',
                    'accion' => $actividad->accion,
                    'modulo' => $actividad->modulo ?? 'N/A',
                ];
            })->toArray();

            $this->tituloReporte = 'Actividad por Período';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' actividades.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    private function generarCambiosInventario()
    {
        try {
            $cambios = Bitacora::with(['usuario'])
                ->where('modulo', 'inventario')
                ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])
                ->orderBy('fecha', 'desc')
                ->get();

            if ($cambios->isEmpty()) {
                session()->flash('error', 'No se encontraron cambios en inventario en el período seleccionado.');
                return;
            }

            $this->datosReporte = $cambios->map(function ($cambio) {
                return [
                    'fecha' => $cambio->fecha,
                    'usuario' => $cambio->usuario->nombre_usuario ?? 'N/A',
                    'accion' => $cambio->accion,
                    'descripcion' => $cambio->descripcion ?? '',
                ];
            })->toArray();

            $this->tituloReporte = 'Cambios en Inventario';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' cambios.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    private function generarLogSistema()
    {
        try {
            $logs = Bitacora::with(['usuario'])
                ->whereBetween('fecha', [$this->fechaInicio, $this->fechaFin])
                ->orderBy('fecha', 'desc')
                ->get();

            if ($logs->isEmpty()) {
                session()->flash('error', 'No se encontraron logs del sistema en el período seleccionado.');
                return;
            }

            $this->datosReporte = $logs->map(function ($log) {
                return [
                    'fecha' => $log->fecha,
                    'usuario' => $log->usuario->nombre_usuario ?? 'Sistema',
                    'modulo' => $log->modulo ?? 'N/A',
                    'accion' => $log->accion,
                    'ip' => $log->ip_address ?? 'N/A',
                ];
            })->toArray();

            $this->tituloReporte = 'Log del Sistema';
            session()->flash('message', 'Reporte generado exitosamente. Total: ' . count($this->datosReporte) . ' registros.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.generar-reportes');
    }
}
