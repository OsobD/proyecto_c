<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * @class GenerarReportes
 * @package App\Livewire
 * @brief Componente para la generación y visualización de reportes del sistema.
 *
 * Este componente permite a los usuarios seleccionar diferentes tipos de reportes,
 * organizados en pestañas (Compras, Traslados, Inventario, Bitácora).
 * Proporciona filtros por fechas, usuarios, proveedores y bodegas para
 * personalizar la información a generar.
 */
class GenerarReportes extends Component
{
    // --- PROPIEDADES PÚBLICAS ---

    /** @var string Pestaña activa ('compras', 'traslados', 'inventario', 'bitacora'). */
    public $tabActivo = 'compras';
    /** @var string ID del tipo de reporte seleccionado. */
    public $tipoReporte = '';
    /** @var string Fecha de inicio para el filtro del reporte. */
    public $fechaInicio = '';
    /** @var string Fecha de fin para el filtro del reporte. */
    public $fechaFin = '';

    // --- FILTROS ESPECÍFICOS ---

    /** @var string|int ID del usuario seleccionado para el filtro. */
    public $usuarioSeleccionado = '';
    /** @var string|int ID del proveedor seleccionado para el filtro. */
    public $proveedorSeleccionado = '';
    /** @var string|int ID de la bodega seleccionada para el filtro. */
    public $bodegaSeleccionada = '';

    /** @var array Lista de usuarios para los filtros. */
    public $usuarios = [];
    /** @var array Lista de proveedores para los filtros. */
    public $proveedores = [];
    /** @var array Lista de bodegas para los filtros. */
    public $bodegas = [];

    // --- LISTAS DE REPORTES POR CATEGORÍA ---

    /** @var array Lista de reportes disponibles en la pestaña de Compras. */
    public $reportesCompras = [];
    /** @var array Lista de reportes disponibles en la pestaña de Traslados. */
    public $reportesTraslados = [];
    /** @var array Lista de reportes disponibles en la pestaña de Inventario. */
    public $reportesInventario = [];
    /** @var array Lista de reportes disponibles en la pestaña de Bitácora. */
    public $reportesBitacora = [];

    // --- MÉTODOS DE CICLO DE VIDA ---

    /**
     * @brief Método que se ejecuta al inicializar el componente.
     * Carga datos de ejemplo para filtros y listas de reportes.
     * @return void
     */
    public function mount()
    {
        // Datos simulados para los filtros
        $this->usuarios = [
            ['id' => 1, 'nombre' => 'Juan Pérez'], ['id' => 2, 'nombre' => 'María García'],
            ['id' => 3, 'nombre' => 'Carlos López'], ['id' => 4, 'nombre' => 'David Bautista'],
        ];
        $this->proveedores = [
            ['id' => 1, 'nombre' => 'Ferretería El Martillo Feliz'],
            ['id' => 2, 'nombre' => 'Suministros Industriales S.A.'],
            ['id' => 3, 'nombre' => 'Distribuidora García'],
        ];
        $this->bodegas = [
            ['id' => 1, 'nombre' => 'Bodega Central'], ['id' => 2, 'nombre' => 'Bodega Norte'],
            ['id' => 3, 'nombre' => 'Bodega Sur'],
        ];

        // Listas de reportes disponibles
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
            ['id' => 'inventario_bodega', 'nombre' => 'Inventario por Bodega'],
            ['id' => 'tarjeta_responsabilidad', 'nombre' => 'Tarjeta de Responsabilidad'],
            ['id' => 'movimientos_producto', 'nombre' => 'Movimientos de Producto'],
            ['id' => 'stock_minimo', 'nombre' => 'Productos con Stock Mínimo'],
        ];
        $this->reportesBitacora = [
            ['id' => 'actividad_usuario', 'nombre' => 'Actividad por Usuario'],
            ['id' => 'actividad_periodo', 'nombre' => 'Actividad por Período'],
            ['id' => 'cambios_inventario', 'nombre' => 'Cambios en Inventario'],
            ['id' => 'log_sistema', 'nombre' => 'Log del Sistema'],
        ];
    }

    // --- MÉTODOS DE INTERACCIÓN ---

    /**
     * @brief Cambia la pestaña de reportes activa y reinicia la selección de reporte.
     * @param string $tab El identificador de la nueva pestaña.
     * @return void
     */
    public function cambiarTab($tab)
    {
        $this->tabActivo = $tab;
        $this->tipoReporte = '';
    }

    /**
     * @brief Simula la generación de un reporte.
     * Muestra un mensaje de éxito o error.
     * @return void
     */
    public function generarReporte()
    {
        if (empty($this->tipoReporte)) {
            session()->flash('error', 'Debe seleccionar un tipo de reporte.');
            return;
        }
        session()->flash('message', 'Generando reporte: ' . $this->tipoReporte);
    }

    /**
     * @brief Simula la exportación del reporte a un archivo Excel.
     * @return void
     */
    public function exportarExcel()
    {
        session()->flash('message', 'Exportando a Excel...');
    }

    /**
     * @brief Simula la preparación de la impresión del reporte.
     * @return void
     */
    public function imprimir()
    {
        session()->flash('message', 'Preparando impresión...');
    }

    /**
     * @brief Renderiza la vista del componente.
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.generar-reportes');
    }
}
