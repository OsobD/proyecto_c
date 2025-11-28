<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\KardexService;
use App\Models\Usuario;
use App\Models\Proveedor;
use App\Models\Bodega;
use App\Models\Producto;

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
        $this->tipoReporte = '';
        $this->datosKardex = [];
    }

    public function generarReporte()
    {
        if (empty($this->tipoReporte)) {
            session()->flash('error', 'Debe seleccionar un tipo de reporte.');
            return;
        }

        // Generar reporte según el tipo
        switch ($this->tipoReporte) {
            case 'kardex':
                $this->generarKardex();
                break;
            default:
                session()->flash('message', 'Generando reporte: ' . $this->tipoReporte . ' (En desarrollo)');
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

    public function render()
    {
        return view('livewire.generar-reportes');
    }
}
